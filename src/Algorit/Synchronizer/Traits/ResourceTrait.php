<?php namespace Algorit\Synchronizer\Traits;

trait ResourceTrait {

	public function setResource($resource)
	{
		$this->resource = $resource;

		return $this;
	}

	public function getResource()
	{
		return $this->resource;
	}
	
}