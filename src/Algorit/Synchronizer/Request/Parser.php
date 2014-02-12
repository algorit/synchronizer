<?php namespace Algorit\Synchronizer\Request;

use App, Str, Log;
use Event, Closure;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Traits\ConfigTrait;
use Algorit\Synchronizer\Request\Exceptions\ParserException;
use Algorit\Synchronizer\Request\Contracts\SystemParserInterface;

class Parser {

	use ConfigTrait;

	/**
	 * The repository instance
	 *
	 * @var \Services\Sync\Erps\
	 */
	protected $repository;

	/**
	 * The filesystem instance
	 *
	 * @var \Services\Sync\Erps\Filesystem
	 */
	protected $files;

	/**
	 * The config instance
	 *
	 * @var \Services\Sync\Config
	 */
	protected $config;

	/**
	 * Create a new instance.
	 *
	 * @param  \Repositories\   $repository
	 * @param  \Repositories\   $files
	 * @return instance
	 */
	public function __construct(Filesystem $files, $namespace)
	{
		$this->namespace = $namespace;
		$this->files = $files;
	}

	/**
	 * Call a parser instance and set the aliases.
	 *
	 * @param  \Repositories\Interfaces\  $name
	 * @param  \Closure 				  $callback
	 * @return instance
	 */
	public function call($name)
	{
		$class = 'Synchronizer\Systems\Jjw\Parsers\\' . $this->getFromEntityName($name);

		Log::notice('Loading parser ' . $class);

		$parser = App::make($class);

		$aliases = array_get($this->config->aliases, $name);

		$parser->setAliases($aliases);

		return $parser;
	}

	/**
	 * Get the repository name from the plural entity name.
	 *
	 * @param  $entityName
	 * @return string
	 */
	private function getFromEntityName($name)
	{
		if( ! is_string($name))
		{
			throw new ParserException('Wrong name format');
		}
		
		return ucfirst(strtolower(Str::singular($name)));
	}

}