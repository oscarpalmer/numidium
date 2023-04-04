<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Configuration;

use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\BooleanArgument;
use League\Container\Argument\Literal\CallableArgument;
use League\Container\Argument\Literal\FloatArgument;
use League\Container\Argument\Literal\IntegerArgument;
use League\Container\Argument\Literal\ObjectArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;

final class Dependencies
{
	public function __construct(private readonly Container $container)
	{
	}

	/**
	 * Add an array as a dependency
	 *
	 * @param array<mixed> $value
	 */
	public function array(string $name, array $value): Dependencies
	{
		$this->container->add($name, new ArrayArgument($value));

		return $this;
	}

	/**
	 * Add a boolean value as a dependency
	 */
	public function boolean(string $name, bool $value): Dependencies
	{
		$this->container->add($name, new BooleanArgument($value));

		return $this;
	}

	/**
	 * Add a callable as a dependency
	 *
	 * @param string $names Names of other dependencies to be used by callable
	 */
	public function callable(string $name, callable $value, string ...$names): Dependencies
	{
		$this->container->add($name, new CallableArgument($value))->addArguments($names);

		return $this;
	}

	/**
	 * Add a class as a dependency
	 *
	 * @param string $name Name of class, e.g. MyClass::class
	 * @param string $names Names of other dependencies to be injected in constructor
	 */
	public function class(string $name, string ...$names): Dependencies
	{
		$this->container->add($name)->addArguments($names);

		return $this;
	}

	/**
	 * Add a float as a dependency
	 */
	public function float(string $name, float $value): Dependencies
	{
		$this->container->add($name, new FloatArgument($value));

		return $this;
	}

	/**
	 * Add an integer as a dependency
	 */
	public function integer(string $name, int $value): Dependencies
	{
		$this->container->add($name, new IntegerArgument($value));

		return $this;
	}

	/**
	 * Add a number - float or integer - as a dependency
	 */
	public function number(string $name, int|float $value): Dependencies
	{
		return is_float($value)
			? $this->float($name, $value)
			: $this->integer($name, $value);
	}

	/**
	 * Add an object as a dependency
	 */
	public function object(string $name, object $value): Dependencies
	{
		$this->container->add($name, new ObjectArgument($value));

		return $this;
	}

	/**
	 * Add a string as a dependency
	 */
	public function string(string $name, string $value): Dependencies
	{
		$this->container->add($name, new StringArgument($value));

		return $this;
	}
}
