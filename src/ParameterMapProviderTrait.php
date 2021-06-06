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

/**
 * Reference implementation of ParameterMapProviderInterface
 *
 * @author renaud
 *
 */
trait ParameterMapProviderTrait
{

	/**
	 *
	 * @return ParameterMapInterface
	 */
	function getParameters()
	{
		if (!($this->parameters instanceof ParameterMap))
			$this->parameters = new ParameterMap();
		return $this->parameters;
	}

	/**
	 *
	 * @var ParameterMap
	 */
	private $parameters;
}