<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

use LogicException;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait Response
{
	private readonly mixed $callback;
	private readonly int $status;

	public function respond(ServerRequestInterface $request, mixed $parameter): ResponseInterface
	{
		$response = $this->getResponse($request, $parameter);

		if ($response instanceof ResponseInterface) {
			return $response;
		}

		return new Psr7Response($this->status, [
			'content-type' => 'text/html; charset=utf-8',
		], is_scalar($response) ? (string) $response : json_encode($response));
	}

	private function getResponse(ServerRequestInterface $request, mixed $parameter): mixed
	{
		if (is_callable($this->callback)) {
			return call_user_func($this->callback, $request);
		}

		if (! is_string($this->callback)) {
			return null;
		}

		if (! str_contains($this->callback, '->')) {
			return $this->getInstancedResponse($this->callback, 'handle', $request, $parameter);
		}

		$parts = explode('->', $this->callback);

		return $this->getInstancedResponse($parts[0], $parts[1], $request, $parameter);
	}

	private function getInstancedResponse(string $class, string $method, ServerRequestInterface $request, mixed $parameter): mixed
	{
		$instance = new $class();

		if ($method === 'handle' && ! ($instance instanceof RequestHandlerInterface)) {
			throw new LogicException();
		}

		return $instance->$method($request, $parameter);
	}
}
