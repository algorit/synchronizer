<?php namespace Algorit\Synchronizer;

use Illuminate\Container\Container as IlluminateContainer;
use Algorit\Synchronizer\Request\Contracts\ContainerInterface;

class Container extends IlluminateContainer implements ContainerInterface {

	private $namespace;

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

}