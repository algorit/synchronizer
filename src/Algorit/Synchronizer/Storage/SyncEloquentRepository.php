<?php namespace Algorit\Synchronizer\Storage;

use Exception;
use Algorit\Veloquent\Repository\Model;

final class SyncEloquentRepository implements SyncRepositoryInterface {
	
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
	 * Create a new model.
	 *
	 * @return Collection
	 */
	public function create($data)
	{
		return $this->entity->create($data);
	}
	
	/**
	 * Update the fillable data given an instance.
	 *
	 * @return Collection
	 */
	public function update($instance, $data)
	{
		if($instance == false)
		{
			return array(
	            'response' => false
	        );
		}

		$fillable = $this->entity->getFillable();

        $updated = array();
        $skipped = array();

        foreach($data as $key => $value)
        {
            if( ! in_array($key, $fillable))
            {
            	$skipped[$key] = $value;

                continue;
            }
           
            $updated[$key]  = $value;

            $instance->$key = $value;
        }
        
        return array(
            'response' => $instance->save(),
            'updated'  => $updated,
            'skipped'  => $skipped
        );
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
		$last = $this->entity->where('status', 'success')
							 ->where('entity', $entity)
							 ->where('type', $type);

		return $last->orderBy('created_at', 'DESC')->first(array('created_at'));
	}

	/**
	 * Set the current sync instance
	 *
	 * @param  \Application\Storage\Entities\Sync   $instance
	 * @return \Application\Storage\Entities\Sync
	 */
	public function setCurrentSync($instance)
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
				'line'	  	=> $exception->getLine()
			))
		));
	}
}