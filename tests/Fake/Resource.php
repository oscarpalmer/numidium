<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Fake;

use oscarpalmer\Numidium\Controllers\Resource as NumidiumResource;
use oscarpalmer\Numidium\Http\Parameters;
use Psr\Http\Message\ServerRequestInterface;

final class Resource implements NumidiumResource
{
	public function create(ServerRequestInterface $request, Parameters $parameters)
	{
		return 'Create';
	}

	public function delete(ServerRequestInterface $request, Parameters $parameters)
	{
		return "Delete: {$parameters->getPath()->id}";
	}

	public function edit(ServerRequestInterface $request, Parameters $parameters)
	{
		return "Edit: {$parameters->getPath()->id}";
	}

	public function index(ServerRequestInterface $request, Parameters $parameters)
	{
		return 'Index';
	}

	public function read(ServerRequestInterface $request, Parameters $parameters)
	{
		return "Read: {$parameters->getPath()->id}";
	}

	public function update(ServerRequestInterface $request, Parameters $parameters)
	{
		return "Update: {$parameters->getPath()->id}";
	}
}
