<?php namespace Synchronizer\Request\Contracts;

Interface SystemRepositoryInterface {

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

