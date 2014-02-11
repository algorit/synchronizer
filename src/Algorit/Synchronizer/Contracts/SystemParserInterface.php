<?php namespace Synchronizer\Contracts;

Interface SystemParserInterface {

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
	 * @param  ?	   $resource
	 * @param  Array   $data
	 * @return instance
	 */
	public function receive($resource, Array $data);

	/**
	 * Parse sent data.
	 *
	 * @param  ?	   $resource
	 * @param  Array   $data
	 * @return instance
	 */
	public function send($resource, Array $data);

	/**
	 * Parse sent returned data.
	 *
	 * @param  ?	   $resource
	 * @param  Array   $data
	 * @return instance
	 */
	public function returned($resource, Array $data);
}

