<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use oscarpalmer\Numidium\Exception\Response;
use oscarpalmer\Numidium\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final class Numidium implements RequestHandlerInterface
{
	public const VERSION = '0.9.0';

	private Configuration $configuration;

	private Router $router;

	public function __construct(?Configuration $configuration = null)
	{
		$this->configuration = $configuration ?? new Configuration();
		$this->router = new Router($this->configuration);
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		try {
			return $this->router->run($request);
		} catch (Response $exception) {
			return $exception->getResponse()->withProtocolVersion($request->getProtocolVersion());
		} catch (Throwable $throwable) {
			return $this->router->getError(500, $request, $throwable)->withProtocolVersion($request->getProtocolVersion());
		}
	}

	public function routes(callable $callback): Numidium
	{
		call_user_func($callback, $this->router->getRoutes());

		return $this;
	}

	public function run(?ServerRequestInterface $request = null): void
	{
		ob_start();

		$request ??= $this->createRequest();
		$response = $this->handle($request);

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
