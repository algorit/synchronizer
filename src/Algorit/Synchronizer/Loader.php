<?php namespace Algorit\Synchronizer;

use Log, Closure, Exception;
use Algorit\Synchronizer\Request\Config;
use Illuminate\Database\Eloquent\Collection;
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
	 * @var \Algorit\Synchronizer\Builder
	 */
	protected $builder;

	/**
	 * The Container instance.
	 *
	 * @var \Algorit\Synchronizer\Container
	 */
	protected $container;

	/**
	 * Create a new Loader.
	 *
	 * @param  Builder $builder
	 * @param  Config  $config
	 * @return instance
	 */
	public function __construct(Container $container, Builder $builder, Config $config)
	{
		$this->config    = $config;
		$this->builder   = $builder;
		$this->container = $container;
	}

	/**
	 * Load the ERP Request.
	 *
	 * @param  Application\Entities\Company $company
	 * @param  mixed  					    $callback
	 * @return instance
	 */
	public function loadSystem(SystemInterface $system, $callback = false)
	{
		$this->system = $system;

		Log::info('Loading "' . $system->name . '" request system...');

		// Load system
		$this->request = $this->system->loadRequest($this->container);

		// Set config
		$this->request->setConfig($this->config->setup($system));

		return $this->select($this->system->getResource(), $callback);
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
	 * See if the variable is a collection or a resource.
	 *
	 * @param  object
	 * @return mixed
	 */
	private function select($resource, $callback)
	{
		if($resource == false)
		{
			throw new Exception('Resource is not defined.');
		}

		if($resource instanceof Collection)
		{
			return $this->startCollection($resource, $callback);
		}

		if($resource instanceof ResourceInterface)
		{
			return $this->start($resource, $callback);
		}

		return $this;
	}

	/**
	 * Start the loader usign a Collection as resource.
	 *
	 * @param  callback
	 * @return void
	 */
	public function startCollection(Collection $collection, $callback = false)
	{
		foreach($collection as $resource)
		{
			$this->start($resource, $callback);
		}

		return $this;
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