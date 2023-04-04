<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test;

use Exception;
use League\Container\Container;
use Nyholm\Psr7\Uri;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Exception\Response;
use oscarpalmer\Numidium\Numidium;
use oscarpalmer\Numidium\Routing\Items\Resources;
use oscarpalmer\Numidium\Routing\Items\Routes;
use oscarpalmer\Numidium\Routing\Router;
use oscarpalmer\Numidium\Test\Fake\Resource;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class RoutingTest extends TestCase
{
	public function testErrors(): void
	{
		$router = new Router(new Configuration(), new Container());
		$routes = new Routes($router);

		$statuses    = [404, 418,            500];
		$callbacks  = ['a',  function () {}, 'b'];
		$middleware = [null, 'a',            ['b']];

		foreach ($statuses as $index => $status) {
			$routes->error($status, $callbacks[$index], $middleware[$index]);
		}

		/** @var array<\oscarpalmer\Numidium\Routing\Item\Error> */
		$values = NumidiumTest::getValue($router, 'errors');

		$this->assertCount(3, $values);

		foreach ($statuses as $index => $status) {
			$item = $values[$status];

			$itemMiddleware = is_null($middleware[$index])
				? []
				: (is_array($middleware[$index])
					? $middleware[$index]
					: [$middleware[$index]]);

			$this->assertSame($status, $item->getStatus());
			$this->assertSame($callbacks[$index], $item->getCallback());
			$this->assertSame($itemMiddleware, $item->getMiddleware());
		}
	}

	public function testResources(): void
	{
		$numidium = new Numidium();

		$numidium->resources(function (Resources $resources) {
			$resources->add('resource', Resource::class);
		});

		$router = NumidiumTest::getValue($numidium, 'router');

		/** @var array<array<\oscarpalmer\Numidium\Routing\Item\Route>> */
		$routes = NumidiumTest::getValue($router, 'routes');

		$resources = 0;
		$total = 0;

		foreach ($routes as $array) {
			foreach ($array as $route) {
				$resources += $route->getIsResource()
				? 1
				: 0;

				$total += 1;
			}
		}

		$this->assertSame(1, count($routes['DELETE']));
		$this->assertSame(3, count($routes['GET']));
		$this->assertSame(3, count($routes['HEAD']));
		$this->assertSame(0, count($routes['OPTIONS']));
		$this->assertSame(1, count($routes['PATCH']));
		$this->assertSame(1, count($routes['POST']));
		$this->assertSame(0, count($routes['PUT']));

		$this->assertSame($total, $resources);
	}

	public function testRoutes(): void
	{
		$methods    = ['delete', 'get', 'head', 'options',      'patch', 'post', 'put'];
		$callbacks  = ['a',      'b',   'c',    function () {}, 'd',     'e',    'f'];
		$middleware = [null,     'a',   ['b'],  'c',           ['d'],    'e',    null];

		$router = new Router(new Configuration(), new Container());
		$routes = new Routes($router);

		foreach ($methods as $index => $method) {
			$routes->{$method}('/a/b/c', $callbacks[$index], $middleware[$index]);
		}

		/** @var array<array<\oscarpalmer\Numidium\Routing\Item\Route>> */
		$values = NumidiumTest::getValue($router, 'routes');

		$this->assertCount(7, $values);

		foreach ($methods as $index => $method) {
			$items = $values[strtoupper($method)];

			$this->assertCount($index === 2 ? 2 : 1, $items);

			$item = $items[$index === 2 ? 1 : 0];

			$itemMiddleware = is_null($middleware[$index])
				? []
				: (is_array($middleware[$index]) ? $middleware[$index] : [$middleware[$index]]);

			$this->assertSame('/a/b/c/', $item->getPath());
			$this->assertSame($callbacks[$index], $item->getCallback());
			$this->assertSame($itemMiddleware, $item->getMiddleware());
		}
	}

	public function testRouting(): void
	{
		$router = new Router(new Configuration([
			'path_prefix' => '/subdirectory/',
		]), new Container());

		(new Routes($router))
			->get('/:nested/:path/#number', function () {
				return 'Hello, world!';
			})
			->error(404, function () {
				return '404 Not Found';
			});

		$request = NumidiumTest::getRequest()->withUri(new Uri('http://example.com/subdirectory/nested/path/1234'));

		try {
			$router->run($request);
		} catch (Exception $exception) {
			$this->assertInstanceOf(Response::class, $exception);
		}

		$request = NumidiumTest::getRequest()->withUri(new Uri('http://example.com/nope/'));

		$response = $router->run($request);

		$this->assertInstanceOf(ResponseInterface::class, $response);
	}
}
