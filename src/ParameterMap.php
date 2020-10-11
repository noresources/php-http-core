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

use Psr\Container\ContainerInterface;
use NoreSources\ArrayRepresentation;
use NoreSources\Container;
use NoreSources\CaseInsensitiveKeyMapTrait;

/**
 * Case-insensitive key-value parameter map.
 *
 * According to RFC 7231 section 3.1.1.1, parameter names asre case insensitive.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-3.1.1.1
 * @see https://tools.ietf.org/html/rfc4288#section-4.3
 */
class ParameterMap implements ParameterMapInterface, ContainerInterface
{

	use CaseInsensitiveKeyMapTrait;

	protected function onKeyNotFound($key)
	{
		throw new ParameterNotFoundException($key);
	}
}