<?php namespace Algorit\Synchronizer\Request\Dispatchers;

use Algorit\Synchronizer\Request\Methods\MethodInterface;
use Algorit\Synchronizer\Request\Contracts\DispatcherInterface;

abstract class Dispatcher implements DispatcherInterface {

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
	 * The request methods.
	 *
	 * @var array
	 */
	protected $methods;

	public abstract function getName();

	public abstract function getRequestUrl();

	public abstract function execute($requestMethod, $data = array(), $options = array());

}