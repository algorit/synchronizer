<?php namespace Algorit\Synchronizer\Request;

// Set configuration place
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

	public function __construct($data = array())
	{
		if($this->name == false)
		{
			$this->setName();
		}
	}

	private function setName()
	{
		$name = explode('\\', get_class($this));

		$this->name = end($name);
	}

	public function loadRequest()
	{
		return new $this->request(new RequestMethod, new Repository($this->namespace), new Parser(new Filesystem, $this->namespace));
	}

}