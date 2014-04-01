<?php namespace Algorit\Synchronizer\Request;

use Algorit\Synchronizer\Request\Methods\MethodInterface;
use Algorit\Synchronizer\Request\Contracts\DispatcherInterface;

class Receiver implements DispatcherInterface {

	protected $methods = array('put', 'get', 'post', 'delete', 'patch');

	/**
	 * Create a new instance.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\RequestMethodInterface  $request
	 * @return instance
	 */
	public function __construct(MethodInterface $request)
	{
		$this->request = $request;
	}

	public function getName()
	{

	}

	public function getRequestUrl()
	{
		
	}

	public function execute($requestMethod, $data = array(), $options = array())
	{
		
	}

}