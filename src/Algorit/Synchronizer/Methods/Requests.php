<?php namespace Synchronizer\Methods;

// https://github.com/rmccue/Requests
use Requests as Method;
use Synchronizer\Contracts\Request\RequestMethodInterface;

class Requests implements RequestMethodInterface {

	public function head($url, $headers = array(), $options = array())
	{
		return Method::head($url, $headers, $options);
	}

	public function get($url, $headers = array(), $options = array())
	{
		return Method::get($url, $headers, $options);
	}

	public function post($url, $headers = array(), $data = array(), $options = array())
	{
		return Method::post($url, $headers, $data, $options);
	}

	public function put($url, $headers = array(), $data = array(), $options = array())
	{
		return Method::put($url, $headers, $data, $options);
	}

	public function delete($url, $headers = array(), $options = array())
	{
		return Method::delete($url, $headers, $options);
	}

	public function patch($url, $headers = array(), $options = array())
	{
		return Method::patch($url, $headers, $options);
	}
	
}