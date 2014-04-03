<?php namespace Algorit\Synchronizer\Request\Contracts;

Interface RepositoryInterface {

	/**
	 * Import data into Repository.
	 *
	 * @param  void
	 * @return string
	 */
	public function import(Array $data);

	/**
	 * Get data from Repository.
	 *
	 * @param  void
	 * @return string
	 */
	public function get();

	/**
	 * Delete data from Repository.
	 *
	 * @param  void
	 * @return string
	 */
	public function delete($all);

	/**
	 * Update data in Repository.
	 *
	 * @param  void
	 * @return string
	 */
	public function update();
	
}

