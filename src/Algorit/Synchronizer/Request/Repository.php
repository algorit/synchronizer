<?php namespace Algorit\Synchronizer\Request;

use Algorit\Synchronizer\Container;

class Repository {

	use EntityTrait;
	
	public function __construct(Container $container)
	{
		$this->container = $container;
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
		$class = $this->container->namespace . '\\Repositories\\' . $this->getFromEntityName($entity);

		return $this->container->make($class);
	}

}