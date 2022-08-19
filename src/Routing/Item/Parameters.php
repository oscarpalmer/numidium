<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

final class Parameters
{
	/**
	 * @var array<array<bool|int|string>|bool|int|string>
	 */
	private array $url = [];

	/**
	 * @param array<string> $matches
	 */
	public function __construct(string $route, array $matches)
	{
		array_shift($matches);

		preg_match_all('/((?:#|:)[\w-]+|\*)/', $route, $keys);

		foreach ($keys[0] as $index => $key) {
			$this->addParameter(ltrim($key, '#:'), $this->getValue($matches[$index]));
		}
	}

	public function __get(int|string $name): mixed
	{
		return $this->url[$name];
	}

	private function addParameter(string $key, bool|int|string $value): void
	{
		if (! isset($this->url[$key])) {
			$this->url[$key] = $value;

			return;
		}

		if (is_array($this->url[$key])) {
			$this->url[$key][] = $value;

			return;
		}

		$this->url[$key] = [$this->url[$key], $value];
	}

	private function getValue(string $value): bool|int|string
	{
		if (preg_match('/\A\d+\z/', $value)) {
			return (int) $value;
		}

		if (preg_match('/\A(false|true)\z/i', $value)) {
			return preg_match('/\Afalse\z/i', $value) ? false : true;
		}

		return $value;
	}
}
