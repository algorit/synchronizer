<?php namespace Algorit\Synchronizer\Request\Dispatchers;

interface DispatcherInterface {

	public function getName();

	public function getRequestUrl();

	public function dispatch($requestMethod, $data = array(), $options = array());

}