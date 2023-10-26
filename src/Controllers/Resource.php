<?php

declare(strict_types=1);

namespace oscarpalmer\Numidium\Controllers;

use oscarpalmer\Numidium\Http\Parameters;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

interface Resource
{
	/**
	 * Controller callback for responding to a POST-request for a resource
	 *
	 * @return resource|scalar|StreamInterface
	 */
	public function create(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a POST-request for removing a resource
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 *
	 * @return resource|scalar|StreamInterface
	 */
	public function delete(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a GET-request for editing a resource, i.e., showing an interface
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 *
	 * @return resource|scalar|StreamInterface
	 */
	public function edit(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a GET-request for all resources
	 *
	 * @return resource|scalar|StreamInterface
	 */
	public function index(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a GET-request for a resource
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 *
	 * @return resource|scalar|StreamInterface
	 */
	public function read(ServerRequestInterface $request, Parameters $parameters);

	/**
	 * Controller callback for responding to a POST-request for updating a resource
	 *
	 * Use the parameters-object for accessing the resource ID, i.e., '$parameters->getPath()->id'
	 *
	 * @return resource|scalar|StreamInterface
	 */
	public function update(ServerRequestInterface $request, Parameters $parameters);
}
