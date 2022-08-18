<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

final class Parameters
{
	/**
	 * @param array<string> $matches
	 */
	public function __construct(string $route, array $matches)
	{
		array_shift($matches);

		preg_match_all('/((?:#|:)[\w-]+|\*)/', $route, $keys);

		foreach ($keys[0] as $index => $key) {
			$this->setParameter($key, $matches[$index]);
		}
	}

	private function setParameter(string $key, string $value): void
	{
		if (str_starts_with($key, '#')) {
			$value = (int) $value;
		}

		if ($key !== '*') {
			$key = ltrim($key, '#:');
		}

		if (! isset($this->{$key})) {
			$this->{$key} = $value;

			return;
		}

		if (is_array($this->{$key})) {
			$this->{$key}[] = $value;

			return;
		}

		$this->{$key} = [$this->{$key}, $value];
	}
}
