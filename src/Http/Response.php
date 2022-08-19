<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Http;

use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Response
{
	private const JSON_OPTIONS = JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

	/**
	 * @param array<string> $headers
	 */
	public static function badRequest(mixed $body, array $headers = []): ResponseInterface
	{
		return self::create(400, $body, $headers);
	}

	/**
	 * @param array<string> $headers
	 */
	public static function create(int $status, mixed $body, array $headers = []): ResponseInterface
	{
		return new Psr7Response($status, $headers, self::getBody($body));
	}

	/**
	 * @param array<string> $headers
	 */
	public static function notFound(mixed $body, array $headers = []): ResponseInterface
	{
		return self::create(404, $body, $headers);
	}

	/**
	 * @param array<string> $headers
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

		if (is_scalar($body)) {
			return (string) $body;
		}

		$encoded = json_encode($body, self::JSON_OPTIONS);

		if ($encoded !== false) {
			return $encoded;
		}

		return '';
	}
}
