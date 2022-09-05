<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Configuration;

final class Configuration
{
	private string $controller_prefix = '';

	private string $default_controller = 'home';

	/**
	 * @var array<string>
	 */
	private array $default_headers = [
		'content-type' => 'text/html; charset=utf-8',
	];

	private string $path_prefix = '';

	/**
	 * @param array<string, mixed> $configuration
	 */
	public function __construct(array $configuration = [])
	{
		$validator = new Validator();

		foreach ($configuration as $key => $value) {
			if (isset($this->{$key})) {
				$this->{$key} = $validator->validate($key, $value);
			}
		}
	}

	public function getControllerPrefix(): string
	{
		return $this->controller_prefix;
	}

	public function getDefaultController(): string
	{
		return $this->default_controller;
	}

	/**
	 * @return array<string, string>
	 */
	public function getDefaultHeaders(): array
	{
		return $this->default_headers;
	}

	public function getPathPrefix(): string
	{
		return $this->path_prefix;
	}
}
