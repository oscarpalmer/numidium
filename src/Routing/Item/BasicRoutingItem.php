<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

abstract class BasicRoutingItem
{
	/**
	 * @param array<callable|string> $middleware
	 */
	public function __construct(
		protected readonly int $status,
		protected readonly string|null $path,
		protected readonly mixed $callback,
		protected readonly array $middleware
	) {
	}

	public function getCallback(): mixed
	{
		return $this->callback;
	}

	/**
	 * @return array<callable|string>
	 */
	public function getMiddleware(): array
	{
		return $this->middleware;
	}

	public function getPath(): string
	{
		return $this->path ?? '';
	}

	public function getStatus(): int
	{
		return $this->status;
	}
}
