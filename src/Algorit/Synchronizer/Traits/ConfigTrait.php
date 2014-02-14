<?php namespace Algorit\Synchronizer\Traits;

use Algorit\Synchronizer\Request\Config;

trait ConfigTrait {

	protected $config;

	public function setConfig(Config $config)
	{
		$this->config = $config;

		return $this;
	}

	public function getConfig()
	{
		return $this->config;
	}

}