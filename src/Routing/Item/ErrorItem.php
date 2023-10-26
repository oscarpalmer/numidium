<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

final class ErrorItem extends BasicRoutingItem
{
	/**
	 * @param array<callable|string> $middleware
	 */
	public function __construct(int $status, callable|string $callback, array $middleware)
	{
		parent::__construct($status, null, $callback, $middleware);
	}
}
