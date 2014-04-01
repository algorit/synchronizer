<?php namespace Algorit\Synchronizer\Request\Methods;

class Curl implements MethodInterface {

	private static $instance;

	protected static function me()
	{
		if(static::$instance == false)
		{
			static::$instance = new CurlMethod;
		}

		return static::$instance;
	}

	public function head($url, $headers = array(), $options = array())
	{
		return static::me()->head($url, $headers, $options);
	}

	public function get($url, $headers = array(), $options = array())
	{
		return static::me()->get($url, $headers, $options);
	}

	public function post($url, $headers = array(), $data = array(), $options = array())
	{
		return static::me()->post($url, $headers, $data, $options);
	}

	public function put($url, $headers = array(), $data = array(), $options = array())
	{
		return static::me()->put($url, $headers, $data, $options);
	}

	public function delete($url, $headers = array(), $options = array())
	{
		return static::me()->delete($url, $headers, $options);
	}

	public function patch($url, $headers = array(), $options = array())
	{
		return static::me()->patch($url, $headers, $options);
	}

}
