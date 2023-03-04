<?php
/**
 * Copyright © 2012 - 2021 by Renaud Guillard (dev@nore.fr)
 * Distributed under the terms of the MIT License, see LICENSE
 */

/**
 *
 * @package Http
 */
namespace NoreSources\Http;

use NoreSources\Container\Container;

class ParameterMapSerializer
{

	/*
	 *
	 * Serialize parameter map to a string
	 *
	 * @param \Traversable $parameters Input parameter array
	 * @param string $glue Parameters separator
	 * @return string
	 */
	public static function serializeParameters($parameters, $glue = '; ')
	{
		return Container::implode($parameters, $glue,
			function ($name, $value) {
				$s = $name . '=';
				if (\preg_match(
					chr(1) . '^' . RFC7230::TOKEN_PATTERN . '$' . chr(1),
					$value))
					$s .= $value;
				else
				{
					$replacement = '\\\\${1}';
					$s .= '"' .
					\preg_replace(
						chr(1) . '([^' . RFC7230::QUOTED_TEXT_RANGE .
						'])' . chr(1), $replacement, $value) . '"';
				}

				return $s;
			});
	}

	const OPTION_DELIMITER = 'delimiter';

	/**
	 * ParameterMap unserialization option.
	 *
	 * A PCRE pattern that MUST contains at least a group for parameter named
	 * and a group for the value.
	 *
	 * Name and value groups are defined by the
	 * OPTION_NAME_PATTERN_GROUPS and OPTION_VALUE_PATTERN_GROUPS
	 */
	const OPTION_PATTERN = 'pattern';

	const OPTION_NAME_PATTERN_GROUPS = 'name-patterngroups';

	/**
	 * ParameterMap unserialization option.
	 *
	 * Array of pattern group indexes where the parameter value
	 * could be.
	 *
	 * If the array entry is a {int, callable} pair, the callable will be applied on the
	 * pattern group value.
	 */
	const OPTION_VALUE_PATTERN_GROUPS = 'value-patterngroups';

	/**
	 * ParameterMap unserialization option.
	 *
	 * Whitespace pattern
	 */
	const OPTION_WHITESPACE_PATTERN = 'whitespace';

	/**
	 * ParameterMap unserialization option.
	 *
	 * A callback invoked each time a parameter and its value is found .in @c $text
	 * The callable should accept 2 arguments (name, value) and return an integer as
	 * follow
	 * <ul>
	 * <li>&gt 1 if the parameter is accepted</li>
	 * <li>0 if the marameter should be ignored</li>
	 * <li>&lt 0 if the parsing must be aborted</li>
	 * </ul>
	 */
	const OPTION_ACCEPT_CALLBACK = 'accept';

	/**
	 * Parameter map unserialization acceptance function result.
	 *
	 * Indicates the parameter key/value pair can be added to the parameter map
	 *
	 * @var integer
	 */
	const ACCEPT = 1;

	/**
	 * Parameter map unserialization acceptance function result.
	 *
	 * Indicates the current key/value pair must be ignored.
	 *
	 * @var integer
	 */
	const IGNORE = 0;

	/**
	 * Parameter map unserialization acceptance function result.
	 *
	 * Indicates the current key/value pair must be ignored and the
	 * parsing process mustbe aborted.
	 *
	 * @var unknown
	 */
	const ABORT = -1;

	/**
	 *
	 * @param \ArrayAccess|array $parameters
	 * @param string $text
	 * @param array $options
	 *
	 * @return integer number of bytes of $text consumed
	 */
	public static function unserializeParameters(&$parameters, $text,
		$options = array())
	{
		if (\is_callable($options)) // Legacy compat
		{
			$options = [
				self::OPTION_ACCEPT_CALLBACK => $options
			];
		}

		$ws = Container::keyValue($options,
			self::OPTION_WHITESPACE_PATTERN, RFC7230::OWS_PATTERN);
		$delimiter = Container::keyValue($options,
			self::OPTION_DELIMITER, ';');
		$pattern = Container::keyValue($options, self::OPTION_PATTERN,
			RFC7230::PARAMETER_PATTERN);
		$namePatternGroups = Container::keyValue($options,
			self::OPTION_NAME_PATTERN_GROUPS, 1);
		$valuePatternGroups = Container::keyValue($options,
			self::OPTION_VALUE_PATTERN_GROUPS,
			[
				3 => '\stripslashes',
				2
			]);
		$acceptCallable = Container::keyValue($options,
			self::OPTION_ACCEPT_CALLBACK);

		if (!\is_array($namePatternGroups))
			$namePatternGroups = [
				$namePatternGroups
			];
		if (!\is_array($valuePatternGroups))
			$valuePatternGroups = [
				$valuePatternGroups
			];

		$consumed = 0;
		$length = \strlen($text);

		while ($length &&
			\preg_match(chr(1) . '^' . $ws . $pattern . $ws . chr(1),
				$text, $groups))
		{
			$c = \strlen($groups[0]);
			$name = '';
			$value = '';

			foreach ($namePatternGroups as $g)
			{
				if (Container::keyExists($groups, $g) &&
					($v = $groups[$g]) && \strlen($v))
				{
					$name = $v;
					break;
				}
			}

			foreach ($valuePatternGroups as $g => $process)
			{
				if (!\is_callable($process))
					$g = $process;
				if (Container::keyExists($groups, $g) &&
					($v = $groups[$g]) && \strlen($v))
				{
					$value = $v;
					if (\is_callable($process))
						$value = \call_user_func($process, $value);
					break;
				}
			}

			$accepted = 1;
			if (\is_callable($acceptCallable))
			{
				$accepted = \call_user_func($acceptCallable, $name,
					$value);
				if ($accepted <= self::ABORT)
					break;
			}

			if ($accepted >= self::ACCEPT)
				Container::setValue($parameters, $name, $value);

			$c = \strlen($groups[0]);
			$text = \substr($text, $c);
			$consumed += $c;

			if (\substr($text, 0, 1) != $delimiter)
				break;

			$consumed++;
			$text = \substr($text, 1);
			$length = \strlen($text);
		}

		return $consumed;
	}
}