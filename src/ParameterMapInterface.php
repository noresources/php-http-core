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

/**
 * Key-value map for several HTTP message header value.
 *
 * <ul>
 * <li>Key must be a token string as described in RFC 7231</li>
 * <li>Value must be a token or quoted-value string as described in RRC 7231</li>
 * <li>Keys are case-insensitive</li>
 * <li>offsetGet($key) MUST return NULL when parameter cannot be found</li>
 * <li>offsetSet($key, $value) MUST throw InvalidArgumentException if the key is not a string</li>
 * <li>offsetSet($key, $value) MAY validate $key and $value and throw
 * InvalidArgumentException</li>
 * <li>get() MUST throw ParameterNotFoundException on non-existing key</li>
 * </ul>
 */
interface ParameterMapInterface extends \ArrayAccess, \Countable,
	\IteratorAggregate, ContainerInterface
{
}