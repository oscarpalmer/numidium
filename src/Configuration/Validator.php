<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Configuration;

use InvalidArgumentException;

final class Validator
{
	/**
	 * @var array<string, string>
	 */
	private array $validators = [
		'controller_prefix' => 'getValidControllerPrefix',
		'default_controller' => 'getValidDefaultController',
		'default_headers' => 'getValidDefaultHeaders',
		'path_prefix' => 'getValidPathPrefix',
	];

	public function validate(string $key, mixed $value): mixed
	{
		if (! array_key_exists($key, $this->validators)) {
			return $value;
		}

		return $this->{$this->validators[$key]}($value);
	}

	private function getValidControllerPrefix(mixed $prefix): string
	{
		if (! is_string($prefix)) {
			throw new InvalidArgumentException('Controller prefix must be a string');
		}

		$prefix = trim($prefix, '/\\');

		if (mb_strlen($prefix, 'utf-8') === 0) {
			return $prefix;
		}

		return str_replace('/', '\\', $prefix) . '\\';
	}

	private function getValidDefaultController(mixed $controller): string
	{
		if (! is_string($controller)) {
			throw new InvalidArgumentException('Default controller name must be a string');
		}

		return $controller;
	}

	/**
	 * @return array<string, array<string>|string>
	 */
	private function getValidDefaultHeaders(mixed $headers): array
	{
		if (! is_array($headers)) {
			throw new InvalidArgumentException('Default headers must be an array of RFC 7230-compatible headers');
		}

		foreach ($headers as $header => $values) {
			if (! is_string($header) || preg_match('/^[!#$%&\'*+.^_`|~0-9A-Za-z-]+$/', $header) !== 1) {
				throw new InvalidArgumentException('Header name must be an RFC 7230-compatible string');
			}

			if (! is_array($values)) {
				if ((! is_numeric($values) && ! is_string($values)) || preg_match('/^[ \t\x21-\x7E\x80-\xFF]*$/', (string) $values) !== 1) {
					throw new \InvalidArgumentException('Header values must be RFC 7230-compatible strings');
				}

				continue;
			}

			foreach ($values as $value) {
				if ((! is_numeric($value) && ! is_string($value)) || preg_match('/^[ \t\x21-\x7E\x80-\xFF]*$/', (string) $value) !== 1) {
					throw new \InvalidArgumentException('Header values must be RFC 7230-compatible strings');
				}
			}
		}

		return $headers;
	}

	private function getValidPathPrefix(mixed $prefix): string
	{
		if (! is_string($prefix)) {
			throw new InvalidArgumentException('Path prefix must be a string');
		}

		$prefix = trim($prefix, '/');

		if (mb_strlen($prefix, 'utf-8') === 0) {
			return $prefix;
		}

		return '/' . $prefix;
	}
}
