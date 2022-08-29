<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium;

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

	public function array(string $name, array $value): Dependencies
	{
		$this->container->add($name, new ArrayArgument($value));

		return $this;
	}

	public function boolean(string $name, bool $value): Dependencies
	{
		$this->container->add($name, new BooleanArgument($value));

		return $this;
	}

	public function callable(string $name, callable $value, string ...$arguments): Dependencies
	{
		$this->container->add($name, new CallableArgument($value))->addArguments($arguments);

		return $this;
	}

	public function class(string $name, string ...$arguments): Dependencies
	{
		$this->container->add($name)->addArguments($arguments);

		return $this;
	}

	public function float(string $name, float $value): Dependencies
	{
		$this->container->add($name, new FloatArgument($value));

		return $this;
	}

	public function instance(string $name, string ...$arguments): Dependencies
	{
		$this->container->add($name)->addArguments($arguments);

		return $this;
	}

	public function integer(string $name, int $value): Dependencies
	{
		$this->container->add($name, new IntegerArgument($value));

		return $this;
	}

	public function number(string $name, int|float $value): Dependencies
	{
		return is_float($value)
			? $this->float($name, $value)
			: $this->integer($name, $value);
	}

	public function object(string $name, object $value): Dependencies
	{
		$this->container->add($name, new ObjectArgument($value));

		return $this;
	}

	public function string(string $name, string $value): Dependencies
	{
		$this->container->add($name, new StringArgument($value));

		return $this;
	}
}
