<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing;

use League\Container\Container;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Controllers\Manager;
use oscarpalmer\Numidium\Exception\ResponseException;
use oscarpalmer\Numidium\Http\HttpParameters;
use oscarpalmer\Numidium\Http\HttpResponse;
use oscarpalmer\Numidium\Psr\RequestHandler;
use oscarpalmer\Numidium\Routing\Item\ErrorItem;
use oscarpalmer\Numidium\Routing\Item\RouteItem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class Router
{
	private readonly Manager $controllers;

	/**
	 * @var array<ErrorItem>
	 */
	private array $errors = [];

	/**
	 * @var array<array<RouteItem>>
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
		$this->errors[$status] = new ErrorItem($status, $callback, $this->getMiddleware($middleware));
	}

	/**
	 * @param array<callable|string>|callable|string|null $middleware
	 */
	public function addRoute(string $method, string $path, callable|string $callback, array|callable|string|null $middleware, bool $isResource): void
	{
		$this->routes[$method][] = new RouteItem($this->getRoutePath($path), $callback, $this->getMiddleware($middleware), $isResource);
	}

	public function getError(int $status, ServerRequestInterface $request, mixed $parameter = null): ResponseInterface
	{
		if (isset($this->errors[$status])) {
			return (new RequestHandler($this->errors[$status]))->prepare($this->configuration, $this->container, $parameter)->handle($request);
		}

		$response = HttpResponse::create($status, '', $this->configuration->getDefaultHeaders());

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
				$handler = new RequestHandler($route);
				$parameters = new HttpParameters($request, $route, $matches);

				$prepared = $handler->prepare($this->configuration, $this->container, $parameters);

				throw new ResponseException($prepared->handle($request));
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
