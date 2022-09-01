<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

use Closure;

final class Error extends Basic
{
	/**
	 * @param array<string|Closure> $middleware
	 */
	public function __construct(int $status, string|Closure $callback, array $middleware)
	{
		parent::__construct($status, null, $callback, $middleware);
	}
}
