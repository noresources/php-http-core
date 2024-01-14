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

use Psr\Container\ContainerInterface;
use NoreSources\Container;
use NoreSources\Container\CaseInsensitiveKeyMapTrait;
use NoreSources\Container\ArrayAccessContainerInterfaceTrait;
use NoreSources\Type\TypeDescription;

/**
 * Case-insensitive key-value parameter map.
 *
 * According to RFC 7231 section 3.1.1.1, parameter names asre case insensitive.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-3.1.1.1
 * @see https://tools.ietf.org/html/rfc4288#section-4.3
 */
class ParameterMap implements ParameterMapInterface
{

	use CaseInsensitiveKeyMapTrait;
	use ArrayAccessContainerInterfaceTrait;

	/**
	 *
	 * {@inheritdoc}
	 * @see ArrayAccess::offsetGet()
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($name)
	{
		if (!$this->offsetExists($name))
			return NULL;
		return $this->caselessOffsetGet($name);
	}

	/**
	 *
	 * {@inheritdoc}
	 * @see ArrayAccess::offsetSet()
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($name, $value)
	{
		if (!\is_string($name))
			throw new \InvalidArgumentException(
				'Invalid parameter name. String expected, got ' .
				TypeDescription::getName($name));
		$this->caselessOffsetSet($name, $value);
	}

	protected function newNotFoundException($key)
	{
		throw new ParameterNotFoundException($key);
	}
}