<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Http;

use oscarpalmer\Numidium\Routing\Item\Route;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

final class Parameters
{
	private ?string $fragment = null;
	private stdClass $path;
	private stdClass $query;

	/**
	 * @param array<string> $matches
	 */
	public function __construct(ServerRequestInterface $request, Route $route, array $matches)
	{
		$fragment = $request->getUri()->getFragment();

		if (is_string($fragment) && mb_strlen($fragment, 'utf-8') > 0) {
			$this->fragment = $fragment;
		}

		$this->buildPath($route->getPath(), $matches);
		$this->buildQuery($request->getQueryParams());
	}

	/**
	 * Fragment of URL, e.g. #fragment
	 */
	public function getFragment(): ?string
	{
		return $this->fragment;
	}

	/**
	 * Matched parameters in URL path
	 *
	 * e.g. '/:name' as route path will become 'getPath()->name' to retrieve the matched value in URL
	 */
	public function getPath(): stdClass
	{
		return $this->path;
	}

	/**
	 * Query of URL
	 *
	 * e.g. '?search=numidium' will become 'getQuery()->search' to retrieve the value 'numidium'
	 */
	public function getQuery(): stdClass
	{
		return $this->query;
	}

	private function addPathValue(string $key, string $value): void
	{
		$normalizedKey = ltrim($key, ':#');
		$normalizedValue = $this->getValue($value, false);

		if (! isset($this->path->{$normalizedKey})) {
			$this->path->{$normalizedKey} = $normalizedValue;

			return;
		}

		if (is_array($this->path->{$normalizedKey})) {
			$this->path->{$normalizedKey}[] = $normalizedValue;

			return;
		}

		$this->path->{$normalizedKey} = [$this->path->{$normalizedKey}, $normalizedValue];
	}

	/**
	 * @param array<string> $path
	 */
	private function buildPath(string $route, array $path): void
	{
		$this->path = new stdClass();

		array_shift($path);

		preg_match_all('/((?:#|:)[\w-]+|\*)/', $route, $keys);

		foreach ($keys[0] as $index => $key) {
			$this->addPathValue($key, $path[$index]);
		}
	}

	/**
	 * @param array<string|array<string>> $query
	 */
	private function buildQuery(array $query): void
	{
		$this->query = new stdClass();

		foreach ($query as $key => $value) {
			$this->query->{$key} = is_array($value)
				? array_map(function ($item) {
					return $this->getValue($item, true);
				}, $value)
				: $this->getValue($value, true);
		}
	}

	private function getValue(string $value, bool $canBeBool): bool|float|int|string
	{
		if (preg_match('/\A\d*\.\d+\z/', $value)) {
			return (float) $value;
		}

		if (preg_match('/\A\d+\z/', $value)) {
			return (int) $value;
		}

		if ($canBeBool && preg_match('/\Afalse|true\z/i', $value)) {
			return preg_match('/\Afalse\z/i', $value) ? false : true;
		}

		return $value;
	}
}
