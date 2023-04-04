<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Controllers;

use Exception;
use League\Container\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Controllers\Callback;
use oscarpalmer\Numidium\Exception\Response;
use oscarpalmer\Numidium\Routing\Router;
use oscarpalmer\Numidium\Test\NumidiumTest;
use PHPUnit\Framework\TestCase;

final class ManagerTest extends TestCase
{
	public function testCallback(): void
	{
		$callback = new Callback('x', 'y');

		$this->assertSame('x', $callback->getClass());
		$this->assertSame('y', $callback->getMethod());

		$route = $callback->toRoute();

		$this->assertInstanceOf('oscarpalmer\Numidium\Routing\Item\Route', $route);
	}

	public function testRespond(): void
	{
		$factory = new Psr17Factory();
		$creator = new ServerRequestCreator($factory, $factory, $factory, $factory);

		$request = $creator->fromGlobals();

		$router = new Router(new Configuration([
			'controller_prefix' => 'oscarpalmer\Numidium\Test',
		]), new Container());

		/** @var \oscarpalmer\Numidium\Controllers\Manager */
		$manager = NumidiumTest::getValue($router, 'controllers');

		$response_1 = $manager->respond($request, '/');
		$response_2 = $manager->respond($request, '/fake');
		$response_3 = $manager->respond($request, '/fake/generic');

		$this->assertSame(404, $response_1->getStatusCode());
		$this->assertSame(404, $response_2->getStatusCode());
		$this->assertSame(404, $response_3->getStatusCode());

		try {
			$manager->respond($request, '/fake/generic/method');
		} catch (Exception $exception) {
			$this->assertInstanceOf(Response::class, $exception);
		}
	}
}
