<?php namespace Algorit\Synchronizer;

use App;
use Log;
use Closure;
use Exception;
use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;

class Loader {

	/**
	 * The Company instance.
	 *
	 * @var object
	 */
	protected $company;

	/**
	 * The Request instance.
	 *
	 * @var object
	 */
	protected $request;

	/**
	 * The System instance.
	 *
	 * @var object
	 */
	protected $system;

	/**
	 * The Builder instance.
	 *
	 * @var object
	 */
	protected $builder;

	/**
	 * Create a new Loader.
	 *
	 * @param  Builder $builder
	 * @param  Config  $config
	 * @return instance
	 */
	public function __construct(Builder $builder, Config $config)
	{
		$this->builder = $builder;
		$this->config  = $config;
	}

	/**
	 * Load the ERP Request.
	 *
	 * @param  Application\Entities\Company $company
	 * @param  mixed  					    $callback
	 * @return instance
	 */
	public function loadSystem(SystemInterface $system)
	{
		$this->system = $system;

		Log::info('Loading "' . $system->name . '" request system...');

		// Load system
		$this->request = $system->loadRequest();

		// Set configurations
		$this->setupConfig($this->config->setup($system));

		return $this;
	}

	/**
	 * Setup config.
	 *
	 * @param  void
	 * @return Builder
	 */
	private function setupConfig(Config $config)
	{
		$this->request->setConfig($config);
		// $this->request->getParser()->setConfig($config);
	}

	/**
	 * Get the Builder instance
	 *
	 * @param  void
	 * @return Builder
	 */
	public function getBuilder()
	{
		return $this->builder;
	}

	/**
	 * Get the System instance
	 *
	 * @param  void
	 * @return System
	 */
	public function getSystem()
	{
		return $this->system;
	}

	/**
	 * Get the System instance
	 *
	 * @param  void
	 * @return System
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Set the resource and start the builder.
	 *
	 * @param  callback
	 * @return void
	 */
	public function start(ResourceInterface $resource, $callback = false)
	{
		// Set system resource
		$this->request->setResource($resource);

		// Start the Builder
		$this->builder->start($this->request, $resource);

		if($callback instanceof Closure)
		{
			return $callback($this); // return?
		}

		return $this;
	}

}