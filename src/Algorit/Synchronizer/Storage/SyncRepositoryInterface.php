<?php namespace Algorit\Synchronizer\Storage;

use Closure;
use Exception;

interface SyncRepositoryInterface {

	/**
	 * Create a new model.
	 *
	 * @return Collection
	 */
	public function create($data);

	/**
	 * Update the fillable data given an instance.
	 *
	 * @return Collection
	 */
	public function update($instance, $data);

	/**
	 * Get last sync date
	 *
	 * @param  mixed   $resource
	 * @param  string  $entity
	 * @param  string  $type
	 * @return \Application\Storage\Entities\Sync
	 */
	public function getLastSync($resource, $entity, $type);

	/**
	 * Set the current sync instance
	 *
	 * @param  \Application\Storage\Entities\Sync   $instance
	 * @return \Application\Storage\Entities\Sync
	 */
	public function setCurrentSync($instance);

	/**
	 * Get the current sync
	 *
	 * @param  void
	 * @return \Application\Storage\Entities\Sync
	 */
	public function getCurrentSync();

	/**
	 * Update the current sync
	 *
	 * @param  array  $data
	 * @return SyncEntity
	 */
	public function updateCurrentSync(Array $data);


	/**
	 * Touch the current sync timestamps.
	 *
	 * @param  void
	 * @return SyncEntity
	 */
	public function touchCurrentSync();

	/**
	 * Update the current sync using an exception
	 *
	 * @param  \Exception  $exception
	 * @return SyncEntity
	 */
	public function updateFailedSync(Exception $exception);

}