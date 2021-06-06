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
use NoreSources\ArrayRepresentation;
use Psr\Container\NotFoundExceptionInterface;

class ParameterNotFoundException extends \Exception implements
	NotFoundExceptionInterface
{

	public function __construct($name)
	{
		$message = $name . ' parameter not found';
		parent::__construct($message);
	}
}