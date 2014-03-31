<?php namespace Algorit\Synchronizer;

use Closure;
use Exception;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Collection;
use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;
use Algorit\Synchronizer\Request\Contracts\ContainerInterface;

class Loader {

	use LoggerTrait;

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
	 * Set the System instance
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\SystemInterface $system
	 * @return \Algorit\Synchronizer\Loader
	 */
	public function setSystem(SystemInterface $system)
	{
		$this->system = $system;

		return $this;
	}

	/**
	 * Get the System instance
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\System
	 */
	public function getSystem()
	{
		return $this->system;
	}

	/**
	 * Get the Builder instance
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\Builder
	 */
	public function getBuilder()
	{
		return $this->builder;
	}

	/**
	 * Get the System instance
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\Request\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Load the ERP Request.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\SystemInterface $system
	 * @param  mixed  $callback
	 * @return \Algorit\Synchronizer\Loader
	 */
	public function loadSystem(SystemInterface $system, $callback = false)
	{	
		$this->logger->notice('Loading ' . $system->name);

		$this->setSystem($system);

		// Load system
		$this->request = $this->system->loadRequest($this->container);

		return $this->select($system->getResource(), $callback);
	}

	/**
	 * Test if the variable is a collection or a resource.
	 *
	 * @param  mixed $resource
	 * @param  mixed $callback
	 * @return \Algorit\Synchronizer\Loader
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
	 * Start the loader given a Collection as resource.
	 *
	 * @param  \Illuminate\Support\Collection $collection
	 * @param  mixed $callback
	 * @return \Algorit\Synchronizer\Loader
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
	 * @param  \Algorit\Synchronizer\Request\Contracts\ResourceInterface $resource
	 * @param  mixed $callback
	 * @return \Algorit\Synchronizer\Loader
	 */
	public function start(ResourceInterface $resource, $callback = false)
	{
		// Set config
		$this->request->setConfig($this->config->setup($this->system, $resource));

		// Set system resource
		$this->request->setResource($resource);

		// Set logger
		$this->builder->setLogger($this->logger);

		// Start the Builder
		$this->builder->start($this->request, $resource);

		if($callback instanceof Closure)
		{
			return $callback($this); // return?
		}

		return $this;
	}

}