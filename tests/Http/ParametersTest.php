<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Uri;
use Nyholm\Psr7Server\ServerRequestCreator;
use oscarpalmer\Numidium\Http\HttpParameters;
use oscarpalmer\Numidium\Routing\Item\RouteItem;
use PHPUnit\Framework\TestCase;

final class ParametersTest extends TestCase
{
	public function test(): void
	{
		$factory = new Psr17Factory();
		$creator = new ServerRequestCreator($factory, $factory, $factory, $factory);

		$request = $creator->fromGlobals();

		$uri = new Uri('http://example.com/prefix/a1/b/1234/c/a3/#fragment');

		$request = $request->withUri($uri);

		$request = $request->withQueryParams([
			'a' => '1234',
			'b' => 'true',
			'c' => ['x', '12.34', 'z'],
		]);

		$parameters = new HttpParameters(
			$request,
			new RouteItem('/prefix/:a/:b/#a/:c/:a', function () {
			}, [], false),
			['/prefix/a1/b/1234/c/a3', 'a1', 'b', '1234', 'c', 'a3'],
		);

		$this->assertSame('fragment', $parameters->getFragment());

		$this->assertIsArray($parameters->getPath()->a);
		$this->assertSame('a1', $parameters->getPath()->a[0]);
		$this->assertSame(1234, $parameters->getPath()->a[1]);
		$this->assertSame('a3', $parameters->getPath()->a[2]);
		$this->assertSame('b', $parameters->getPath()->b);
		$this->assertSame('c', $parameters->getPath()->c);

		$this->assertSame(1234, $parameters->getQuery()->a);
		$this->assertSame(true, $parameters->getQuery()->b);
		$this->assertIsArray($parameters->getQuery()->c);
		$this->assertSame('x', $parameters->getQuery()->c[0]);
		$this->assertSame(12.34, $parameters->getQuery()->c[1]);
		$this->assertSame('z', $parameters->getQuery()->c[2]);
	}
}
