<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Psr;

use League\Container\Container;
use LogicException;
use Nyholm\Psr7\Response;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Routing\Item\Basic;
use oscarpalmer\Numidium\Routing\Item\Error;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequestHandler implements RequestHandlerInterface
{
	private Configuration $configuration;

	private Container $container;

	/**
	 * @var array<callable|string>
	 */
	private array $middleware = [];

	private mixed $parameters;

	public function __construct(private readonly Basic $item)
	{
		$this->middleware = $item->getMiddleware();
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		if (count($this->middleware) > 0) {
			return $this->getResponse('middleware', $request, array_shift($this->middleware));
		}

		return $this->getResponse('response', $request, $this->item->getCallback());
	}

	public function prepare(Configuration $configuration, Container $container, mixed $parameters): RequestHandlerInterface
	{
		$this->configuration = $configuration;
		$this->container = $container;
		$this->parameters = $parameters;

		return $this;
	}

	private function createResponse(string $type, ServerRequestInterface $request, mixed $callback): mixed
	{
		if (is_callable($callback)) {
			return call_user_func($callback, $request, $type === 'middleware' ? $this : $this->parameters);
		}

		if (! is_string($callback)) {
			// Ignored as $callback is defined as mixed, but should really be a callable or string due to type hints
			// @codeCoverageIgnoreStart
			return null;
			// @codeCoverageIgnoreEnd
		}

		if (! str_contains($callback, '->')) {
			return $this->createInstancedResponse($type, $callback, null, $request);
		}

		$parts = explode('->', $callback);

		return $this->createInstancedResponse($type, $parts[0], $parts[1], $request);
	}

	private function createInstancedResponse(string $type, string $name, ?string $method, ServerRequestInterface $request): mixed
	{
		$isMiddleware = $type === 'middleware';

		if (! @class_exists($name)) {
			throw new LogicException("The class '{$name}' does not exist");
		}

		$instance = $this->container->has($name)
			? $this->container->get($name)
			: new $name();

		if (! is_object($instance)) {
			// Ignored as $instance is defined as mixed, but should really be an object
			// @codeCoverageIgnoreStart
			throw new LogicException("Unable to instatiate the class '{$name}'");
			// @codeCoverageIgnoreEnd
		}

		if (is_null($method)) {
			$method = $this->getMethod($name, $instance, $isMiddleware);
		}

		if (! method_exists($instance, $method)) {
			throw new LogicException("The method '{$method}' could not be found for class '{$name}'");
		}

		$first = $isMiddleware ? $this : $this->parameters;
		$second = $isMiddleware ? $this->parameters : null;

		return $instance->$method($request, $first, $second);
	}

	private function getMethod(string $name, mixed $instance, bool $isMiddleware): string
	{
		if (($isMiddleware && ! ($instance instanceof MiddlewareInterface))
			|| (! $isMiddleware && ! ($instance instanceof RequestHandlerInterface))) {
			$expected = $isMiddleware
				? MiddlewareInterface::class
				: RequestHandlerInterface::class;

			throw new LogicException("Simple callback string provided, expected class '{$name}' to inherit '{$expected}'");
		}

		return $isMiddleware
			? 'process'
			: 'handle';
	}

	private function getResponse(string $type, ServerRequestInterface $request, mixed $callback): ResponseInterface
	{
		$response = $this->createResponse($type, $request, $callback);

		if ($response instanceof ResponseInterface) {
			if ($this->item instanceof Error) {
				$response = $response->withStatus($this->item->getStatus());
			}

			return $response->withProtocolVersion($request->getProtocolVersion());
		}

		$status = $this->item->getStatus();
		$headers = $this->configuration->getDefaultHeaders();
		$body = $this->getResponseBody($response);

		$response = new Response($status, $headers, $body);

		return $response->withProtocolVersion($request->getProtocolVersion());
	}

	/**
	 * @return resource|string|StreamInterface
	 */
	private function getResponseBody(mixed $body)
	{
		if (is_string($body) || is_resource($body) || $body instanceof StreamInterface) {
			return $body;
		}

		if (! is_scalar($body)) {
			throw new LogicException('Response body must be scalar, a resource, or inherit \'StreamInterface\'');
		}

		return (string) $body;
	}
}
