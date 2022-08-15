<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class Router
{
	/**
	 * @var array<callable>
	 */
	private array $errors = [];

	/**
	 * @var array<array<mixed>>
	 */
	private array $routes = [
		'DElETE' => [],
		'GET' => [],
		'HEAD' => [],
		'OPTIONS' => [],
		'PATCH' => [],
		'POST' => [],
		'PUT' => [],
	];

	public function addError(int $status, callable $callback): void
	{
		$this->errors[$status] = $callback;
	}

	public function addRoute(string $method, string $path, callable $callback): void
	{
		$this->routes[$method][] = [$path, $callback];
	}

	public function getError(int $status, ServerRequestInterface $request, ?Throwable $throwable = null): ResponseInterface
	{
		if (! isset($this->errors[$status])) {
			$response = new Response($status);

			$response->getBody()->write("{$status} {$response->getReasonPhrase()}");

			return $response;
		}

		$response = call_user_func($this->errors[$status], $request, $throwable);

		if ($response instanceof ResponseInterface) {
			return $response;
		}

		return new Response($status, [], (string) $response);
	}

	public function getRoutes(): Routes
	{
		return new Routes($this);
	}

	public function run(ServerRequestInterface $request): ResponseInterface
	{
		$method = $request->getMethod();
		$path = $this->getPath($request);

		foreach ($this->routes[$method] as $route) {
			if ($path !== $route[0]) {
				continue;
			}

			$response = call_user_func($route[1], $request);

			if ($response instanceof ResponseInterface) {
				return $response;
			}

			return new Response(200, [], (string) $response);
		}

		return $this->getError(in_array($method, ['GET', 'HEAD']) ? 404 : 405, $request);
	}

	private function getPath(ServerRequestInterface $request): string
	{
		$path = $request->getUri()->getPath();

		if (mb_strlen($path) === 0) {
			return '/';
		}

		$path = ltrim($path, '/');

		return "/{$path}";
	}
}
