<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

final class Route
{
	use Response;

	private readonly int $status;

	/**
	 * @param array<string>|string|callable $callback
	 */
	public function __construct(private readonly string $path, private readonly mixed $callback)
	{
		$this->status = 200;
	}

	public function getPath(): string
	{
		return $this->path;
	}
}
