<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Controllers;

use oscarpalmer\Numidium\Routing\Item\RouteItem;

final class ControllerCallback
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

	public function toRoute(): RouteItem
	{
		return new RouteItem('', sprintf('%s->%s', $this->class, $this->method), [], false);
	}
}
