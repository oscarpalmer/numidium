<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

final class Route extends Basic
{
	private const EXPRESSION_PATTERNS = ['/\A\/*/', '/\/*\z/', '/\//', '/\./', '/\((.*?)\)/', '/\*/', '/#([\w-]+)/', '/:([\w-]+)/'];
	private const EXPRESSION_REPLACEMENTS = ['/', '/?', '\/', '\.', '(?:\\1)?', '(.*?)', '(\d+)', '([\w-]+)'];

	/**
	 * @param array<callable|string> $middleware
	 */
	public function __construct(string $path, callable|string $callback, array $middleware)
	{
		parent::__construct(200, $path, $callback, $middleware);
	}

	public function getExpression(): string
	{
		return sprintf('/\A%s\z/i', preg_replace(self::EXPRESSION_PATTERNS, self::EXPRESSION_REPLACEMENTS, $this->path ?? ''));
	}
}
