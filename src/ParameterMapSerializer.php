<?php
/**
 * Copyright © 2012 - 2020 by Renaud Guillard (dev@nore.fr)
 * Distributed under the terms of the MIT License, see LICENSE
 */

/**
 *
 * @package Http
 */
namespace NoreSources\Http;

use NoreSources\Container;

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
				if (\preg_match(chr(1) . '^' . RFC7230::TOKEN_PATTERN . '$' . chr(1), $value))
					$s .= $value;
				else
				{
					$replacement = '\\\\${1}';
					$s .= '"' .
					\preg_replace(chr(1) . '([^' . RFC7230::QUOTED_TEXT_RANGE . '])' . chr(1),
						$replacement, $value) . '"';
				}

				return $s;
			});
	}

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
	 * @param \Closure $acceptCallable
	 *        	A callback invoked each time a parameter and its value is found .in @c $text
	 *        	The callable should accept 2 arguments (name, value) and return an integer as
	 *        	follow
	 *        	<ul>
	 *        	<li>&gt 1 if the parameter is accepted</li>
	 *        	<li>0 if the marameter should be ignored</li>
	 *        	<li>&lt 0 if the parsing must be aborted</li>
	 *        	</ul>
	 * @return integer number of bytes of $text consumed
	 */
	public static function unserializeParameters(&$parameters, $text, $acceptCallable = null)
	{
		$consumed = 0;
		$length = \strlen($text);

		while ($length &&
			\preg_match(
				chr(1) . '^' . RFC7230::OWS_PATTERN . RFC7230::PARAMETER_PATTERN .
				RFC7230::OWS_PATTERN . chr(1), $text, $groups))
		{
			$c = \strlen($groups[0]);
			$name = $groups[1];
			if (Container::keyExists($groups, 3))
				$value = \stripslashes(Container::keyValue($groups, 3));
			else
				$value = Container::keyValue($groups, 2, '');

			$accepted = 1;
			if (\is_callable($acceptCallable))
			{
				$accepted = \call_user_func($acceptCallable, $name, $value);
				if ($accepted <= self::ABORT)
					break;
			}

			if ($accepted >= self::ACCEPT)
				Container::setValue($parameters, $name, $value);

			$c = \strlen($groups[0]);
			$text = \substr($text, $c);
			$consumed += $c;

			if (\substr($text, 0, 1) != ';')
				break;

			$consumed++;
			$text = \substr($text, 1);
			$length = \strlen($text);
		}

		return $consumed;
	}
}