<?php namespace Algorit\Synchronizer\Request\Contracts;

use Algorit\Synchronizer\Request\Contracts\ResourceInterface;

Interface ParserInterface {

	/**
	 * Set class aliases.
	 *
	 * @param  Array 	$aliases
	 * @return instance
	 */
	public function setAliases($aliases);

	/**
	 * Parse received data.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\ResourceInterface $resource
	 * @param  Array  $data
	 * @return instance
	 */
	public function receive(ResourceInterface $resource, Array $data);

	/**
	 * Parse sent data.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\ResourceInterface $resource
	 * @param  Array  $data
	 * @return instance
	 */
	public function send(ResourceInterface $resource, Array $data);

	/**
	 * Parse sent returned data.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\ResourceInterface $resource
	 * @param  Array  $data
	 * @return instance
	 */
	public function returned(ResourceInterface $resource, Array $data);
}

