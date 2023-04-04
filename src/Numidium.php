<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium;

use League\Container\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Configuration\Dependencies;
use oscarpalmer\Numidium\Exception\Error;
use oscarpalmer\Numidium\Exception\Response;
use oscarpalmer\Numidium\Routing\Router;
use oscarpalmer\Numidium\Routing\Items\Resources;
use oscarpalmer\Numidium\Routing\Items\Routes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class Numidium implements RequestHandlerInterface
{
	public const VERSION = '0.17.0';

	private Configuration $configuration;

	private Container $container;

	private Router $router;

	/**
	 * @param ?Configuration $configuration Optional configuration
	 * @param ?Container $container Optional dependencies
	 */
	public function __construct(?Configuration $configuration = null, ?Container $container = null)
	{
		$this->configuration = $configuration ?? new Configuration();
		$this->container = $container ?? new Container();

		$this->router = new Router($this->configuration, $this->container);
	}

	/**
	 * Add injectable dependencies for route callbacks
	 */
	public function dependencies(callable $callback): Numidium
	{
		call_user_func($callback, new Dependencies($this->container));

		return $this;
	}

	/**
	 * Create and return a response based on a HTTP request
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		try {
			return $this->router->run($request);
		} catch (Error $error) {
			return $this->router->getError($error->getStatus(), $request, $error->getData());
		} catch (Response $exception) {
			return $exception->getResponse();
		} catch (Throwable $throwable) {
			return $this->router->getError(500, $request, $throwable);
		}
	}

	/**
	 * Add resources for responding to HTTP requests
	 */
	public function resources(callable $callback): Numidium
	{
		call_user_func($callback, new Resources($this->router));

		return $this;
	}

	/**
	 * Add routes for responding to HTTP requests
	 */
	public function routes(callable $callback): Numidium
	{
		call_user_func($callback, new Routes($this->router));

		return $this;
	}

	/**
	 * Create and output a response based on a HTTP request
	 */
	public function run(?ServerRequestInterface $request = null): void
	{
		ob_start();

		$response = $this->handle($request ?? $this->createRequest());

		ob_end_clean();

		$this->sendResponse($response);
	}

	private function createRequest(): ServerRequestInterface
	{
		$factory = new Psr17Factory();
		$creator = new ServerRequestCreator($factory, $factory, $factory, $factory);

		return $creator->fromGlobals();
	}

	private function sendHeaders(ResponseInterface $response, int|null $length): void
	{
		header(sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()), true);

		foreach ($response->getHeaders() as $name => $values) {
			foreach ($values as $value) {
				header(sprintf('%s: %s', $name, $value), false);
			}
		}

		if (! is_null($length)) {
			header(sprintf('content-length: %s', $length));
		}
	}

	private function sendResponse(ResponseInterface $response): void
	{
		$body = $response->getBody();

		if (! headers_sent()) {
			$this->sendHeaders($response, $body->getSize());
		}

		echo (string) $body;
	}
}
