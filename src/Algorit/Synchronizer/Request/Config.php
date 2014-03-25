<?php namespace Algorit\Synchronizer\Request;

use Str;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;

class Config {
	
	/**
	 * The config array.
	 *
	 * @var array
	 */
	public $config;

	/**
	 * The aliases array.
	 *
	 * @var array
	 */
	public $aliases;

	/**
	 * The date config.
	 *
	 * @var array
	 */
	public $date;

	/**
	 * The resource instance name.
	 *
	 * @var array
	 */
	public $resourceInstance;

	/**
	 * The entities config.
	 *
	 * @var array
	 */
	protected $entities;

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * Create a new instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * Setup the configuration.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\SystemInterface $system
	 * @return \Algorit\Synchronizer\Request\Config
	 */
	public function setup(SystemInterface $system, $resource)
	{
		$path = $system->path . '/Config';

		if(isset($resource->slug))
		{
			$path = $system->path . '/Config/' . $resource->slug;
		}

		$this->config  = $this->files->getRequire($path . '/config.php');
		$this->aliases = $this->files->getRequire($path . '/aliases.php');

		if( ! is_array($this->config))
		{
			throw new Exception('Config files not found.');
		}

		$this->date = array_get($this->config, 'date');
		$this->entities = array_get($this->config, 'entities');
		$this->resourceInstance = array_get($this->config, 'resourceInstance');

		if($this->aliases == null or $this->entities == null or $this->date == null)
		{
			throw new Exception('Wrong file format.');
		}

		return $this;
	}

	public function getAlias($name)
	{
		return $this->aliases[$name];
	}

	/**
	 * Get the entities.
	 *
	 * @param  void
	 * @return array
	 */	
	public function getEntities()
	{
		return $this->entities;
	}
}