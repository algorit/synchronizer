<?php namespace Algorit\Synchronizer\Request;

use App, Str, Log;
use Synchronizer\Exceptions\RepositoryException;

class Repository {

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

	/**
	 * Get the repository name from the plural entity name.
	 *
	 * @param  $name
	 * @return string
	 */
	private function getFromEntityName($name)
	{
		if( ! is_string($name))
		{
			throw new RepositoryException('Wrong name format');
		}

		return ucfirst(strtolower(Str::singular($name)));
	}

}