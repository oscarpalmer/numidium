<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

use Closure;
use oscarpalmer\Numidium\Psr\RequestHandler;

final class Route extends RequestHandler
{
	private const EXPRESSION_PATTERNS = ['/\A\/*/', '/\/*\z/', '/\//', '/\./', '/\((.*?)\)/', '/\*/', '/#([\w-]+)/', '/:([\w-]+)/'];
	private const EXPRESSION_REPLACEMENTS = ['/', '/?', '\/', '\.', '(?:\\1)?', '(.*?)', '(\d+)',  '([\w-]+)'];

	public function __construct(string $path, string|Closure $callback)
	{
		parent::__construct(200, $path, $callback);
	}

	public function getExpression(): string
	{
		return sprintf('/\A%s\z/i', preg_replace(self::EXPRESSION_PATTERNS, self::EXPRESSION_REPLACEMENTS, $this->path));
	}

	public function getPath(): string
	{
		return $this->path;
	}
}
