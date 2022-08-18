<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

final class Error
{
	use Response;

	/**
	 * @param array<string>|string|callable $callback
	 */
	public function __construct(private readonly int $status, private readonly mixed $callback)
	{
	}
}
