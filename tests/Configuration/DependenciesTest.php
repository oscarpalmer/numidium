<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Configuration;

use League\Container\Container;
use oscarpalmer\Numidium\Routing\Items\Dependencies;
use PHPUnit\Framework\TestCase;

final class DependenciesTest extends TestCase
{
	public function test(): void
	{
		$dependencies = new Dependencies(new Container());

		$this->assertInstanceOf(Dependencies::class, $dependencies->array('array', [1, 2, 3, 4]));
		$this->assertInstanceOf(Dependencies::class, $dependencies->boolean('boolean', true));
		$this->assertInstanceOf(Dependencies::class, $dependencies->callable('callable', function () {}));
		$this->assertInstanceOf(Dependencies::class, $dependencies->class(Dependencies::class, 'object'));
		$this->assertInstanceOf(Dependencies::class, $dependencies->float('float', 12.34));
		$this->assertInstanceOf(Dependencies::class, $dependencies->integer('integer', 1234));
		$this->assertInstanceOf(Dependencies::class, $dependencies->number('number_1', 12.34));
		$this->assertInstanceOf(Dependencies::class, $dependencies->number('number_2', 1234));
		$this->assertInstanceOf(Dependencies::class, $dependencies->object('object', new Container()));
		$this->assertInstanceOf(Dependencies::class, $dependencies->string('number_1', 'Test'));
	}
}
