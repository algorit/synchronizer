<?php namespace Algorit\Synchronizer\Request\Contracts;

interface DispatcherInterface {

	public function getName();

	public function getRequestUrl();

	public function execute($requestMethod, $data = array(), $options = array());

}