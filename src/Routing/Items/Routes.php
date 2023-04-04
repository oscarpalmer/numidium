<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Items;

use oscarpalmer\Numidium\Routing\Router;

final class Routes
{
	public function __construct(private readonly Router $router)
	{
	}

	/**
	 * Add a route for handling a DELETE-request
	 *
	 * @param string $path Path for route
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function delete(string $path, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addRoute('DELETE', $path, $callback, $middleware, false);

		return $this;
	}

	/**
	 * Add an error handler for a status code
	 *
	 * @param int $status Status code for error
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function error(int $status, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addError($status, $callback, $middleware);

		return $this;
	}

	/**
	 * Add a route for handling a GET-request
	 *
	 * @param string $path Path for route
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function get(string $path, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addRoute('GET', $path, $callback, $middleware, false);
		$this->router->addRoute('HEAD', $path, $callback, $middleware, false);

		return $this;
	}

	/**
	 * Add a route for handling a HEAD-request
	 *
	 * @param string $path Path for route
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function head(string $path, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addRoute('HEAD', $path, $callback, $middleware, false);

		return $this;
	}

	/**
	 * Add a route for handling an OPTIONS-request
	 *
	 * @param string $path Path for route
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function options(string $path, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addRoute('OPTIONS', $path, $callback, $middleware, false);

		return $this;
	}

	/**
	 * Add a route for handling a PATCH-request
	 *
	 * @param string $path Path for route
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function patch(string $path, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addRoute('PATCH', $path, $callback, $middleware, false);

		return $this;
	}

	/**
	 * Add a route for handling a POST-request
	 *
	 * @param string $path Path for route
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function post(string $path, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addRoute('POST', $path, $callback, $middleware, false);

		return $this;
	}

	/**
	 * Add a route for handling a PUT-request
	 *
	 * @param string $path Path for route
	 * @param callable|string $callback Callback for route
	 * @param array<callable|string>|callable|string|null $middleware Optional middleware
	 */
	public function put(string $path, callable|string $callback, array|callable|string|null $middleware = null): Routes
	{
		$this->router->addRoute('PUT', $path, $callback, $middleware, false);

		return $this;
	}
}
