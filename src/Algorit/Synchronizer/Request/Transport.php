<?php namespace Algorit\Synchronizer\Request;

use Algorit\Synchronizer\Container;
use Illuminate\Filesystem\Filesystem;

class Transport {

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
	 * @return 
	 */
	public function __construct(Container $container, Receiver $receiver, Sender $sender)
	{
		// $this->files = $files;
		$this->container = $container;
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

	/**
	 * Call a parser instance and set the aliases.
	 *
	 * @param  string $name
	 * @param  array  $alias
	 * @return \Algorit\Synchronizer\Request\Contracts\ParserInterface
	 */
	public function callParser($name, Array $alias)
	{
		$class = $this->container->getNamespace() . '\\Parsers\\' . $this->getFromEntityName($name);

		return $this->container->make($class)->setAliases($alias);
	}
	
	/**
	 * Call a repository instance.
	 *
	 * @param  string $name
	 * @return \Algorit\Synchronizer\Request\Contracts\RepositoryInterface
	 */
	public function callRepository($name)
	{
		$class = $this->container->getNamespace() . '\\Repositories\\' . $this->getFromEntityName($name);

		return $this->container->make($class);
	}

}