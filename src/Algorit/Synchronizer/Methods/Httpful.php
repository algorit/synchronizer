<?php namespace Algorit\Synchronizer\Methods;

use Httpfull\Request as Method;

class Httpful implements MethodInterface {
	
	public function head($url, $headers = array(), $options = array())
	{
		return Method::head($url, $options)->addHeaders($headers)->send();
	}

	public function get($url, $headers = array(), $options = array())
	{
		return Method::get($url, $options)->addHeaders($headers)->send();
	}

	public function post($url, $headers = array(), $data = array(), $options = array())
	{
		return Method::post($url, $data, $options)->addHeaders($headers)->send();
	}

	public function put($url, $headers = array(), $data = array(), $options = array())
	{
		return Method::put($url, $data, $options)->addHeaders($headers)->send();
	}

	public function delete($url, $headers = array(), $options = array())
	{
		return Method::delete($url, $options)->addHeaders($headers)->send();
	}

	public function patch($url, $headers = array(), $options = array())
	{
		return Method::patch($url, $options)->addHeaders($headers)->send();
	}
	
}