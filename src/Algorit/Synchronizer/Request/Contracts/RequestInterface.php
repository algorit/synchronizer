<?php namespace Algorit\Synchronizer\Request\Contracts;

Interface RequestInterface {

	public function setConfig(Config $config);

	public function getConfig();

	public function setResource($resource);

	public function authenticate();

	public function setRequestOptions($entityName, $lastSync = false, $type = 'receive');

	public function executeRequest($auth = true);

	public function receive($entityName, $lastSync);

	public function send(Array $data, $entityName, $requestDate);
	
}

