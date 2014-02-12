<?php namespace Algorit\Synchronizer;

use App;
use Log;
use Closure;
use Exception;
use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;

class Loader {

	/**
	 * The Company instance.
	 *
	 * @var object
	 */
	protected $company;

	/**
	 * The ERP Request instance.
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
	public function loadSystem(SystemInterface $system, $callback = false)
	{

		Log::info('Loading "' . $system->name . '" request system...');

		// Load system
		$this->system  = $system;
		$this->request = $system->loadRequest();

		// Set configurations
		$this->request->setConfig($this->config->setup($system));

		if($callback instanceof Closure)
		{
			return $this->set($callback);
		}

		return $this;
	}

	/**
	 * Set system and resource
	 *
	 * @param  callback
	 * @return instance
	 */
	public function set($callback = false)
	{	
		$config = $this->request->getConfig();

		Log::info('Setting resource...');

		switch($config->resourceInstance)
		{
			case 'representative':
				$this->loadForRepresentatives($callback);
			break;
			case 'company':
				$this->loadForCompany($callback);
			break;
		}

		return $this;
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
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Load representatives as resource
	 *
	 * @param  callback
	 * @return void
	 */
	// private function loadForRepresentatives($callback = false)
	// {
	// 	foreach($this->company->representatives as $representative)
	// 	{	
	// 		Log::notice('Loading representative ' . $representative->name);

	// 		$this->start($representative, $callback);
	// 	}
	// }

	// /**
	//  * Load company as resource
	//  *
	//  * @param  callback
	//  * @return void
	//  */
	// private function loadForCompany($callback = false)
	// {
	// 	Log::notice('Loading company ' . $this->company->name);

	// 	return $this->start($this->company, $callback);
	// }

	/**
	 * Set the resource and start the builder.
	 *
	 * @param  callback
	 * @return void
	 */
	public function start($resource, $callback = false)
	{
		// Set system resource
		$this->request->setResource($resource);

		// Start the builder
		$this->builder->start($this->request, $resource);

		if($callback instanceof Closure)
		{
			return $callback($this); // return?
		}

		return $this;
	}

}