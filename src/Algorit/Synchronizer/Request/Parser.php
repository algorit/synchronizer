<?php namespace Algorit\Synchronizer\Request;

use Algorit\Synchronizer\Container;
use Illuminate\Filesystem\Filesystem;

class Parser {

	use EntityTrait;
	
	/**
	 * The filesystem instance
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new instance.
	 *
	 * @param  \Repositories\   $repository
	 * @param  \Repositories\   $files
	 * @return 
	 */
	public function __construct(Filesystem $files, Container $container)
	{
		$this->files = $files;
		$this->container = $container;
	}

	public function setConfig(Config $config)
	{
		$this->config = $config;

		return $this;
	}

	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Call a parser instance and set the aliases.
	 *
	 * @param  \Repositories\Interfaces\  $name
	 * @param  \Closure 				  $callback
	 * @return instance
	 */
	public function call($name, Array $alias)
	{
		$class  = $this->container->getNamespace() . '\\Parsers\\' . $this->getFromEntityName($name);

		$parser = $this->container->make($class);
		$parser->setAliases($alias);

		return $parser;
	}

}