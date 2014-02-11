<?php namespace Synchronizer;

use Str;
use Carbon\Carbon;
use Synchronizer\Contracts\SystemRequestInterface;

class Receiver {

	/**
	 * Receive data from ERP
	 *
	 * @return void
	 */
	public function fromErp(SystemRequestInterface $system, $entity, $lastSync)
	{
		return $system->receive($entity, $lastSync);
	}

	/**
	 * Get data from the Database
	 *
	 * @return void
	 */
	public function fromDatabase(SystemRequestInterface $system, $entity, $lastSync)
	{
		return $system->repository->call($entity)->get($lastSync);
	}

	/**
	 * Receive data from a Device (Api)
	 *
	 * @return void
	 */
	public function fromApi($data)
	{
		// No use for this... Yet. 
		// Update: Receive orders!
	}

}