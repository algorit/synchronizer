<?php namespace Algorit\Synchronizer;

use Closure;
use Exception;
use Carbon\Carbon;
use Algorit\Synchronizer\Storage\SyncRepositoryInterface;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;

class Builder {

	use LoggerTrait;

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
	 * @param \Algorit\Synchronizer\Sender 	 $send
	 * @param \Algorit\Synchronizer\Receiver $receive
	 * @param \Algorit\Synchronizer\Storage\SyncRepositoryInterface $repository
	 * @return void
	 */
	public function __construct(Sender $send, Receiver $receive, SyncRepositoryInterface $repository)
	{
		$this->send = $send;
		$this->receive = $receive;
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

	/**
	 * Get the request
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\Request\Contracts\RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Get the resource
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\Request\Contracts\ResourceInterface
	 */
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
		$sync = array(
			'entity'  => $entity,
			'type'    => $type,
			'class'   => get_class($this->request),
			'status'  => 'processing',
		);

		return $this->repository->setCurrentSync($this->repository->create($sync));
	}

	/**
	 * Get last sync date from repository. (Or try to...)
	 *
	 * @param  string|bool  $lastSync
	 * @param  string  		$entity
	 * @param  string  		$function
	 * @return mixed
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
	 * @param  string 			$entity
	 * @param  \Carbon\Carbon 	$lastSync
	 * @param  Closure 			$try
	 * @param  boolean 			$details
	 * @return mixed
	 */
	private function process($entity, $lastSync, $function, Closure $try, $details = false)
	{
		$this->logger->info('Processing ' . $entity . ' request');

		try
		{	
			$this->createCurrentSync($entity, $function);

			$lastSync = $this->getLastSync($lastSync, $entity, $function);

			// Do it!
			$do = $try($entity, $lastSync);

			$this->repository->updateCurrentSync(array(
				'status' 	 => 'success',
				'started_at' => array_get($do, 'options')->lastSync->toDateTimeString(),
				'url' 		 => array_get($do, 'options')->url,
				'response'	 => json_encode($do)
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

			$this->logger->error($message);

			return false;
		}
	}

	/**
	 * Receive data from ERP and send it to the repository
	 *
	 * Receive all system data.
	 *
	 * @param string 	$entity
	 * @param mixed 	$lastSync
	 * @return \Algorit\Synchronizer\Sender
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
			$response = $this->send->toDatabase($this->request, (string) $entity, $data);

			return [
				'options' => $this->getRequest()->getOptions(),
				'response' => $response
			];
		});
	}

	/**
	 * Send data from repository to ERP
	 *
	 * Usually send orders that was received from device.
	 *
	 * @param string 	$entity
	 * @param mixed 	$lastSync
	 * @return \Algorit\Synchronizer\Sender
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
			$response = $this->send->toErp($this->request, (string) $entity, $data);

			return [
				'options' => $this->getRequest()->getOptions(),
				'response' => $response
			];
		});
	}

	/**
	 * Send data to Api
	 *
	 * Send all Device data.
	 *
	 * @param string 	$entity
	 * @param mixed 	$lastSync
	 * @return \Algorit\Synchronizer\Sender
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
			$response =  $this->send->toApi($data);

			return [
				'options' => $this->getRequest()->getOptions(),
				'response' => $response
			];
		});
	}

	/**
	 * Receive data from Api
	 *
	 * Usually receive orders from the device.
	 *
	 * @param array 	$data
	 * @param string 	$entity
	 * @param mixed 	$lastSync
	 * @return \Algorit\Synchronizer\Sender
	 */
	public function fromApiToDatabase(Array $data, $entity, $lastSync = null)
	{
		return $this->process($entity, $lastSync, __FUNCTION__, function($entity, $lastSync) use ($data)
		{
			// Touch current sync to set a new updated_at date.
			$this->repository->touchCurrentSync();

			// Send to database
			$response = $this->send->toDatabase($this->request, (string) $entity, $data);

			return [
				'options' => $this->getRequest()->getOptions(),
				'response' => $response
			];
		});

	}

}