<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Configuration;

use LogicException;

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
			throw new LogicException('');
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
			throw new LogicException('');
		}

		return $controller;
	}

	/**
	 * @return array<string, string>
	 */
	private function getValidDefaultHeaders(mixed $headers): array
	{
		if (! is_array($headers)) {
			throw new LogicException('');
		}

		return $headers;
	}

	private function getValidPathPrefix(mixed $prefix): string
	{
		if (! is_string($prefix)) {
			throw new LogicException('');
		}

		$prefix = trim($prefix, '/');

		if (mb_strlen($prefix, 'utf-8') === 0) {
			return $prefix;
		}

		return '/' . $prefix;
	}
}
