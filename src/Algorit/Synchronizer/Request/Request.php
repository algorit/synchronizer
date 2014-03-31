<?php namespace Algorit\Synchronizer\Request;

// use Log;
use Closure;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Request\Methods\MethodInterface;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;
use Algorit\Synchronizer\Request\Exceptions\RequestException;

abstract class Request implements RequestInterface {

	use EntityTrait;

	/**
	 * The method instance.
	 *
	 * @var \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	protected $method;

	/**
	 * The transport instance.
	 *
	 * @var \Algorit\Synchronizer\Request\Transport
	 */
	protected $transport;

	/**
	 * The http cookie.
	 *
	 * @var string
	 */
	protected $cookie;

	/**
	 * The http headers.
	 *
	 * @var array
	 */
	protected $headers;

	/**
	 * The current last sync date.
	 *
	 * @var array
	 */
	protected $lastSync;

	/**
	 * The current sync type.
	 *
	 * @var array
	 */
	protected $type;

	/**
	 * Create a new instance.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\RequestMethodInterface  $method
	 * @param  \Algorit\Synchronizer\Request\Transport  $transport
	 * @return instance
	 */
	public function __construct(MethodInterface $method, Transport $transport)
	{
		$this->method = $method;
		$this->transport = $transport;
	}

	public function setConfig(Config $config)
	{
		$this->config = $config;

		return $this;
	}

	public function setResource(ResourceInterface $resource)
	{
		$this->resource = $resource;

		return $this;
	}

	public function getConfig()
	{
		return $this->config;
	}

	public function getResource()
	{
		return $this->resource;
	}

	public function getTransport()
	{
		return $this->transport;
	}

	// public function getParser()
	// {
	// 	return $this->parser;
	// }

	// public function getRepository()
	// {
	// 	return $this->repository;
	// }

	public abstract function authenticate();

	/**
	 * Set the request options. 
	 *
	 * @param  string  $entityName
	 * @param  string  $lastSync
	 * @param  string  $type
	 * @return void
	 */
	public function setOptions($entityName, $lastSync = false, $type = 'receive')
	{
		$entities = $this->config->getEntities();

		if( ! in_array($type, array('receive', 'send')))
		{
			throw new RequestException('Wrong request type');
		}

		if( ! isset($entities[$type][$entityName]))
		{
			throw new RequestException('Entity not found in system config file.');
		}

		if( ! $lastSync instanceof Carbon)
		{
			$lastSync = Carbon::createFromFormat($this->config->date['format'], $this->config->date['default']);
		}

		// Set them all!
		$this->type = $type;
		$this->lastSync = $lastSync;

		$this->setEntity($entities[$type][$entityName]);
	}

	/**
	 * Get the options
	 *
	 * @param  void
	 * @return array
	 */
	public function getOptions()
	{
		$base = array_get($this->config->config, 'base_url');

		return array(
			'base_url' => $base,
			'url'    => $this->getRequestUrl(),
			'entity' => $this->getEntity(),
			'lastSync' => $this->lastSync,
			'type' => $this->type,
		);
	}
	
	/**
	 * Process the data received from a request.
	 *
	 * @param   \Algorit\Synchronizer\Contracts\RequestMethodInterface  $request
	 * @param   \Closure  $callback
	 * @return  mixed
	 */
	protected function processRequestData($request, Closure $callback)
	{
		return $callback(json_decode($request->body, true));
	}

	/**
	 * Execute a request.
	 *
	 * Needs to be implemented by subclasses.
	 * 
	 * @param  boolean $auth
	 * @return \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	public abstract function executeRequest($auth = true);

	/**
	 * Create a request to receive data.
	 *
	 * Needs to be implemented by subclasses.
	 * 
	 * @param  string $entityName
	 * @param  mixed  $lastSync
	 * @return \Algorit\Synchronizer\Request\Methods\MethodInterface 
	 */
	public abstract function receive($entityName, $lastSync);

	/**
	 * Create a request to send data.
	 *
	 * Needs to be implemented by subclasses.
	 * 
	 * @param  array  $data
	 * @param  string $entityName
	 * @param  mixed  $lastSync
	 * @return \Algorit\Synchronizer\Request\Methods\MethodInterface 
	 */
	public abstract function send($entityName, Array $data, $lastSync = false);

	/**
	 * Create a multipart request. (Used for file uploads)
	 *
	 * @return $body
	 */
	protected function createMultipartRequest($inputName, $file, $fileName)
	{
		$separator = '----' . md5($fileName);
		$eol = "\r\n";

		$content = $this->files->get($file);

		$headers = array(
			'Cookie' 		=> $this->headers['Cookie'],
			'Content-Type' 	=> 'multipart/form-data; boundary=' . $separator,
			'Connection' 	=> 'keep-alive'
		);

		$body = '--' . $separator . $eol . 
				'Content-Disposition: form-data; name="' . $inputName . '"; filename="' . $fileName . '.zip"' . $eol .
                'Content-Type: application/octet-stream' . $eol .
                $eol . $content . 
                $eol . $eol  .
                '--' . $separator;

		return array(
			'headers' => $headers,
			'body'	  => $body
		);
	}

	/**
	 * Execute a request to Send data.
	 *
	 * @param  string  $requestMethod,
	 * @param  string  $url
	 * @return \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	protected function executeSendRequest($requestMethod, $data, $options = array())
	{
		if( ! isset($data['headers']) or ! isset($data['body']))
		{
			throw new RequestException('Wrong send request data format');
		}

		$headers = array_get($data, 'headers');
		$body	 = array_get($data, 'body');

		if( ! isset($options['timeout']))
		{
			$options = array_merge($options, array('timeout' => 200000));
		}

		return $this->method->{$requestMethod}($this->getRequestUrl(), $headers, $body, $options);
	}

	/**
	 * Execute a request to Receive data.
	 *
	 * @param  string  $requestMethod,
	 * @param  string  $url
	 * @return \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	protected function executeReceiveRequest($requestMethod, $options = array())
	{
		if( ! isset($options['timeout']))
		{
			$options = array_merge($options, array('timeout' => 200000));
		}

		return $this->method->{$requestMethod}($this->getRequestUrl(), $this->headers, $options);
	}

	/**
	 * Get the request URL with the last sync date
	 *
	 * @param  void,
	 * @return string
	 */
	private function getRequestUrl()
	{
		$base_url = array_get($this->config->config, 'base_url') . '/' . array_get($this->entity, 'url');

		$lastSync = $this->lastSync->format($this->config->date['format']);
		$query_string = $this->config->date['query_string'];

		// Add date to URL on Receive requests.
		$base_url .= '?' . $query_string . '=' . str_replace(' ', '_', $lastSync);

		return $base_url;
	}

}