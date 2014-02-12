<?php namespace Algorit\Synchronizer\Tests;

use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Request as AbstractRequest;
use Algorit\Synchronizer\Request\Exceptions\RequestException;
use Algorit\Synchronizer\Methods\MethodInterface as RequestMethod;

class RequestExample extends AbstractRequest implements RequestInterface {

	public function authenticate(){}

	public function executeRequest($auth = true){}

	public function receive($entityName, $lastSync){}

	public function send(Array $data, $entityName, $requestDate){}

}