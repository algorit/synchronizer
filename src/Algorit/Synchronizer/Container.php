<?php namespace Algorit\Synchronizer;

use Illuminate\Container\Container as IlluminateContainer;

class Container extends IlluminateContainer {

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