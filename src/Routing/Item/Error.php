<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

use Closure;

final class Error
{
	use Response;

	public function __construct(private readonly int $status, private readonly string|Closure $callback)
	{
	}
}
