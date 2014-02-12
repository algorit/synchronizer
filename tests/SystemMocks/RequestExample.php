<?php namespace Algorit\Synchronizer\Tests;

use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Request\Contracts\RequestInterface;
use Algorit\Synchronizer\Request\Request as AbstractRequest;
use Algorit\Synchronizer\Request\Exceptions\RequestException;
use Algorit\Synchronizer\Methods\MethodInterface as RequestMethod;

class RequestExample extends AbstractRequest implements RequestInterface {

	/**
	 * The config array.
	 *
	 * @var \Synchronizer\Systems\Config
	 */
	protected $config;

	/**
	 * The method instance.
	 *
	 * @var \Synchronizer\Methods\RequestMethodInterface
	 */
	protected $method;

	/**
	 * The parser instance.
	 *
	 * @var \Synchronizer\Systems\Jjw\Parser
	 */
	protected $parser;

	/**
	 * The filesystem
	 *
	 * @var \Synchronizer\Systems\Filesystem
	 */
	protected $files;

	/**
	 * The repository instance.
	 *
	 * @var object
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

	protected $resource;

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
	public function __construct(RequestMethod $method, Parser $parser, Repository $repository)
	{
		$this->method = $method;
		$this->parser = $parser;
		$this->repository = $repository;
	}

	public function authenticate(){}

	public function executeRequest($auth = true){}

	public function receive($entityName, $lastSync){}

	public function send(Array $data, $entityName, $requestDate){}

}