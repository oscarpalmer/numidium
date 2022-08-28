<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

use Closure;
use LogicException;
use oscarpalmer\Numidium\Configuration;
use oscarpalmer\Numidium\Http\Parameters;
use oscarpalmer\Numidium\Http\Response as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

trait Response
{
	private readonly string|Closure $callback;
	private readonly int $status;

	public function respond(ServerRequestInterface $request, Configuration $configuration, Parameters|Throwable|null $parameters): ResponseInterface
	{
		$response = $this->getResponse($request, $parameters);

		if ($response instanceof ResponseInterface) {
			return $response;
		}

		return HttpResponse::create($this->status, $response, $configuration->getDefaultHeaders());
	}

	private function getResponse(ServerRequestInterface $request, Parameters|Throwable|null $parameters): mixed
	{
		if (is_callable($this->callback)) {
			return call_user_func($this->callback, $request, $parameters);
		}

		if (! is_string($this->callback)) {
			return null;
		}

		if (! str_contains($this->callback, '->')) {
			return $this->getInstancedResponse($this->callback, 'handle', $request, $parameters);
		}

		$parts = explode('->', $this->callback);

		return $this->getInstancedResponse($parts[0], $parts[1], $request, $parameters);
	}

	private function getInstancedResponse(string $class, string $method, ServerRequestInterface $request, Parameters|Throwable|null $parameters): mixed
	{
		$instance = new $class();

		if ($method === 'handle' && ! ($instance instanceof RequestHandlerInterface)) {
			throw new LogicException();
		}

		return $instance->$method($request, $parameters);
	}
}
