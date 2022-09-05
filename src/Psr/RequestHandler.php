<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Psr;

use Closure;
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
	 * @var array<string|Closure>
	 */
	private array $middleware;

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

	private function createResponse(string $type, ServerRequestInterface $request, string|Closure $callback): mixed
	{
		if (is_callable($callback)) {
			return call_user_func($callback, $request, $type === 'middleware' ? $this : $this->parameters);
		}

		if (! is_string($callback)) {
			return null;
		}

		if (! str_contains($callback, '->')) {
			return $this->createInstancedResponse($type, $callback, null, $request);
		}

		$parts = explode('->', $callback);

		return $this->createInstancedResponse($type, $parts[0], $parts[1], $request);
	}

	private function createInstancedResponse(string $type, string $class, ?string $method, ServerRequestInterface $request): mixed
	{
		if (! class_exists($class)) {
			throw new LogicException('');
		}

		$instance = $this->container->has($class)
			? $this->container->get($class)
			: new $class();

		if (! is_object($instance)) {
			throw new LogicException('');
		}

		if ($type === 'middleware' && is_null($method) && ! ($instance instanceof MiddlewareInterface)) {
			throw new LogicException('');
		}

		if ($type === 'response' && is_null($method) && ! ($instance instanceof RequestHandlerInterface)) {
			throw new LogicException('');
		}

		$method ??= ($type === 'middleware' ? 'process' : 'handle');

		if (! method_exists($instance, $method)) {
			throw new LogicException('');
		}

		return $instance->$method($request, $type === 'middleware' ? $this : $this->parameters);
	}

	private function getResponse(string $type, ServerRequestInterface $request, string|Closure $callback): ResponseInterface
	{
		$response = $this->createResponse($type, $request, $callback);

		if ($response instanceof ResponseInterface) {
			if ($this->item instanceof Error) {
				$response = $response->withStatus($this->item->getStatus());
			}

			return $response->withProtocolVersion($request->getProtocolVersion());
		}

		return (new Response(
			$this->item->getStatus(),
			$this->configuration->getDefaultHeaders(),
			$this->getResponseBody($response),
		))->withProtocolVersion($request->getProtocolVersion());
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
			throw new LogicException('');
		}

		return (string) $body;
	}
}
