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

	public function __construct()
	{
		$this->setup();
	}

	private function setup()
	{
		$reflector = new ReflectionClass(get_class($this));
        
        $this->path = dirname($reflector->getFileName());

        $this->name = $reflector->getName();

        $this->namespace = $reflector->getNamespaceName();
	}

	public function setRequest($name)
	{
		$this->request = $this->namespace . '\\' . $name;
	}

	public function loadRequest()
	{
		if( ! isset($this->request))
		{
			$this->setRequest('Request');
		}

		return new $this->request(new RequestMethod, new Repository($this->namespace), new Parser(new Filesystem, $this->namespace));
	}

}