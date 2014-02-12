<?php namespace Algorit\Synchronizer\Request;

use Str;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Application\Storage\Entities\Company as CompanyEntity;

class Config {

	/**
	 * The files instance.
	 *
	 * @var \Application\Services\Sync\Erps\Filesystem
	 */
	protected $files;
	
	/**
	 * The config array.
	 *
	 * @var string
	 */
	public $config;

	/**
	 * The ERP name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The aliases array.
	 *
	 * @var string
	 */
	public $aliases;

	/**
	 * The url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Create a new instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return instance
	 */
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * Setup the configuration.
	 *
	 * @param  void
	 * @return void
	 */
	public function setup(CompanyEntity $company)
	{
		$directory = __DIR__ . '/' . ucfirst(strtolower($company->erp->name));

		$this->config  = $this->files->getRequire($directory . '/Config/' . $company->slug . '/config.php');
		$this->aliases = $this->files->getRequire($directory . '/Config/' . $company->slug . '/aliases.php');

		if( ! is_array($this->config))
		{
			throw new Exception('Config file not found.');
		}

		$this->entities = array_get($this->config, 'entities');
		$this->date 	= array_get($this->config, 'date');
		$this->resourceInstance = array_get($this->config, 'resourceInstance');

		if($this->aliases == null or $this->entities == null or $this->date == null)
		{
			throw new Exception('Wrong file format.');
		}

		return $this;
	}

	public function getEntities()
	{
		return $this->entities;
	}
}