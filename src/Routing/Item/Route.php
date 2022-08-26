<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Routing\Item;

final class Route
{
	use Response;

	private const EXPRESSION_PATTERNS = ['/\A\/*/', '/\/*\z/', '/\//', '/\./', '/\((.*?)\)/', '/\*/', '/#([\w-]+)/', '/:([\w-]+)/'];
	private const EXPRESSION_REPLACEMENTS = ['/', '/?', '\/', '\.', '(?:\\1)?', '(.*?)', '(\d+)',  '([\w-]+)'];

	private readonly int $status;

	public function __construct(private readonly string $path, private readonly mixed $callback)
	{
		$this->status = 200;
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
