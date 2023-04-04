<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Controllers;

use oscarpalmer\Numidium\Routing\Item\Route;

final class Callback
{
	private string $class;

	private string $method;

	public function __construct(string $class, string $method)
	{
		$this->class = $class;
		$this->method = $method;
	}

	public function getClass(): string
	{
		return $this->class;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function toRoute(): Route
	{
		return new Route('', sprintf('%s->%s', $this->class, $this->method), []);
	}
}
