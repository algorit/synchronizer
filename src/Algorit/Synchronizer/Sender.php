<?php namespace Synchronizer;

use Str, Closure;
use Synchronizer\Contracts\SystemRequestInterface;

class Sender {

	/**
	 * Send data to ERP
	 *
	 * @return void
	 */
	public function toErp(SystemRequestInterface $system, $entity, Array $response)
	{
		$data = array_get($response, 'data');

		if( ! is_array($data) OR count($data) == 0)
		{
			return $response;
		}

		return $system->send($entity, $data);
	}

	/**
	 * Insert data into Database
	 *
	 * @return void
	 */
	public function toDatabase(SystemRequestInterface $system, $entity, Array $response)
	{
		$data = array_get($response, 'data');

		// debug($data);

		if( ! is_array($data) OR count($data) == 0)
		{
			return $response;
		}

		return $system->getRepository()->call($entity)->set($data); // Change function name later...
	}

	/**
	 * Send data to Device (Api)
	 *
	 * @return void
	 */
	public function toApi(Array $data, $parse = false)
	{
		// Since it's an API we will just return it. (It could've been parsed!)

		if($parse instanceof Closure)
		{
			return $parse($data);
		}

		return $data;
	}

}