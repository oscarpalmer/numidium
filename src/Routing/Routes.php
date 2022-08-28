<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing;

use Closure;

final class Routes
{
	public function __construct(private readonly Router $router)
	{
	}

	public function delete(string $path, string|Closure $callback): Routes
	{
		$this->router->addRoute('DELETE', $path, $callback);

		return $this;
	}

	public function error(int $status, string|Closure $callback): Routes
	{
		$this->router->addError($status, $callback);

		return $this;
	}

	public function get(string $path, string|Closure $callback): Routes
	{
		$this->router->addRoute('GET', $path, $callback);
		$this->router->addRoute('HEAD', $path, $callback);

		return $this;
	}

	public function head(string $path, string|Closure $callback): Routes
	{
		$this->router->addRoute('HEAD', $path, $callback);

		return $this;
	}

	public function patch(string $path, string|Closure $callback): Routes
	{
		$this->router->addRoute('PATCH', $path, $callback);

		return $this;
	}

	public function post(string $path, string|Closure $callback): Routes
	{
		$this->router->addRoute('POST', $path, $callback);

		return $this;
	}

	public function put(string $path, string|Closure $callback): Routes
	{
		$this->router->addRoute('PUT', $path, $callback);

		return $this;
	}
}
