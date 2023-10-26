<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Configuration;

use Exception;
use LogicException;
use oscarpalmer\Numidium\Configuration\Configuration;
use oscarpalmer\Numidium\Configuration\Validator;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ConfigurationTest extends TestCase
{
	public function testDefaults(): void
	{
		$configuration = new Configuration();

		$this->assertSame('', $configuration->getControllerPrefix());
		$this->assertSame('home', $configuration->getDefaultController());
		$this->assertSame('', $configuration->getPathPrefix());

		$this->assertSame([
			'content-type' => 'text/html; charset=utf-8',
		], $configuration->getDefaultHeaders());
	}

	public function testControllerPrefix(): void
	{
		try {
			new Configuration([
				'controller_prefix' => 123,
			]);
		} catch (Exception $exception) {
			$this->assertInstanceOf(LogicException::class, $exception);
		}

		$emptyPrefix = new Configuration(['controller_prefix' => '']);
		$realPrefix = new Configuration(['controller_prefix' => 'test']);

		$this->assertSame('', $emptyPrefix->getControllerPrefix());
		$this->assertSame('test\\', $realPrefix->getControllerPrefix());
	}

	public function testDefaultController(): void
	{
		try {
			new Configuration([
				'default_controller' => 123,
			]);
		} catch (Exception $exception) {
			$this->assertInstanceOf(LogicException::class, $exception);
		}

		$configuration = new Configuration(['default_controller' => 'test']);

		$this->assertSame('test', $configuration->getDefaultController());
	}

	public function testHeaders(): void
	{
		$values = [
			123,
			new stdClass(),
			[123 => []],
			['content-type' => true],
			['content-type' => [true]],
		];

		foreach ($values as $value) {
			try {
				new Configuration([
					'default_headers' => $value,
				]);
			} catch (Exception $exception) {
				$this->assertInstanceOf(LogicException::class, $exception);
			}
		}

		$values = [
			'content-length' => 0,
			'content-type' => ['text/plain'],
		];

		$configuration = new Configuration([
			'default_headers' => $values,
		]);

		$this->assertSame($values, $configuration->getDefaultHeaders());
	}

	public function testPathPrefix(): void
	{
		try {
			new Configuration(['path_prefix' => 123]);
		} catch (Exception $exception) {
			$this->assertInstanceOf(LogicException::class, $exception);
		}

		$emptyPrefix = new Configuration(['path_prefix' => '']);
		$realPrefix = new Configuration(['path_prefix' => 'test']);

		$this->assertSame('', $emptyPrefix->getPathPrefix());
		$this->assertSame('/test', $realPrefix->getPathPrefix());
	}

	public function testUnknown(): void
	{
		$this->assertSame('unknown_configuration_value', (new Validator())->validate('unknown_configuration_key', 'unknown_configuration_value'));
	}
}
