<?php namespace Algorit\Synchronizer\Request;

use App, Str, Log;
use Event, Closure;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Traits\EntityTrait;
use Algorit\Synchronizer\Traits\ConfigTrait;
use Algorit\Synchronizer\Request\Exceptions\ParserException;
use Algorit\Synchronizer\Request\Contracts\SystemParserInterface;

class Parser {

	use EntityTrait;
	use ConfigTrait;
	
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
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
		// $this->namespace = $namespace;
	}

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

	public function setFilesystem(Filesystem $files)
	{
		$this->files = $files;
	}

	public function getFilesystem()
	{
		return $this->files;
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
		$class = $this->namespace . '\\Parsers\\' . $this->getFromEntityName($name);

		Log::notice('Loading parser ' . $class);

		$parser = App::make($class);
		$parser->setAliases($alias);

		return $parser;
	}

}