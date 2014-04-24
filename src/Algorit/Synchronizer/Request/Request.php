<?php namespace Algorit\Synchronizer\Request;

use Closure;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Request\Methods\MethodInterface;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;
use Algorit\Synchronizer\Request\Contracts\DispatcherInterface;
use Algorit\Synchronizer\Request\Exceptions\RequestException;

abstract class Request implements RequestInterface {

	/**
	 * The method instance.
	 *
	 * @var \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	protected $method;

	/**
	 * The Caller instance.
	 *
	 * @var \Algorit\Synchronizer\Request\Caller
	 */
	protected $caller;

	/**
	* The Config instance.
	*
	* @var \Algorit\Synchronizer\Config
	*/
	protected $config;

	/**
	* The Resource instance.
	*
	* @var \Algorit\Synchronizer\Request\Contracts\ResourceInterface
	*/
	protected $resource;

	/**
	* The Dispatcher instance.
	*
	* @var \Algorit\Synchronizer\Request\Contracts\DispatcherInterface
	*/
	protected $dispatcher;

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
	 * The request options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Create a new instance.
	 *
	 * @param  \Algorit\Synchronizer\Request\Contracts\RequestMethodInterface  $method
	 * @param  \Algorit\Synchronizer\Request\Caller  $caller
	 * @return instance
	 */
	public function __construct(MethodInterface $method, Caller $caller)
	{
		$this->method = $method;
		$this->caller = $caller;
	}

	/**
	 * Set the Config instance.
	 *
	 * @param \Algorit\Synchronizer\Request\Config
	 * @return void
	 */
	public function setConfig(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Set the Resource instance.
	 *
	 * @param \Algorit\Synchronizer\Request\Contracts\ResourceInterface
	 * @return void
	 */
	public function setResource(ResourceInterface $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * Set the Dispatcher instance.
	 *
	 * @param \Algorit\Synchronizer\Request\Contracts\DispatcherInterface
	 * @return void
	 */
	public function setDispatcher(DispatcherInterface $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

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

		$this->options = (object) [
			'base_url' => array_get($this->config->config, 'base_url'),
			'type' 	   => $type,
			'lastSync' => $lastSync,
			'entity'   => $entities[$type][$entityName]
		];
	}

	/**
	 * Get the Config instance.
	 *
	 * @return \Algorit\Synchronizer\Request\Config
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Get the Resource instance.
	 *
	 * @return \Algorit\Synchronizer\Request\Contracts\ResourceInterface
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * Get the Caller instance.
	 *
	 * @return \Algorit\Synchronizer\Request\Caller
	 */
	public function getCaller()
	{
		return $this->caller;
	}

	/**
	 * Get the request options
	 *
	 * @param  void
	 * @return array
	 */
	public function getOptions()
	{
		$this->options->url = $this->getRequestUrl();

		return $this->options;
	}

	/**
	 * Get the request URL with the last sync date
	 *
	 * @param  void,
	 * @return string
	 */
	private function getRequestUrl()
	{
		// Set URL
		$url = array_get($this->config->config, 'base_url') . '/' . array_get($this->options->entity, 'url');

		// Get lastSync date
		$lastSync = $this->options->lastSync->format($this->config->date['format']);

		// Todo: Use Sender or Receiver
		if($this->options->type == 'receive')
		{
			// Add date to URL on Receive requests.
			$url .= '?' . $this->config->date['query_string'] . '=' . str_replace(' ', '_', $lastSync);
		}

		return $url;
	}

	/**
	 * Create a request to authenticate.
	 *
	 * Needs to be implemented by subclasses.
	 *
	 * @return mixed
	 */
	public abstract function authenticate();

	/**
	 * Process the data received from a request.
	 *
	 * @param   mixed $request
	 * @param   \Closure  $callback
	 * @return  mixed
	 */
	public function processRequestData($request, Closure $callback)
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
	 * Create a multipart request. (For file uploads)
	 *
	 * @param string $inputName
	 * @param string $file
	 * @param string $fileName
	 * @return array
	 */
	protected function createMultipartRequest($inputName, $file, $fileName)
	{
		$separator = '----' . md5($fileName);
		$eol = "\r\n";

		// Use filesystem? Call parsers?
		// $content = $this->files->get($file);

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

}
