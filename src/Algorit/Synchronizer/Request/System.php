<?php namespace Algorit\Synchronizer\Request;

use ReflectionClass;
use Algorit\Synchronizer\Container;
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
	 * The filesystem
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

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

	// public function setConfig(Config $config)
	// {
	// 	$this->config = $config;

	// 	return $this;
	// }

	// public function getConfig()
	// {
	// 	return $this->config;
	// }

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
	 * Set the filesystem
	 *
	 * @param  \Illuminate\Filesystem\Filesystem $filesystem
	 * @return void
	 */
	public function setFilesystem(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
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

		$this->bindContainer($container);

		return $container->make($this->request);
	}

	private function bindContainer(Container $container)
	{
		// Set actual namespace in container.
		$container->setNamespace($this->namespace);

		// Bind the correct Filesystem
		$container->bind('Illuminate\Filesystem\Filesystem', function()
		{
			return $this->filesystem ?: new Filesystem;
		});

		// Is it binding the Container to itself? Brain fuck
		// Think we should use an interface here, as well for the filesystem.
		$container->bind('Algorit\Synchronizer\Container', function() use ($container)
		{
			return $container;
		});

		$container->bind('Algorit\Synchronizer\Request\Methods\MethodInterface', function()
		{
			return $this->method ?: new Requests;
		});
	}

}