<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing;

use Closure;
use oscarpalmer\Numidium\Configuration;
use oscarpalmer\Numidium\Exception\Response as ExceptionResponse;
use oscarpalmer\Numidium\Http\Parameters;
use oscarpalmer\Numidium\Http\Response;
use oscarpalmer\Numidium\Routing\Item\Error;
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

	public function __construct(private readonly Configuration $configuration)
	{
	}

	public function addError(int $status, string|Closure $callback): void
	{
		$this->errors[$status] = new Error($status, $callback);
	}

	public function addRoute(string $method, string $path, string|Closure $callback): void
	{
		$this->routes[$method][] = new Route($this->getRoutePath($path), $callback);
	}

	public function getError(int $status, ServerRequestInterface $request, ?Throwable $throwable = null): ResponseInterface
	{
		if (isset($this->errors[$status])) {
			return $this->errors[$status]->prepare($this->configuration, $throwable)->handle($request);
		}

		$response = Response::create($status, '', $this->configuration->getDefaultHeaders());

		$template = is_null($throwable)
			? '%s %s'
			: '%s %s<br><br>%s';

		$response->getBody()->write(sprintf($template, $status, $response->getReasonPhrase(), $throwable));

		return $response->withProtocolVersion($request->getProtocolVersion());
	}

	public function run(ServerRequestInterface $request): ResponseInterface
	{
		$method = $request->getMethod();
		$path = $this->getRequestPath($request);

		foreach ($this->routes[$method] as $route) {
			if (preg_match($route->getExpression(), $path, $matches)) {
				throw new ExceptionResponse($route->prepare($this->configuration, new Parameters($request, $route, $matches))->handle($request));
			}
		}

		return $this->getError(in_array($method, ['GET', 'HEAD']) ? 404 : 405, $request);
	}

	private function getRequestPath(ServerRequestInterface $request): string
	{
		$path = $request->getUri()->getPath();

		if (mb_strlen($path, 'utf-8') === 0) {
			return '/';
		}

		return '/' . ltrim($path, '/');
	}

	private function getRoutePath(string $path): string
	{
		$prefix = $this->configuration->getPathPrefix();

		$path = '/' . ltrim($path, '/');

		if (stripos($path, $prefix) === 0) {
			return $path;
		}

		return $prefix . $path;
	}
}
