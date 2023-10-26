<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Items;

use oscarpalmer\Numidium\Routing\Router;

final class Resources
{
	public function __construct(private readonly Router $router)
	{
	}

	/**
	 * Add a resource to the router, creating specific routes for a controller that extends `\oscarpalmer\Numidium\Controllers\Resource`.
	 *
	 * - `GET: /entities` - all entities
	 * - `POST: /entities/create` - creates an entity
	 * - `GET: /entities/#id` - a specific entity
	 * - `GET: /entities/#id/edit` - form for editing an entity
	 * - `POST: /entities/#id/update` - updates an entity
	 * - `POST: /entities/#id/remove` - removes an entity
	 *
	 * @param array<callable|string> $middleware
	 */
	public function add(string $path, string $controller, array|callable|string|null $middleware = null): Resources
	{
		$indexCallback = sprintf('%s->index', $controller);

		// Retrieving all resources
		$this->router->addRoute('GET', $path, $indexCallback, $middleware, true);
		$this->router->addRoute('HEAD', $path, $indexCallback, $middleware, true);

		// Creating a resource
		$this->router->addRoute('POST', sprintf('%s/create', $path), sprintf('%s->create', $controller), $middleware, true);

		$idPath = sprintf('%s/#id', $path);
		$readCallback = sprintf('%s->read', $controller);

		// Reading a resource
		$this->router->addRoute('GET', $idPath, $readCallback, $middleware, true);
		$this->router->addRoute('HEAD', $idPath, $readCallback, $middleware, true);

		// Updating a resource
		$this->router->addRoute('POST', sprintf('%s/update', $idPath), sprintf('%s->update', $controller), $middleware, true);

		// Deleting a resource
		$this->router->addRoute('POST', sprintf('%s/remove', $idPath), sprintf('%s->delete', $controller), $middleware, true);

		$editCallback = sprintf('%s->edit', $controller);
		$editPath = sprintf('%s/edit', $idPath);

		// Editing a resource
		$this->router->addRoute('GET', $editPath, $editCallback, $middleware, true);
		$this->router->addRoute('HEAD', $editPath, $editCallback, $middleware, true);

		return $this;
	}
}
