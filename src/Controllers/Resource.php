<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Controllers;

use oscarpalmer\Numidium\Http\Parameters;
use Psr\Http\Message\ServerRequestInterface;

interface Resource
{
	/**
	 * Controller callback for responding to a POST-request for a resource
	 */
	public function create(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a DELETE-request for a resource
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 */
	public function delete(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a GET-request for editing a resource, i.e., showing an interface
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 */
	public function edit(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a GET-request for all resources
	 */
	public function index(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a GET-request for a resource
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 */
	public function read(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a PATCH-request for a resource
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 */
	public function update(ServerRequestInterface $request, Parameters $parameters);
}
