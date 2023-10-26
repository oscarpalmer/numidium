<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test;

use oscarpalmer\Numidium\Exception\ErrorException;
use oscarpalmer\Numidium\Exception\ResponseException;
use oscarpalmer\Numidium\Http\HttpResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ExceptionTest extends TestCase
{
	public function testError(): void
	{
		$error = new ErrorException(1234, 'Test');

		$this->assertInstanceOf(ErrorException::class, $error);
		$this->assertSame(1234, $error->getStatus());
		$this->assertSame('Test', $error->getData());
	}

	public function testResponse(): void
	{
		$response = new ResponseException(HttpResponse::ok('Test'));

		$this->assertInstanceOf(ResponseException::class, $response);
		$this->assertInstanceOf(ResponseInterface::class, $response->getResponse());
	}
}
