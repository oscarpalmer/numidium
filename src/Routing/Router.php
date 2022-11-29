<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing;

use League\Container\Container;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Controllers\Manager;
use oscarpalmer\Numidium\Exception\Response as ExceptionResponse;
use oscarpalmer\Numidium\Http\Parameters;
use oscarpalmer\Numidium\Http\Response;
use oscarpalmer\Numidium\Psr\RequestHandler;
use oscarpalmer\Numidium\Routing\Item\Error;
use oscarpalmer\Numidium\Routing\Item\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class Router
{
	private readonly Manager $controllers;

	/**
	 * @var array<Error>
	 */
	private array $errors = [];

	/**
	 * @var array<array<Route>>
	 */
	private array $routes = [
		'DELETE' => [],
		'GET' => [],
		'HEAD' => [],
		'OPTIONS' => [],
		'PATCH' => [],
		'POST' => [],
		'PUT' => [],
	];

	public function __construct(private readonly Configuration $configuration, private readonly Container $container)
	{
		$this->controllers = new Manager($this, $configuration, $container);
	}

	/**
	 * @param array<callable|string>|callable|string|null $middleware
	 */
	public function addError(int $status, callable|string $callback, array|callable|string|null $middleware): void
	{
		$this->errors[$status] = new Error($status, $callback, $this->getMiddleware($middleware));
	}

	/**
	 * @param array<callable|string>|callable|string|null $middleware
	 */
	public function addRoute(string $method, string $path, callable|string $callback, array|callable|string|null $middleware): void
	{
		$this->routes[$method][] = new Route($this->getRoutePath($path), $callback, $this->getMiddleware($middleware));
	}

	public function getError(int $status, ServerRequestInterface $request, mixed $parameter = null): ResponseInterface
	{
		if (isset($this->errors[$status])) {
			return (new RequestHandler($this->errors[$status]))->prepare($this->configuration, $this->container, $parameter)->handle($request);
		}

		$response = Response::create($status, '', $this->configuration->getDefaultHeaders());

		$parameter = $parameter instanceof Throwable
			? $parameter
			: null;

		$template = is_null($parameter)
			? '%s %s'
			: '%s %s<br><br>%s';

		$response->getBody()->write(sprintf($template, $status, $response->getReasonPhrase(), $parameter));

		return $response->withProtocolVersion($request->getProtocolVersion());
	}

	public function run(ServerRequestInterface $request): ResponseInterface
	{
		$path = $this->getRequestPath($request);
		$routes = $this->routes[$request->getMethod()];

		foreach ($routes as $route) {
			if (preg_match($route->getExpression(), $path, $matches)) {
				throw new ExceptionResponse((new RequestHandler($route))->prepare($this->configuration, $this->container, new Parameters($request, $route, $matches))->handle($request));
			}
		}

		return $this->controllers->respond($request, $path);
	}

	/**
	 * @param array<callable|string>|callable|string|null $middleware
	 *
	 * @return array<callable|string>
	 */
	private function getMiddleware(array|callable|string|null $middleware): array
	{
		if (is_null($middleware)) {
			return [];
		}

		if (is_array($middleware)) {
			return $middleware;
		}

		return [$middleware];
	}

	private function getRequestPath(ServerRequestInterface $request): string
	{
		$path = $request->getUri()->getPath();

		if (mb_strlen($path, 'utf-8') === 0) {
			return '/';
		}

		return '/' . trim($path, '/') . '/';
	}

	private function getRoutePath(string $path): string
	{
		$prefix = $this->configuration->getPathPrefix();

		$path = '/' . trim($path, '/');

		if (stripos($path, $prefix) === 0) {
			return $path . '/';
		}

		return $prefix . $path . '/';
	}
}
