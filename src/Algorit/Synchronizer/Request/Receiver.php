<?php namespace Algorit\Synchronizer\Request;

use Algorit\Synchronizer\Request\Methods\MethodInterface;
use Algorit\Synchronizer\Request\Contracts\TransporterInterface;

class Receiver implements TransporterInterface {

	protected $methods = array('put', 'get', 'post', 'delete', 'patch');

	/**
	 * Create a new instance.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\RequestMethodInterface  $method
	 * @return instance
	 */
	public function __construct(MethodInterface $method)
	{
		$this->method = $method;
	}

	public function execute()
	{
		
	}

}