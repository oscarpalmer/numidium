<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing;

use oscarpalmer\Numidium\Exception\Response as ExceptionResponse;
use oscarpalmer\Numidium\Http\Response;
use oscarpalmer\Numidium\Routing\Item\Error;
use oscarpalmer\Numidium\Routing\Item\Parameters;
use oscarpalmer\Numidium\Routing\Item\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class Router
{
	/**
	 * @var array<Error>
	 */
	private array $errors = [];

	/**
	 * @var array<array<Route>>
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

	public function addError(int $status, string|callable $callback): void
	{
		$this->errors[$status] = new Error($status, $callback);
	}

	public function addRoute(string $method, string $path, string|callable $callback): void
	{
		$this->routes[$method][] = new Route($path, $callback);
	}

	public function getError(int $status, ServerRequestInterface $request, ?Throwable $throwable = null): ResponseInterface
	{
		if (isset($this->errors[$status])) {
			return $this->errors[$status]->respond($request, $throwable);
		}

		$response = Response::create($status, '', [
			'content-type' => 'text/html; charset=utf-8',
		]);

		$response->getBody()->write(sprintf('%s %s<br><br>%s', $status, $response->getReasonPhrase(), $throwable));

		return $response->withProtocolVersion($request->getProtocolVersion());
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
			if (preg_match($route->getExpression(), $path, $matches)) {
				throw new ExceptionResponse($route->respond($request, new Parameters($route->getPath(), $matches)));
			}
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
