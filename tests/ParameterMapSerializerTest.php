<?php
/**
 * Copyright © 2012 - 2020 by Renaud Guillard (dev@nore.fr)
 * Distributed under the terms of the MIT License, see LICENSE
 */

/**
 *
 * @package HTTP
 */
namespace NoreSources\Http;

use NoreSources\Container;

final class ParameterMapSerializerTest extends \PHPUnit\Framework\TestCase
{

	public function testUnserializeParameters()
	{
		$tests = [
			'basic' => [
				'text' => 'key=value',
				'expected' => [
					'key' => 'value'
				]
			],
			'whitespaces before' => [
				'text' => '   key=value',
				'expected' => [
					'key' => 'value'
				],
				'serialized' => 'key=value'
			],
			'whitespaces after' => [
				'text' => 'key=value;  ',
				'expected' => [
					'key' => 'value'
				],
				'consumed' => \strlen('key=value;'),
				'serialized' => 'key=value'
			],
			'multiple basic' => [
				'text' => 'key=value; Foo=bar; valid-token#name=content',
				'expected' => [
					'key' => 'value',
					'Foo' => 'bar',
					'valid-token#name' => 'content'
				]
			],
			'invalid' => [
				'text' => 'k e y=value',
				'expected' => [],
				'consumed' => 0,
				'serialized' => ''
			],
			'quoted-value' => [
				'text' => 'key="Some value"',
				'expected' => [
					'key' => 'Some value'
				]
			],
			'quoted-value with quoted pair' => [
				'text' => 'key="A \"quoted\" text"',
				'expected' => [
					'key' => 'A "quoted" text'
				]
			],
			'multiple quoted-value with quoted pair' => [
				'text' => 'delimiter=";"; key="A \"quoted\" text"',
				'expected' => [
					'delimiter' => ';',
					'key' => 'A "quoted" text'
				]
			]
		];

		foreach ($tests as $label => $test)
		{
			$text = $test['text'];
			$expected = $test['expected'];
			$consumed = Container::keyValue($test, 'consumed', \strlen($text));
			$serialized = Container::keyValue($test, 'serialized', $text);

			$parameters = [];
			$c = ParameterMapSerializer::unserializeParameters($parameters, $text);

			$this->assertEquals($expected, $parameters, $label . ' parsed values');
			$this->assertEquals($consumed, $c, $label . ' consumed bytes');

			$s = ParameterMapSerializer::serializeParameters($parameters);
			$this->assertEquals($serialized, $s, $label . ' serialized');
		}
	}
}
