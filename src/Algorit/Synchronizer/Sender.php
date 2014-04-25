<?php namespace Algorit\Synchronizer;

use Closure;
use Algorit\Synchronizer\Request\RequestInterface;

class Sender {

	/**
	 * Send data to ERP
	 *
	 * @return void
	 */
	public function toErp(RequestInterface $request, $entity, Array $response)
	{
		if( ! $data = $this->parse($response))
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
		if( ! $data = $this->parse($response))
		{
			return $response;
		}

		return $request->getCaller()
					   ->repository($entity)
					   ->sync($data);
	}

	/**
	 * Send data to Device (Api)
	 *
	 * @return void
	 */
	public function toApi(Array $data, $parse = false)
	{
		if($parse instanceof Closure)
		{
			return $parse($data);
		}

		return $data;
	}

	/**
	 * Parse response array
	 *
	 * @param  array $response
	 * @return array
	 */	
	private function parse(Array $response)
	{
		$data = array_get($response, 'data');

		if( ! is_array($data) OR count($data) == 0)
		{
			return false;
		}

		return $data;
	}

}