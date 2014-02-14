<?php namespace Algorit\Synchronizer\Request;

use App, Str, Log;
use Algorit\Synchronizer\Traits\EntityTrait;

class Repository {

	use EntityTrait;
	
	public function __construct($namespace)
	{
		$this->namespace = $namespace;
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
		$class = $this->namespace . '\\' . $this->getFromEntityName($entity);

		Log::notice('Loading repository ' . $class);

		return App::make($class);
	}

}