<?php namespace Algorit\Synchronizer\Request\Contracts;

use Algorit\Synchronizer\Request\Config;

Interface SystemInterface {

	public function setResource($resource);

	public function getResource();

	public function setConfig(Config $config);

	public function getConfig();
}