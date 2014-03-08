<?php namespace Algorit\Synchronizer\Request;

// Set configuration place
use ReflectionClass;
use Algorit\Synchronizer\Container;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Traits\ConfigTrait;
use Algorit\Synchronizer\Traits\ResourceTrait;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;
use Algorit\Synchronizer\Request\Contracts\ResourceInterface;
use Algorit\Synchronizer\Request\Methods\Requests as RequestMethod;

abstract class System implements SystemInterface {

	use ConfigTrait;
	use ResourceTrait;

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

		// Bind the correct Filesystem
		$container->bind('Illuminate\Filesystem\Filesystem', function()
		{
			if( ! $this->filesystem)
			{
				return new Filesystem;
			}

			return $this->filesystem;
		});

		$container->bind('Algorit\Synchronizer\Request\Methods\MethodInterface', 'Algorit\Synchronizer\Request\Methods\Requests');

		return $container->make($this->request);

		// return $container->make($this->request);
		// How to test?
		// return new $this->request(
		// 	new RequestMethod,
		// 	(new Repository)->setNamespace($this->namespace),
		// 	(new Parser($this->filesystem))->setNamespace($this->namespace)
		// );
	}

}