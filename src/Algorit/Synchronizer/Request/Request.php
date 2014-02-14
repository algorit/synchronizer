<?php namespace Algorit\Synchronizer\Request;

use Log;
use Closure;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Traits\ConfigTrait;
use Algorit\Synchronizer\Traits\ResourceTrait;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Exceptions\RequestException;
use Algorit\Synchronizer\Request\Methods\MethodInterface as RequestMethod;

abstract class Request implements RequestInterface {

	use ConfigTrait;
	use ResourceTrait;

	/**
	 * The method instance.
	 *
	 * @var \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	protected $method;

	/**
	 * The parser instance.
	 *
	 * @var \Algorit\Synchronizer\Request\Parser
	 */
	protected $parser;

	/**
	 * The repository instance.
	 *
	 * @var \Algorit\Synchronizer\Request\Repository
	 */
	protected $repository;

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
	protected $headers = array(
		'Accept' 			=>  '*/*',
		// 'Content-Length' 	=> 69,
		'Content-Type' 		=> 'application/x-www-form-urlencoded'
	);

	/**
	 * The current entity.
	 *
	 * @var array
	 */
	protected $entity;

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
	 * @param  \Synchronizer\Contracts\RequestMethodInterface  $method
	 * @param  \Services\Sync\Unzip   			  $zip
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  \Services\Sync\Erps\Jjw\Repository $repository
	 * @return instance
	 */
	public function __construct(RequestMethod $method, Repository $repository, Parser $parser)
	{
		$this->method = $method;
		$this->parser = $parser;
		$this->repository = $repository;
	}

	public function getParser()
	{
		return $this->parser;
	}

	public function getRepository()
	{
		return $this->repository;
	}

	public abstract function authenticate();

	/**
	 * Set the request options. 
	 *
	 * @param  string $entityName
	 * @param  string $lastSync
	 * @return void
	 */
	public function setRequestOptions($entityName, $lastSync = false, $type = 'receive')
	{
		if( ! in_array($type, array('receive', 'send')))
		{
			throw new RequestException('Wrong request type');
		}

		$entities = $this->config->getEntities();

		if( ! isset($entities[$type][$entityName]))
		{
			throw new RequestException('Entity not found in system config file.');
		}

		if( ! $lastSync instanceof Carbon)
		{
			$lastSync = Carbon::createFromFormat($this->config->date['format'], $this->config->date['default']);
		}

		// Set them all!
		$this->type 	= $type;
		$this->lastSync = $lastSync;
		$this->entity 	= $entities[$type][$entityName];
	}
	
	/**
	 * Process the data received from a request.
	 *
	 * @param   \Synchronizer\Contracts\RequestMethodInterface  $request
	 * @param   \Closure  $callback
	 * @return  mixed
	 */
	protected function processRequestData($request, Closure $callback)
	{
		return $callback(json_decode($request->body, true));
	}

	public abstract function executeRequest($auth = true);

	public abstract function receive($entityName, $lastSync);

	public abstract function send(Array $data, $entityName, $requestDate);

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
	 * @return \Synchronizer\Methods\MethodInterface
	 */
	protected function executeSendRequest($requestMethod, $url, $data, $options = array())
	{
		if( ! isset($data['headers']) or ! isset($data['body']))
		{
			throw new RequestException('Wrong send request data format');
		}

		$headers = array_get($data, 'headers');
		$body	 = array_get($data, 'body');

		Log::info('Sending data to ' . $url);

		if( ! isset($options['timeout']))
		{
			$options = array_merge($options, array('timeout' => 200000));
		}

		return $this->method->{$requestMethod}($url, $headers, $body, $options);
	}

	/**
	 * Execute a request to Receive data.
	 *
	 * @param  string  $requestMethod,
	 * @param  string  $url
	 * @return \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	protected function executeReceiveRequest($requestMethod, $url, $options = array())
	{
		$lastSync = $this->lastSync->format($this->config->date['format']);
		$query_string = $this->config->date['query_string'];

		// Add date to URL on Receive requests.
		$url .= '?' . $query_string . '=' . str_replace(' ', '_', $lastSync);

		Log::info('Receiving data from ' . $url);

		if( ! isset($options['timeout']))
		{
			$options = array_merge($options, array('timeout' => 200000));
		}

		return $this->method->{$requestMethod}($url, $this->headers, $options);
	}

}