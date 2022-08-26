<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium;

final class Configuration
{
	/**
	 * @var array<string, int|string>
	 */
	private array $values = [
		'pathPrefix' => '',
	];

	/**
	 * @param array<string> $configuration
	 */
	public function __construct(array $configuration = [])
	{
		$this->values['pathPrefix'] = $this->getValidPathPrefix((string) ($configuration['pathPrefix'] ?? $this->values['pathPrefix']));
	}

	public function getPathPrefix(): string
	{
		return (string) $this->values['pathPrefix'];
	}

	private function getValidPathPrefix(string $prefix): string
	{
		$prefix = trim($prefix, '/');

		if (mb_strlen($prefix, 'utf-8') === 0) {
			return $prefix;
		}

		return "/{$prefix}";
	}
}
