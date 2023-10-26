<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;

final class ResponseException extends Exception
{
	public function __construct(private readonly ResponseInterface $response)
	{
	}

	public function getResponse(): ResponseInterface
	{
		return $this->response;
	}
}
