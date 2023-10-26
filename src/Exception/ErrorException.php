<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Exception;

use Exception;

final class ErrorException extends Exception
{
	/**
	 * @param int $status Status code for error
	 * @param mixed $data Optional data object, available as the second parameter in callbacks
	 */
	public function __construct(private readonly int $status, private readonly mixed $data = null)
	{
	}

	public function getData(): mixed
	{
		return $this->data;
	}

	public function getStatus(): int
	{
		return $this->status;
	}
}
