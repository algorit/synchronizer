<?php namespace Algorit\Synchronizer\Request\Contracts;

Interface RepositoryInterface {

	/**
	 * Create or update data.
	 *
	 * @param  array $data
	 * @return string
	 */
	public function sync(Array $data);

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
	public function delete(Array $all);
	
}

