<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Psr;

use Closure;
use LogicException;
use oscarpalmer\Numidium\Configuration;
use oscarpalmer\Numidium\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class RequestHandler implements RequestHandlerInterface
{
	protected readonly string|Closure $callback;

	protected readonly string $path;

	protected readonly int $status;

	private Configuration $configuration;

	private mixed $parameters;

	public function __construct(int $status, ?string $path, string|Closure $callback)
	{
		$this->callback = $callback;
		$this->path = $path ?? '';
		$this->status = $status;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$response = $this->getResponse($request);

		if ($response instanceof ResponseInterface) {
			return $response;
		}

		return Response::create($this->status, $response, $this->configuration->getDefaultHeaders());
	}

	public function prepare(Configuration $configuration, mixed $parameters): RequestHandlerInterface
	{
		$this->configuration = $configuration;
		$this->parameters = $parameters;

		return $this;
	}

	private function getResponse(ServerRequestInterface $request): mixed
	{
		if (is_callable($this->callback)) {
			return call_user_func($this->callback, $request, $this->parameters);
		}

		if (! is_string($this->callback)) {
			return null;
		}

		if (! str_contains($this->callback, '->')) {
			return $this->getInstancedResponse($this->callback, null, $request);
		}

		$parts = explode('->', $this->callback);

		return $this->getInstancedResponse($parts[0], $parts[1], $request);
	}

	private function getInstancedResponse(string $class, ?string $method, ServerRequestInterface $request): mixed
	{
		if (! class_exists($class)) {
			throw new LogicException('');
		}

		$instance = new $class();

		if (is_null($method) && ! ($instance instanceof RequestHandlerInterface)) {
			throw new LogicException('');
		}

		$method ??= 'handle';

		if (! method_exists($instance, $method)) {
			throw new LogicException('');
		}

		return $instance->$method($request, $this->parameters);
	}
}
