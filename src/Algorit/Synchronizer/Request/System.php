<?php namespace Algorit\Synchronizer\Request;

// Set configuration place
use ReflectionClass;
use Illuminate\Filesystem\Filesystem;
use Algorit\Synchronizer\Traits\ConfigTrait;
use Algorit\Synchronizer\Traits\ResourceTrait;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;
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
	 * The system resource (Company, device, representative...)
	 *
	 * @var object
	 */
	public $resource;

	/**
	 * The resource config path
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Create a new instance.
	 *
	 * @param  void
	 * @return void
	 */
	public function __construct()
	{
		$this->setup();
	}

	/**
	 * Setup the names.
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\Request\Contracts\SystemInterface
	 */
	private function setup()
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
		$this->request = $this->namespace . '\\' . $name;

		return $this;
	}

	/**
	 * Load the request Class
	 *
	 * @param  void
	 * @return \Algorit\Synchronizer\Request\Contracts\RequestInterface
	 */
	public function loadRequest()
	{
		if( ! isset($this->request))
		{
			$this->setRequest();
		}

		// How to test?
		return new $this->request(
			new RequestMethod,
			new Repository($this->namespace),
			new Parser(new Filesystem, $this->namespace)
		);
	}

}