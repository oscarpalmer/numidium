<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Items;

use oscarpalmer\Numidium\Routing\Router;

final class Resources
{
	public function __construct(private readonly Router $router)
	{
	}

	public function add(string $path, string $callback, array|callable|string|null $middleware = null): Resources
	{
		$indexCallback = sprintf('%s->index', $callback);

		// Retrieving all resources
		$this->router->addRoute('GET', $path, $indexCallback, $middleware, true);
		$this->router->addRoute('HEAD', $path, $indexCallback, $middleware, true);

		// Creating a resource
		$this->router->addRoute('POST', $path, sprintf('%s->create', $callback), $middleware, true);

		$idPath = sprintf('%s/#id', $path);
		$readCallback = sprintf('%s->read', $callback);

		// Reading a resource
		$this->router->addRoute('GET', $idPath, $readCallback, $middleware, true);
		$this->router->addRoute('HEAD', $idPath, $readCallback, $middleware, true);

		// Updating a resource
		$this->router->addRoute('PATCH', $idPath, sprintf('%s->update', $callback), $middleware, true);

		// Deleting a resource
		$this->router->addRoute('DELETE', $idPath, sprintf('%s->delete', $callback), $middleware, true);

		$editCallback = sprintf('%s->edit', $callback);
		$editPath = sprintf('%s/#id/edit', $path);

		// Editing a resource
		$this->router->addRoute('GET', $editPath, $editCallback, $middleware, true);
		$this->router->addRoute('HEAD', $editPath, $editCallback, $middleware, true);

		return $this;
	}
}
