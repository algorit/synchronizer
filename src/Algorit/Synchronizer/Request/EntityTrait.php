<?php namespace Algorit\Synchronizer\Request;

use Exception;

trait EntityTrait {

	protected $entity;

	public function setEntity($entity)
	{
		$this->entity = $entity;

		return $this;
	}

	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * Get the base name given an entity plural name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function getFromEntityName($name)
	{
		if( ! is_string($name))
		{
			throw new Exception('Wrong name format');
		}
		
		return ucfirst(strtolower(str_singular($name)));
	}
	
}