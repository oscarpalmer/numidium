<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

use Closure;

abstract class Basic
{
	/**
	 * @param array<string|Closure> $middleware
	 */
	public function __construct(protected readonly int $status, protected readonly string|null $path, protected readonly string|Closure $callback, protected readonly array $middleware)
	{
	}

	public function getCallback(): string|Closure
	{
		return $this->callback;
	}

	/**
	 * @return array<string|Closure>
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
