<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Http;

use Exception;
use InvalidArgumentException;
use Nyholm\Psr7\Stream;
use oscarpalmer\Numidium\Http\HttpResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class ResponseTest extends TestCase
{
	public function testStatuses(): void
	{
		$bodies = [
			200 => 'Test',
			400 => 1234,
			404 => true,
			500 => Stream::create('Test'),
		];

		$methods = [
			200 => 'ok',
			400 => 'badRequest',
			404 => 'notFound',
			500 => 'serverError',
		];

		foreach ($methods as $status => $method) {
			$callback = [HttpResponse::class, $method];

			if (! is_callable($callback)) {
				continue;
			}

			$response = call_user_func($callback, $bodies[$status]);

			$this->assertInstanceOf(ResponseInterface::class, $response);
			$this->assertSame($status, $response->getStatusCode());
		}
	}

	public function testJson(): void
	{
		$value = [
			'test' => 1234,
		];

		$body_1 = '{"test":1234}';
		$body_2 = '{
    "test": 1234
}';

		$response_1 = HttpResponse::json(200, $value);
		$response_2 = HttpResponse::json(200, $value, options: false);
		$response_3 = HttpResponse::json(200, $value, options: true);

		$this->assertSame($body_1, (string) $response_1->getBody());
		$this->assertSame($body_1, (string) $response_2->getBody());
		$this->assertSame($body_2, (string) $response_3->getBody());
	}

	public function testGetBody(): void
	{
		try {
			/** @phpstan-ignore-next-line */
			HttpResponse::ok([]);
		} catch (Exception $exception) {
			$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		}
	}
}
