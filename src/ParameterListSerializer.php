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
use NoreSources\Http\RFC7230;
use NoreSources\KeyValueParameterMapInterface;
use NoreSources\ModifiableKeyValueParameterMapInterface;

class ParameterListSerializer
{

	/**
	 *
	 * Serialize parameter map to RFC7230 parameter list string
	 *
	 * @param KeyValueParameterMapInterface $parameters
	 * @return string
	 */
	public function serializeToString(KeyValueParameterMapInterface $parameters)
	{
		$iterator = null;
		if ($parameters instanceof ModifiableKeyValueParameterMapInterface)
			$iterator = $parameters->getParameterIterator();
		else
			$iterator = $parameters->getParameters();

		return Container::implode($iterator, '; ',
			function ($name, $value) {
				$token = preg_match(chr(1) . '^' . RFC7230::TOKEN_PATTERN . '$' . chr(1), $value);
				if ($token)
					return $name . '=' . $value;
				// Quoted string
				/**
				 *
				 * @todo escape string
				 */
				return $name . '="' . $value . '"';
			});
	}

	const ACCEPT = 1;

	const IGNORE = 0;

	const ABORT = -1;

	/**
	 *
	 * @param string $text
	 * @param callable $acceptCallable
	 *        	A callback invoked each time a parameter and its value is found .in @c $text
	 *        	The callable should accept 2 arguments (name, value) and return an integer as
	 *        	follow
	 *        	<ul>
	 *        	<li>&gt 1 if the parameter is accepted</li>
	 *        	<li>0 if the marameter should be ignored</li>
	 *        	<li>&lt 0 if the parsing must be aborted</li>
	 *        	</ul>
	 * @return number The number of bytes consumed
	 */
	public function unserializeFromString($parameters, $text, $acceptCallable = null,
		$assignCallable = null)
	{
		if (!\is_callable($assignCallable))
		{
			$assignCallable = [
				Container::class,
				'setValue'
			];
		}

		$consumed = 0;
		$length = \strlen($text);

		while ($length &&
			\preg_match(
				chr(1) . RFC7230::OWS_PATTERN . RFC7230::PARAMETER_PATTERN . RFC7230::OWS_PATTERN .
				chr(1), $text, $groups))
		{
			$c = \strlen($groups[0]);
			$name = $groups[1];
			$value = Container::keyValue($groups, 3, Container::keyValue($groups, 2, ''));

			$accepted = 1;
			if (\is_callable($acceptCallable))
			{
				$accepted = \call_user_func($acceptCallable, $name, $value);
				if ($accepted <= self::ABORT)
					break;
			}

			if ($accepted >= self::ACCEPT)
				\call_user_func($assignCallable, $parameters, $name, $value);

			$c = strlen($groups[0]);
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









