<?php namespace Algorit\Synchronizer\Request;

use ReflectionClass;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Request\Methods\Requests;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;

abstract class System implements SystemInterface {

	/**
	 * The system name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The system namespace
	 *
	 * @var string
	 */
	public $namespace;

	/**
	 * The resource config path
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The request method
	 *
	 * @var object
	 */
	protected $method;

	/**
	 * Create a new instance.
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct($resource = false)
	{
		if($resource)
		{
			$this->resource = $resource;
		}

		$this->setup();
	}

	public function setResource(ResourceInterface $resource)
	{
		$this->resource = $resource;

		return $this;
	}

	public function getResource()
	{
		return $this->resource;
	}
	
	/**
	 * Setup the names.
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\Request\Contracts\SystemInterface
	 */
	protected function setup()
	{
		$reflector = new ReflectionClass(get_class($this));

		if( ! $this->path)
		{
			$this->path = dirname($reflector->getFileName());
		}

		if( ! $this->name)
		{
			$this->name = $reflector->getName();
		}

		if( ! $this->namespace)
		{
			$this->namespace = $reflector->getNamespaceName();
		}

		return $this;
	}

	/**
	 * Set the request Class.
	 *
	 * @param  string $name
	 * @return \Algorit\Synchronizer\Request\Contracts\SystemInterface
	 */
	public function setRequest($name = 'Request')
	{
		if(strpos($name, '\\') === false)
		{
			$name = $this->namespace . '\\' . $name;
		}

		$this->request = $name;

		return $this;
	}

	/**
	 * Load the request Class
	 *
	 * @param  \Algorit\Synchronizer\Container  $container
	 * @return \Algorit\Synchronizer\Request\Contracts\RequestInterface
	 */
	public function loadRequest(Container $container)
	{
		if( ! isset($this->request))
		{
			$this->setRequest();
		}

		return $this->request = $this->bindedContainer($container)
									 ->make($this->request);
	}

	/**
	 * Get the container with all its bindings
	 *
	 * @param  \Algorit\Synchronizer\Container  $container
	 * @return \Algorit\Synchronizer\Container  $container
	 */
	private function bindedContainer(Container $container)
	{
		// Set actual namespace in container.
		$container->namespace = $this->namespace;

		$container->bind('Algorit\Synchronizer\Request\Methods\MethodInterface', function()
		{
			return $this->method ?: new Requests;
		});

		// Is it binding the Container to itself? Brain fuck
		// Think we should use an interface here
		$container->bind('Algorit\Synchronizer\Container', function() use ($container)
		{
			return $container;
		});

		return $container;
	}

}