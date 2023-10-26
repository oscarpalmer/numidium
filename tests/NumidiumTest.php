<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test;

use LogicException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use oscarpalmer\Numidium\Exception\ErrorException;
use oscarpalmer\Numidium\Numidium;
use oscarpalmer\Numidium\Routing\Items\Dependencies;
use oscarpalmer\Numidium\Routing\Items\Routes;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use ReflectionObject;

class NumidiumTest extends TestCase
{
	public function testConstructor(): void
	{
		$numidium = new Numidium();

		$this->assertInstanceOf(Numidium::class, $numidium);
	}

	public function testRoutesAndDependencies(): void
	{
		$numidium = new Numidium();

		$returnedFromDependencies = $numidium->dependencies(function ($parameter) {
			$this->assertInstanceOf(Dependencies::class, $parameter);
		});

		$returnedFromRoutes = $numidium->routes(function ($parameter) {
			$this->assertInstanceOf(Routes::class, $parameter);
		});

		$this->assertInstanceOf(Numidium::class, $returnedFromDependencies);
		$this->assertInstanceOf(Numidium::class, $returnedFromRoutes);
	}

	public function testHandle(): void
	{
		$x = [
			[404, null, '404 Not Found'],
			[400, function () {
				throw new ErrorException(400);
			}, '400 Bad Request'],
			[200, function () {
				return 'OK';
			}, 'OK'],
			[500, function () {
				throw new LogicException();
			}, '500 Internal Server Error'],
		];

		foreach ($x as $y) {
			$numidium = new Numidium();

			if (! is_null($y[1])) {
				$numidium->routes(function (Routes $routes) use ($y) {
					$routes->get('/', $y[1]);
				});
			}

			$response = $numidium->handle(self::getRequest());

			$this->assertInstanceOf(ResponseInterface::class, $response);
			$this->assertStringStartsWith($y[2], $response->getBody()->__toString());
		}
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testRun(): void
	{
		$numidium = new Numidium();

		$numidium->run();

		$this->expectOutputString('404 Not Found');
	}

	public function testVersion(): void
	{
		$this->assertIsString(Numidium::VERSION);
	}

	public static function getRequest(): \Psr\Http\Message\ServerRequestInterface
	{
		$factory = new Psr17Factory();
		$creator = new ServerRequestCreator($factory, $factory, $factory, $factory);

		return $creator->fromGlobals();
	}

	public static function getValue(object $source, string $property): mixed
	{
		$reflection = new ReflectionObject($source);

		$property = $reflection->getProperty($property);

		$property->setAccessible(true);

		return $property->getValue($source);
	}
}
