<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test;

use oscarpalmer\Numidium\Exception\Error;
use oscarpalmer\Numidium\Exception\Response;
use oscarpalmer\Numidium\Http\Response as HttpResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ExceptionTest extends TestCase
{
	public function testError(): void
	{
		$error = new Error(1234, 'Test');

		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame(1234, $error->getStatus());
		$this->assertSame('Test', $error->getData());
	}

	public function testResponse(): void
	{
		$response = new Response(HttpResponse::ok('Test'));

		$this->assertInstanceOf(Response::class, $response);
		$this->assertInstanceOf(ResponseInterface::class, $response->getResponse());
	}
}
