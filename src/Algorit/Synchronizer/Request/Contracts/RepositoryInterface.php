<?php namespace Algorit\Synchronizer\Request\Contracts;

Interface RepositoryInterface {

	/**
	 * Start a sync.
	 *
	 * @param  array $data
	 * @return string
	 */
	public function sync(Array $data);

	/**
	 * Update or insert data.
	 *
	 * @param  array $data
	 * @return array
	 */
	public function upsert(Array $data);

	/**
	 * Get data from the Repository.
	 *
	 * @param  void
	 * @return string
	 */
	public function get();

	/**
	 * Delete data from the Repository.
	 *
	 * @param  void
	 * @return string
	 */
	public function remove(Array $all);
	
}

