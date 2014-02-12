<?php namespace Algorit\Synchronizer\Tests\Stubs;

use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Request as AbstractRequest;

class Request extends AbstractRequest implements RequestInterface {

	public function authenticate(){}

	public function executeRequest($auth = true){}

	public function receive($entityName, $lastSync){}

	public function send(Array $data, $entityName, $requestDate){}

}