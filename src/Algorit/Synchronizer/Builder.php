<?php namespace Algorit\Synchronizer;

// use Log;
use Closure;
use Exception;
use Carbon\Carbon;
use Algorit\Synchronizer\Storage\SyncInterface;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;

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
	 * The system request instance.
	 *
	 * @var object
	 */
	public $request;

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
	 * Set the Request and Resource
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\RequestInterface  $request
	 * @param  \Algorit\Synchronizer\Request\Contracts\ResourceInterface $resource
	 * @return void
	 */
	public function start(RequestInterface $request, ResourceInterface $resource)
	{
		$this->request  = $request;
		$this->resource = $resource;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getResource()
	{
		return $this->resource;
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
		// $create = Config::get('synchronizer::repository.create');
		$create = false; // Get config class later.

		if($create instanceof Closure)
		{
			$sync = $create($this->request, $this->resource, $entity, $type);
		}
		else
		{
			$sync = array(
				'entity'  => $entity,
				'type'    => $type,
				'class'   => get_class($this->request),
				'status'  => 'processing',
			);
		}

		return $this->repository->setCurrentSync($this->repository->create($sync));
	}

	/**
	 * Get last sync date from repository. (Or try to...)
	 *
	 * @param  string|bool  $lastSync
	 * @param  string  		$entity
	 * @param  string  		$function
	 * @return \Carbon\Carbon 
	 */
	private function getLastSync($lastSync, $entity, $function, $format = 'Y-m-d H:i:s')
	{
		if($lastSync instanceof Carbon)
		{
			return $lastSync;
		}

		// Use 'default' last sync to use the default date on system config.
		if($lastSync === 'default' or $lastSync === 'all')
		{
			return false;
		}

		$lastSync = $this->repository->getLastSync($this->resource, $entity, $function);

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
	private function process($entity, $lastSync, $function, Closure $try, $details = false)
	{
		try
		{	
			$this->createCurrentSync($entity, $function);

			$lastSync = $this->getLastSync($lastSync, $entity, $function);

			// Do it!
			$do = $try($entity, $lastSync);

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

			$message = get_class($e) 	  . ': ' 
					   . $e->getMessage() . ' in ' 
					   . $e->getFile() 	  . ' on line '
					   . $e->getLine();

			// Log::error($message);
			// $this->logger->error($message);

			echo $message; // fuck.
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
		return $this->process($entity, $lastSync, __FUNCTION__, function($entity, $lastSync)
		{
			// Receive from ERP
			$data = $this->receive->fromErp($this->request, (string) $entity, $lastSync);
				
			// Touch current sync to set a new updated_at date.
			$this->repository->touchCurrentSync();

			// Send to database
			return $this->send->toDatabase($this->request, (string) $entity, $data);
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
		return $this->process($entity, $lastSync, __FUNCTION__, function($entity, $lastSync)
		{
			// Receive from Database
			$data = $this->receive->fromDatabase($this->request, (string) $entity, $lastSync);
			
			// Touch current sync to set a new updated_at date.
			$this->repository->touchCurrentSync();

			// Send to ERP
			return $this->send->toErp($this->request, (string) $entity, $data);
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
		return $this->process($entity, $lastSync, __FUNCTION__, function($entity, $lastSync)
		{
			// Receive from Database
			$data = $this->receive->fromDatabase($this->request, (string) $entity, $lastSync);

			// Touch current sync to set a new updated_at date.
			$this->repository->touchCurrentSync();

			// Send to Api
			return $this->send->toApi($data);
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
		return $this->process($entity, $lastSync, __FUNCTION__, function($entity, $lastSync) use ($data)
		{
			// Send to database
			return $this->send->toDatabase($this->request, (string) $entity, $data);
		});

	}

}