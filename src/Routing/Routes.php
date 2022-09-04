<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing;

use Closure;

final class Routes
{
	public function __construct(private readonly Router $router)
	{
	}

	/**
	 * Add a route for handling a DELETE-request
	 *
	 * @param string $path Path for route
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function delete(string $path, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addRoute('DELETE', $path, $callback, $middleware);

		return $this;
	}

	/**
	 * Add an error handler for a status code
	 *
	 * @param int $status Status code for error
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function error(int $status, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addError($status, $callback, $middleware);

		return $this;
	}

	/**
	 * Add a route for handling a GET-request
	 *
	 * @param string $path Path for route
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function get(string $path, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addRoute('GET', $path, $callback, $middleware);
		$this->router->addRoute('HEAD', $path, $callback, $middleware);

		return $this;
	}

	/**
	 * Add a route for handling a HEAD-request
	 *
	 * @param string $path Path for route
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function head(string $path, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addRoute('HEAD', $path, $callback, $middleware);

		return $this;
	}

	/**
	 * Add a route for handling an OPTIONS-request
	 *
	 * @param string $path Path for route
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function options(string $path, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addRoute('OPTIONS', $path, $callback, $middleware);

		return $this;
	}

	/**
	 * Add a route for handling a PATCH-request
	 *
	 * @param string $path Path for route
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function patch(string $path, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addRoute('PATCH', $path, $callback, $middleware);

		return $this;
	}

	/**
	 * Add a route for handling a POST-request
	 *
	 * @param string $path Path for route
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function post(string $path, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addRoute('POST', $path, $callback, $middleware);

		return $this;
	}

	/**
	 * Add a route for handling a PUT-request
	 *
	 * @param string $path Path for route
	 * @param string|Closure $callback Callback for route
	 * @param array<string|Closure>|string|Closure|null $middleware Optional middleware
	 */
	public function put(string $path, string|Closure $callback, array|string|Closure|null $middleware = null): Routes
	{
		$this->router->addRoute('PUT', $path, $callback, $middleware);

		return $this;
	}
}
