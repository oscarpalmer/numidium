<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Test\Fake;

use oscarpalmer\Numidium\Controllers\ResourceController;
use oscarpalmer\Numidium\Http\HttpParameters;
use Psr\Http\Message\ServerRequestInterface;

final class Resource implements ResourceController
{
	public function create(ServerRequestInterface $request, HttpParameters $parameters)
	{
		return 'Create';
	}

	public function delete(ServerRequestInterface $request, HttpParameters $parameters)
	{
		return "Delete: {$parameters->getPath()->id}";
	}

	public function edit(ServerRequestInterface $request, HttpParameters $parameters)
	{
		return "Edit: {$parameters->getPath()->id}";
	}

	public function index(ServerRequestInterface $request, HttpParameters $parameters)
	{
		return 'Index';
	}

	public function read(ServerRequestInterface $request, HttpParameters $parameters)
	{
		return "Read: {$parameters->getPath()->id}";
	}

	public function update(ServerRequestInterface $request, HttpParameters $parameters)
	{
		return "Update: {$parameters->getPath()->id}";
	}
}
