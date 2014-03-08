<?php namespace Algorit\Synchronizer\Request;

use App, Str, Log;
use Algorit\Synchronizer\Traits\EntityTrait;

class Repository {

	use EntityTrait;
	
	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * Call a repository instance.
	 *
	 * @param  \Repositories\Interfaces\  $repositoryInterface
	 * @param  \Closure 				  $callback
	 * @return instance
	 */
	public function call($entity)
	{
		$class = $this->namespace . '\\Repositories\\' . $this->getFromEntityName($entity);

		Log::notice('Loading repository ' . $class);

		return App::make($class);
	}

}