<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Fake;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Inherited implements MiddlewareInterface, RequestHandlerInterface
{
	static int $value = 4321;

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return new Response(200, [], 'Fake\Inherited::handle: ' . self::$value);
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		self::$value = 1234;

		return $handler->handle($request);
	}
}
