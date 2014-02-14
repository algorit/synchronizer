<?php namespace Algorit\Synchronizer\Traits;

use Algorit\Synchronizer\Request\Contracts\ResourceInterface;

trait ResourceTrait {

	public function setResource(ResourceInterface $resource)
	{
		$this->resource = $resource;

		return $this;
	}

	public function getResource()
	{
		return $this->resource;
	}
	
}