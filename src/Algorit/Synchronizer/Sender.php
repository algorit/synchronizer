<?php namespace Algorit\Synchronizer;

use Closure;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;

class Sender {

	/**
	 * Send data to ERP
	 *
	 * @return void
	 */
	public function toErp(RequestInterface $request, $entity, Array $response)
	{
		$data = array_get($response, 'data');

		if( ! is_array($data) OR count($data) == 0)
		{
			return $response;
		}

		return $request->send($entity, $data);
	}

	/**
	 * Insert data into Database
	 *
	 * @return void
	 */
	public function toDatabase(RequestInterface $request, $entity, Array $response)
	{
		$data = array_get($response, 'data');

		if( ! is_array($data) OR count($data) == 0)
		{
			return $response;
		}

		return $request->getTransport()
					   ->callRepository($entity)
					   ->set($data); // Change function name.
	}

	/**
	 * Send data to Device (Api)
	 *
	 * @return void
	 */
	public function toApi(Array $data, $parse = false)
	{
		// Since it's an API we will just return it.
		if($parse instanceof Closure)
		{
			return $parse($data);
		}

		return $data;
	}

}