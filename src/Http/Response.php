<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Http;

use LogicException;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Response
{
	/**
	 * Creates a response for a bad request
	 *
	 * @param scalar|resource|StreamInterface $body
	 * @param array<string> $headers
	 *
	 * @return ResponseInterface A response with the status '400 Bad Request'
	 */
	public static function badRequest(mixed $body, array $headers = []): ResponseInterface
	{
		return self::create(400, $body, $headers);
	}

	/**
	 * @param scalar|resource|StreamInterface $body
	 * @param array<string> $headers
	 */
	public static function create(int $status, mixed $body, array $headers = []): ResponseInterface
	{
		return new Psr7Response($status, $headers, self::getBody($body));
	}

	/**
	 * Creates a response for a missing resource
	 *
	 * @param scalar|resource|StreamInterface $body
	 * @param array<string> $headers
	 *
	 * @return ResponseInterface A response with the status '404 Not Found'
	 */
	public static function notFound(mixed $body, array $headers = []): ResponseInterface
	{
		return self::create(404, $body, $headers);
	}

	/**
	 * Creates an OK response, with status '200 OK'
	 *
	 * @param scalar|resource|StreamInterface $body
	 * @param array<string> $headers
	 *
	 * @return ResponseInterface A response with the status '200 OK'
	 */
	public static function ok(mixed $body, array $headers = []): ResponseInterface
	{
		return self::create(200, $body, $headers);
	}

	/**
	 * @return string|resource|StreamInterface
	 */
	private static function getBody(mixed $body)
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
