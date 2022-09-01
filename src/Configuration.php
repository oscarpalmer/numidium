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
		'default_headers' => 'getValidHeaders',
		'path_prefix' => 'getValidPathPrefix',
	];

	private array $values = [
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

	/**
	 * @return array<string, string>
	 */
	private function getValidHeaders(mixed $headers): array
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

		return "/{$prefix}";
	}
}
