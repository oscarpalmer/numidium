<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium;

use LogicException;

final class Configuration
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

	private array $values = [
		'controller_prefix' => '',
		'default_controller' => 'home',
		'default_headers' => [
			'content-type' => 'text/html; charset=utf-8',
		],
		'path_prefix' => '',
	];

	/**
	 * @param array<string, mixed> $configuration
	 */
	public function __construct(array $configuration = [])
	{
		foreach ($configuration as $key => $value) {
			if (! isset($this->values[$key])) {
				continue;
			}

			$this->values[$key] = isset($this->validators[$key])
				? $this->{$this->validators[$key]}($value)
				: $value;
		}
	}

	public function getControllerPrefix(): string
	{
		return $this->values['controller_prefix'];
	}

	public function getDefaultController(): string
	{
		return $this->values['default_controller'];
	}

	/**
	 * @return array<string, string>
	 */
	public function getDefaultHeaders(): array
	{
		return $this->values['default_headers'];
	}

	public function getPathPrefix(): string
	{
		return $this->values['path_prefix'];
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
