<?php namespace Algorit\Synchronizer\Request;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Request\Contracts\ParserInterface;
use Algorit\Synchronizer\Request\Exceptions\ParserException;
use Algorit\Synchronizer\Request\Contracts\RepositoryInterface;
use Algorit\Synchronizer\Request\Exceptions\RepositoryException;

class Caller {

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
	public function __construct(Container $container)
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
	public function parser($name, Array $alias)
	{
		$parser = $this->container->make($this->getClass('Parsers', $name));

		if( ! $parser instanceof ParserInterface)
		{
			throw new ParserException('Parser must implement \Algorit\Synchronizer\Request\Contracts\ParserInterface');
		}
		
		return $parser->setAliases($alias);
	}

	/**
	 * Call a repository instance.
	 *
	 * @param  string $name
	 * @return \Algorit\Synchronizer\Request\Contracts\RepositoryInterface
	 */
	public function repository($name)
	{
		$repository = $this->container->make($this->getClass('Repositories', $name));

		if( ! $repository instanceof RepositoryInterface)
		{
			throw new RepositoryException('Repository must implement \Algorit\Synchronizer\Request\Contracts\RepositoryInterface');
		}

		return $repository;
	}

	/**
	* Get the class name
	*
	* @param  string  $type
	* @param  string  $name
	* @return string
	*/
	public function getClass($type, $name)
	{
		return $this->container->namespace . '\\' . $type . '\\' . $this->getFromEntityName($name);
	}

}
