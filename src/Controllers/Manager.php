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
			$route = new Route('', $callback, []);
			$handler = new RequestHandler($route);

			$prepared = $handler->prepare($this->configuration, $this->container, null);

			throw new Response($prepared->handle($request));
		}

		return $this->router->getError(404, $request);
	}

	/**
	 * @param array<array<string>> $callbacks
	 */
	private function evaluateCallbacks(array $callbacks, ?string &$callback): bool
	{
		$prefix = $this->configuration->getControllerPrefix();

		foreach ($callbacks as $callback) {
			$class = $prefix . str_replace('/', '\\', $callback[0]);
			$method = $callback[1];

			try {
				$this->loadCallback($class, $method);

				$pattern = '/' . str_replace('\\', '\\\\', $class) . '/i';

				$matches = array_values(array_filter(get_declared_classes(), function ($declared) use ($pattern) {
					return preg_match($pattern, $declared) === 1;
				}));

				if (count($matches) > 0) {
					$callback = $matches[0] . '->' . $method;

					return true;
				}
			} catch (Throwable) {
				continue;
			}
		}

		return false;
	}

	private function findCallback(string $path, ?string &$callback): bool
	{
		$unprefixed = ltrim($path, $this->configuration->getPathPrefix());
		$trimmed = trim($unprefixed, '/');

		$matched = preg_match('/\A(.*)\/(.*)\z/', $trimmed, $matches);

		$callbacks = $this->getCallbacks($trimmed, $matched === 1, $matches);

		return $this->evaluateCallbacks($callbacks, $callback);
	}

	/**
	 * @param array<string> $matches
	 *
	 * @return array<array<string>>
	 */
	private function getCallbacks(string $original, bool $matched, array $matches): array
	{
		$defaultController = $this->configuration->getDefaultController();

		if (mb_strlen($original, 'utf-8') === 0) {
			return [[$defaultController, 'handle'], [$defaultController, 'index']];
		}

		$callbacks = [[$original, 'handle'], [$original, 'index']];

		if ($matched) {
			$callbacks[] = [$matches[1], $matches[2]];
		} else {
			$callbacks[] = [$defaultController, $original];
		}

		return $callbacks;
	}

	private function loadCallback(string $class, string $method): void
	{
		if (! @class_exists($class) || ! @method_exists($class, $method)) {
			throw new Exception();
		}
	}
}
