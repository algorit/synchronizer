<?php namespace Algorit\Synchronizer\Request;

use Algorit\Synchronizer\Container;
use Illuminate\Filesystem\Filesystem;

class Transport {

	use EntityTrait;
	
	/**
	 * The filesystem instance
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The container instance
	 *
	 * @var \Algorit\Synchronizer\Container
	 */
	protected $container;

	/**
	 * Create a new instance.
	 *
	 * @param  \Algorit\Synchronizer\Container   $container
	 * @param  \Illuminate\Filesystem\Filesystem $files
	 * @return 
	 */
	public function __construct(Container $container, Filesystem $files)
	{
		$this->files = $files;
		$this->container = $container;
	}

	/**
	 * Call a parser instance and set the aliases.
	 *
	 * @param  \Repositories\Interfaces\  $name
	 * @param  \Closure 				  $callback
	 * @return instance
	 */
	public function callParser($name, Array $alias)
	{
		$class = $this->container->getNamespace() . '\\Parsers\\' . $this->getFromEntityName($name);

		return $this->container->make($class)->setAliases($alias);
	}
	
	/**
	 * Call a repository instance.
	 *
	 * @param  \Repositories\Interfaces\  $repositoryInterface
	 * @param  \Closure 				  $callback
	 * @return instance
	 */
	public function callRepository($entity)
	{
		$class = $this->container->getNamespace() . '\\Repositories\\' . $this->getFromEntityName($entity);

		return $this->container->make($class);
	}

}