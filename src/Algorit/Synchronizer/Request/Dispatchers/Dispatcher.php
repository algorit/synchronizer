<?php namespace Algorit\Synchronizer\Request\Dispatchers;

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

	/**
	 * The request.
	 *
	 * @var \Algorit\Synchronizer\Request\Methods\MethodInterface
	 */
	protected $request;

	public abstract function getName();

	public abstract function getRequestUrl();

	public abstract function dispatch($method, $data = array(), $options = array());

}