<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Psr;

use Exception;
use League\Container\Container;
use Nyholm\Psr7\Response as Psr7Response;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Psr\RequestHandler;
use oscarpalmer\Numidium\Routing\Item\Error;
use oscarpalmer\Numidium\Routing\Item\Route;
use oscarpalmer\Numidium\Test\NumidiumTest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

final class RequestHandlerTest extends TestCase
{
	public function testBadResource(): void
	{
		$handler = new RequestHandler(new Route('', 'oscarpalmer\Numidium\Test\Fake\Inherited', [], true));

		try {
			$handler
				->prepare(new Configuration(), new Container(), null)
				->handle(NumidiumTest::getRequest());
		} catch (Exception $exception) {
			$this->assertInstanceOf('LogicException', $exception);
		}
	}

	public function testDefaultMethods(): void
	{
		$handler = new RequestHandler(new Route(
			'',
			'oscarpalmer\Numidium\Test\Fake\Inherited',
			['oscarpalmer\Numidium\Test\Fake\Inherited'],
			false),
		);

		$response = $handler
			->prepare(new Configuration(), new Container(), null)
			->handle(NumidiumTest::getRequest());

		$this->assertSame('Fake\Inherited::handle: 1234', $response->getBody()->__toString());
	}

	public function testErrorResponse(): void
	{
		$handler = new RequestHandler(new Error(404, function () {
			return new Psr7Response(200, [], '404 Not Found');
		}, []));

		$response = $handler
			->prepare(new Configuration(), new Container(), null)
			->handle(NumidiumTest::getRequest());

		$this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
		$this->assertSame(404, $response->getStatusCode());
		$this->assertSame('404 Not Found', $response->getBody()->__toString());
	}

	public function testInheritedClasses(): void
	{
		$route_1 = new Route('', 'oscarpalmer\Numidium\Test\Fake\Generic', [], false);
		$route_2 = new Route('', 'oscarpalmer\Numidium\Test\Fake\Inherited', ['oscarpalmer\Numidium\Test\Fake\Generic'], false);

		foreach ([$route_1, $route_2] as $route) {
			$handler = new RequestHandler($route);

			try {
				$handler
					->prepare(new Configuration(), new Container(), null)
					->handle(NumidiumTest::getRequest());
			} catch (Exception $exception) {
				$this->assertInstanceOf('LogicException', $exception);
			}
		}
	}

	public function testMiddleware(): void
	{
		$blob = new stdClass();

		$handler = new RequestHandler(new Route('',
			function () use ($blob) {
				return $blob->body ?? '';
			},
			[function (ServerRequestInterface $req, RequestHandlerInterface $rhi) use ($blob) {
				$blob->body = 'Hello, world!';

				return $rhi->handle($req);
			}],
			false));

		$response = $handler
			->prepare(new Configuration(), new Container(), null)
			->handle(NumidiumTest::getRequest());

		$this->assertSame('Hello, world!', $response->getBody()->__toString());
	}

	public function testMissingClass(): void
	{
		$handler = new RequestHandler(new Route('', 'not a real class', [], false));

		try {
			$handler
				->prepare(new Configuration(), new Container(), null)
				->handle(NumidiumTest::getRequest());
		} catch (Exception $exception) {
			$this->assertInstanceOf('LogicException', $exception);
		}
	}

	public function testMissingMethod(): void
	{
		$handler = new RequestHandler(new Route('', 'oscarpalmer\Numidium\Test\Fake\Generic->blah', [], false));

		try {
			$handler
				->prepare(new Configuration(), new Container(), null)
				->handle(NumidiumTest::getRequest());
		} catch (Exception $exception) {
			$this->assertInstanceOf('LogicException', $exception);
		}
	}

	public function testResponseBody(): void
	{
		$handler_scalar = new RequestHandler(new Route('', function () { return 1234; }, [], false));
		$handler_error = new RequestHandler(new Route('', function () { return []; }, [], false));

		$response_scalar = $handler_scalar
			->prepare(new Configuration(), new Container(), null)
			->handle(NumidiumTest::getRequest());

		$this->assertSame('1234', $response_scalar->getBody()->__toString());

		try {
			$handler_error
				->prepare(new Configuration(), new Container(), null)
				->handle(NumidiumTest::getRequest());
		} catch (Exception $exception) {
			$this->assertInstanceOf('LogicException', $exception);
		}
	}

	public function testDependencyResponse(): void
	{
		$dependencies = new Container();

		$dependencies->add('oscarpalmer\Numidium\Test\Fake\Generic');

		$handler = new RequestHandler(new Route('', 'oscarpalmer\Numidium\Test\Fake\Generic->method', [], false));

		$response = $handler
			->prepare(new Configuration(), $dependencies, null)
			->handle(NumidiumTest::getRequest());

		$this->assertSame('Fake\Generic::method', $response->getBody()->__toString());
	}
}
