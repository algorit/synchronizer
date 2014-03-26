<?php namespace Algorit\Synchronizer\Storage;

use Exception;
use Algorit\Veloquent\Repository\Model;

// use Application\Storage\Entities\Company as CompanyEntity;
// use Application\Storage\Entities\Representative as RepresentativeEntity;

final class SyncEloquentRepository extends Model implements SyncInterface {
	
	/**
	 * The current instance.
	 *
	 * @var object
	 */	
	protected $current;

	/**
	 * Define the entity instance
	 *
	 * @return void
	 */
	public function __construct(Sync $entity)
	{
		$this->entity = $entity;
	}

	/**
	 * Get last sync date
	 *
	 * @param  mixed   $resource
	 * @param  string  $entity
	 * @param  string  $type
	 * @return \Application\Storage\Entities\Sync
	 */
	public function getLastSync($resource, $entity, $type)
	{
		$needsFilter = array('customers', 'prices', 'orders');

		$last = $this->entity->where('status', 'success')
							 ->where('entity', $entity)
							 ->where('type', $type);

		// Todo: Rewrite it.
		// if(in_array($entity, $needsFilter) and $resource instanceof RepresentativeEntity)
		// {
		// 	$last->where('representative_id', $resource->id);
		// }
		// elseif(in_array($entity, $needsFilter) and $resource instanceof CompanyEntity)
		// {
		// 	$last->where('company_id', $resource->id);
		// }

		return $last->orderBy('created_at', 'DESC')->first(array('created_at'));
	}

	/**
	 * Set the current sync instance
	 *
	 * @param  \Application\Storage\Entities\Sync   $instance
	 * @return \Application\Storage\Entities\Sync
	 */
	public function setCurrentSync(Sync $instance)
	{
		return $this->current = $instance;
	}

	/**
	 * Get the current sync
	 *
	 * @param  void
	 * @return \Application\Storage\Entities\Sync
	 */
	public function getCurrentSync()
	{
		return $this->current;
	}

	/**
	 * Update the current sync
	 *
	 * @param  array  $data
	 * @return SyncEntity
	 */
	public function updateCurrentSync(Array $data)
	{
		return $this->update($this->current, $data);
	}

	/**
	 * Touch the current sync timestamps.
	 *
	 * @param  void
	 * @return SyncEntity
	 */
	public function touchCurrentSync()
	{
		return $this->current->touch();
	}
	
	/**
	 * Update the current sync using an exception
	 *
	 * @param  \Exception  $exception
	 * @return SyncEntity
	 */
	public function updateFailedSync(Exception $exception)
	{	
		return $this->updateCurrentSync(array(
			'status'   => 'fail',
			'response' => json_encode(array(
				'exception'	=> get_class($exception),
				'message' 	=> $exception->getMessage(),
				'code'	  	=> $exception->getCode(),
				'file'	  	=> $exception->getFile(),
				'line'	  	=> $exception->getLine(),
				// 'trace'	  	=> $e->getTraceAsString() // -> is that too much?
			))
		));
	}
}