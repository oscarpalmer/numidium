<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Controllers;

use Exception;
use League\Container\Container;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Exception\Response;
use oscarpalmer\Numidium\Psr\RequestHandler;
use oscarpalmer\Numidium\Routing\Item\Route;
use oscarpalmer\Numidium\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class Manager
{
	public function __construct(
		private readonly Router $router,
		private readonly Configuration $configuration,
		private readonly Container $container,
	) {
	}

	public function respond(ServerRequestInterface $request, string $path): ResponseInterface
	{
		if ($this->findCallback($path, $callback)) {
			$handler = new RequestHandler($callback->toRoute());
			$prepared = $handler->prepare($this->configuration, $this->container, null);

			throw new Response($prepared->handle($request));
		}

		return $this->router->getError(404, $request);
	}

	/**
	 * @param array<Callback> $callbacks
	 */
	private function evaluateCallbacks(array $callbacks, ?Callback &$returned): bool
	{
		$prefix = $this->configuration->getControllerPrefix();

		foreach ($callbacks as $callback) {
			$method = $callback->getMethod();

			$class = sprintf('%s%s', $prefix, str_replace('/', '\\', $callback->getClass()));
			$pattern = sprintf('/\A%s\z/i', str_replace('\\', '\\\\', $class));

			try {
				$this->loadCallback($class, $method);

				$matches = array_values(array_filter(get_declared_classes(), function ($declared) use ($pattern) {
					return preg_match($pattern, $declared) === 1;
				}));

				if (count($matches) === 1) {
					$returned = new Callback($matches[0], $method);

					return true;
				}
			} catch (Throwable) {
				continue;
			}
		}

		return false;
	}

	private function findCallback(string $path, ?Callback &$callback): bool
	{
		$unprefixed = ltrim($path, $this->configuration->getPathPrefix());
		$trimmed = trim($unprefixed, '/');

		preg_match('/\A(.*)\/(.*)\z/', $trimmed, $matches);

		$callbacks = $this->getCallbacks($trimmed, $matches);

		return $this->evaluateCallbacks($callbacks, $callback);
	}

	/**
	 * @param array<string> $matches
	 *
	 * @return array<Callback>
	 */
	private function getCallbacks(string $original, array $matches): array
	{
		if (count($matches) === 3) {
			return [
				new Callback($matches[1], $matches[2]),
				new Callback($original, 'handle'),
			];
		}

		return [
			new Callback($original, 'handle'),
			new Callback($this->configuration->getDefaultController(), $original),
		];
	}

	private function loadCallback(string $class, string $method): void
	{
		if (! @class_exists($class) || ! @method_exists($class, $method)) {
			throw new Exception();
		}
	}
}
