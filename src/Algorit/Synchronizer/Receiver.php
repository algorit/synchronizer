<?php namespace Algorit\Synchronizer;

use Algorit\Synchronizer\Request\Contracts\RequestInterface;

class Receiver {

	/**
	 * Receive data from ERP
	 *
	 * @return void
	 */
	public function fromErp(RequestInterface $request, $entity, $lastSync)
	{
		return $request->receive($entity, $lastSync);
	}

	/**
	 * Get data from the Database
	 *
	 * @return void
	 */
	public function fromDatabase(RequestInterface $request, $entity, $lastSync)
	{
		return $request->getTransport()
					   ->callRepository($entity)
					   ->get($lastSync);
	}

	/**
	 * Receive data from a Device (Api)
	 *
	 * @return void
	 */
	public function fromApi($data)
	{
		return $data;
	}

}