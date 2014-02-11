<?php namespace Algorit\Synchronizer;

use Log;
use Config;
use Closure;
use Exception;
use Carbon\Carbon;

use Application\Storage\Contracts\SyncInterface;
use Algorit\Synchronizer\Contracts\RequestInterface;
use Algorit\Synchronizer\Contracts\SystemRequestInterface;

/**
 * Sync builder.
 * 
 * ERP can send data to Database
 * Database can send data to ERP
 * Database can send data to Device
 * Device can send data to Database
 **/
class Builder {

	/**
	 * The ERP System instance.
	 *
	 * @var object
	 */
	public $system;

	/**
	 * The current resource
	 *
	 * @var object
	 */
	protected $resource;

	/**
	 * The database repository instance.
	 *
	 * @var object
	 */
	protected $repository;

	/**
	 * The Sender instance.
	 *
	 * @var Sender
	 */
	protected $send;

	/**
	 * The Receiver instance.
	 *
	 * @var Receiver
	 */
	protected $receive;

	/**
	 * Class constructor.
	 *
	 * @param \Synchronizer\Sender 	 $send
	 * @param \Synchronizer\Receiver $receive
	 * @param \Application\Storage\Contracts\SyncInterface $repository
	 * @return void
	 */
	public function __construct(Sender $send, Receiver $receive, SyncInterface $repository)
	{
		$this->send    	  = $send;
		$this->receive 	  = $receive;
		$this->repository = $repository;
	}

	/**
	 * Set the system (ERP)
	 *
	 * @param  \Synchronizer\Contracts\SystemRequestInterface $system
	 * @return void
	 */
	public function start(SystemRequestInterface $system, $resource)
	{
		$this->resource = $resource;
		$this->system   = $system;
	}

	/**
	 * Create a current sync repository instance. 
	 *
	 * @param  string  $entity
	 * @param  string  $type
	 * @return \Carbon\Carbon $lastSync
	 */
	private function createCurrentSync($entity, $type)
	{	

		$create = Config::get('synchronizer::create');

		$sync = array(
			
		);

		if($create instanceof Closure)
		{
			$sync = $create($this->system, $this->resource, $entity, $type);
		}

		return $this->repository->setCurrentSync($this->repository->create($sync));
	}

	/**
	 * Get last sync date from repository. (Or try to...)
	 *
	 * @param  string|bool  $lastSync
	 * @param  string  		$entity
	 * @param  string  		$type
	 * @return \Carbon\Carbon 
	 */
	private function getLastSync($lastSync, $entity, $type, $format = 'Y-m-d H:i:s')
	{
		// Use 'default' last sync to use the default date on system config.
		if($lastSync === 'default' or $lastSync === 'all')
		{
			return false;
		}

		$lastSync = $this->repository->getLastSync($this->resource, $entity, $type);

		if($lastSync == false)
		{
			return false;
		}

		return Carbon::createFromFormat($format, $lastSync->created_at);
	}

	/**
	 * Process a closure inside a try statement
	 *
	 * @param  Closure 	$try
	 * @param  bool 	$details
	 * @return void
	 */
	private function process(Closure $try, $details = false)
	{
		try
		{	
			// Do it!
			$do = $try();

			$this->repository->updateCurrentSync(array(
				'status'   => 'success',
				'response' => json_encode($do)
			));

			return $do;
		}
		catch(Exception $e)
		{
			// Update sync
			$this->repository->updateFailedSync($e);

			$message = get_class($e) . ': ' . $e->getMessage();

			// if($details)
			// {
				$message .= ' in ' . $e->getFile();
			// }

			Log::error($message . ' on line ' . $e->getLine());
		}
	}

	/**
	 * Receive data from ERP and send it to the repository
	 *
	 * Receive all system data.
	 *
	 * @param string 	  $entity
	 * @param string|bool $lastSync
	 * @return \Synchronizer\Sender
	 */
	public function fromErpToDatabase($entity, $lastSync = null)
	{
		$this->createCurrentSync($entity, __FUNCTION__);

		if( ! $lastSync instanceof Carbon)
		{
			$lastSync = $this->getLastSync($lastSync, $entity, __FUNCTION__);
		}

		return $this->process(function() use ($entity, $lastSync)
		{
			// Receive from ERP
			$data = $this->receive->fromErp($this->system, (string) $entity, $lastSync);
				
			// Touch current sync to set a new update_at date.
			$this->repository->touchCurrentSync();

			// Send to database
			return $this->send->toDatabase($this->system, (string) $entity, $data);
		});
	}

	/**
	 * Send data from repository to ERP
	 *
	 * Usually send orders that was received from device.
	 *
	 * @param string 	  $entity
	 * @param string|bool $lastSync
	 * @return \Synchronizer\Sender
	 */
	public function fromDatabaseToErp($entity, $lastSync = null)
	{	
		$this->createCurrentSync($entity, __FUNCTION__);

		if( ! $lastSync instanceof Carbon)
		{
			$lastSync = $this->getLastSync($lastSync, $entity, __FUNCTION__);
		}

		return $this->process(function() use ($entity, $lastSync)
		{
			// Receive from Database
			$data = $this->receive->fromDatabase($this->system, (string) $entity, $lastSync);
			
			// Touch current sync to set a new update_at date.
			$this->repository->touchCurrentSync();

			// Send to ERP
			return $this->send->toErp($this->system, (string) $entity, $data);
		});
	}

	/**
	 * Send data to Api
	 *
	 * Send all Device data.
	 *
	 * @param string 	  $entity
	 * @param string|bool $lastSync
	 * @return \Synchronizer\Sender
	 */
	public function fromDatabaseToApi($entity, $lastSync = null)
	{
		$this->createCurrentSync($entity, __FUNCTION__);

		if( ! $lastSync instanceof Carbon)
		{
			$lastSync = $this->getLastSync($lastSync, $entity, __FUNCTION__);
		}

		return $this->process(function() use ($entity, $lastSync)
		{
			// Receive from Database
			$data = $this->receive->fromDatabase($this->system, (string) $entity, $lastSync);

			// Touch current sync to set a new update_at date.
			$this->repository->touchCurrentSync();

			// Send to Api
			return $this->send->toApi($this->system, (string) $entity, $data);
		});
	}

	/**
	 * Receive data from Api
	 *
	 * Usually receive orders from the device.
	 *
	 * @param array 	  $data
	 * @param string 	  $entity
	 * @param string|bool $lastSync
	 * @return \Synchronizer\Sender
	 */
	public function fromApiToDatabase(Array $data, $entity, $lastSync = null)
	{
		$this->createCurrentSync($entity, __FUNCTION__);

		if( ! $lastSync instanceof Carbon)
		{
			$lastSync = $this->getLastSync($lastSync, $entity, __FUNCTION__);
		}

		return $this->process(function() use ($entity, $lastSync)
		{
			// Send to database
			return $this->send->toDatabase($this->system, $data, (string) $entity, $lastSync);
		});

	}

}