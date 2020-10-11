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

final class ParameterMapTest extends \PHPUnit\Framework\TestCase
{

	public function testUnset()
	{
		$caseSensitive = [
			'key' => 'value',
			'Foo' => 'Bar'
		];

		$m = new ParameterMap($caseSensitive);
		$this->assertCount(2, $m);
		$m->offsetUnset('foo');
		$this->assertCount(1, $m);
		list ($k, $v) = Container::first($m);
		$this->assertEquals('value', $v);
	}

	public function testException()
	{
		$m = new ParameterMap([
			'bar' => 'baz'
		]);

		$this->assertNull($m->offsetGet('wtf'));

		$this->expectException(ParameterNotFoundException::class);
		$m->get('WTF');
	}

	public function testCaseInsensitive()
	{
		$caseSensitive = [
			'key' => 'value',
			'Foo' => 'Bar'
		];

		$m = new ParameterMap($caseSensitive);

		$this->assertCount(2, $m, 'count');
		$this->assertEquals($caseSensitive, $m->getArrayCopy(),
			'Keep case internally');

		$this->assertTrue($m->has('key'), 'has / offsetExists');
		$this->assertTrue($m->has('Foo'), 'has / offsetExists');
		$this->assertFalse($m->has('bar'), 'has / offsetExists');

		$this->assertEquals('value', $m->get('key'), 'get (strict case)');
		$this->assertEquals('Bar', $m->get('foo'), 'get (lowercase)');
		$this->assertEquals('Bar', $m->offsetGet('foo'),
			'offsetGet (lowercase)');
		$this->assertEquals('Bar', $m['foo'], 'operator [] (lowercase)');

		$m['CaSe'] = 'Insensitive';
		$this->assertCount(3, $m, 'Add key');

		$this->assertTrue($m->offsetExists('case'));
		$this->assertTrue($m->offsetExists('cAsE'));
		$this->assertEquals('Insensitive', $m['caSE']);
	}
}
