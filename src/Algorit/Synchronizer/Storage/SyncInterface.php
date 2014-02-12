<?php namespace Algorit\Synchronizer\Storage;

use Closure;

interface SyncInterface {

	/**
	 * Load relationships.
	 *
	 * @return Collection
	 */
	public function with($data);

	/**
	 * Load relationships.
	 *
	 * @return Collection
	 */
	public function load($data);

	/**
	 * Where filter.
	 *
	 * @return Collection
	 */
	public function where($column, $sign, $value = false);

	/**
	 * Get collection where field is null.
	 *
	 * @return Collection
	 */
	public function whereNull($column);

	/**
	 * Where In filter.
	 *
	 * @return Collection
	 */
	public function whereIn($column, Array $data);

	/**
	 * Where Not In filter.
	 *
	 * @return Collection
	 */
	public function whereNotIn($column, Array $data);

	/**
	 * Find a collection given an id.
	 *
	 * @return Collection
	 */
	public function find($id);

	/**
	 * Get collection data.
	 *
	 * @return Collection
	 */
	public function get($data);
	
	/**
	 * Get all collections.
	 *
	 * @return Collection
	 */
	public function all($fields = array('*'));

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
	 * Insert or update the repository using a Closure to match the data.
	 *
	 * @param  array 	$data
	 * @param  object 	$exists
	 * @return array
	 */
	public function createOrUpdate(Array $data, Closure $exists, $callback = false);

}