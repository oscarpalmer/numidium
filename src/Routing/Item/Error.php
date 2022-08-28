<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

use Closure;
use oscarpalmer\Numidium\Psr\RequestHandler;

final class Error extends RequestHandler
{
	public function __construct(int $status, string|Closure $callback)
	{
		parent::__construct($status, null, $callback);
	}
}
