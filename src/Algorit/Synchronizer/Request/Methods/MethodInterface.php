<?php namespace Algorit\Synchronizer\Request\Methods;

interface MethodInterface {

	public function head($url, $headers = array(), $options = array());

	public function get($url, $headers = array(), $options = array());

	public function post($url, $headers = array(), $options = array());

	public function put($url, $headers = array(), $options = array());

	public function delete($url, $headers = array(), $options = array());

	public function patch($url, $headers = array(), $options = array());

}