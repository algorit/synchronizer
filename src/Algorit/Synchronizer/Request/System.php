<?php namespace Algorit\Synchronizer\Request;

// Set configuration place
use Algorit\Synchronizer\Traits\ConfigTrait;
use Algorit\Synchronizer\Traits\ResourceTrait;
use Algorit\Synchronizer\Methods\Requests as RequestMethod;
use Algorit\Synchronizer\Request\Contracts\SystemInterface;

abstract class System implements SystemInterface{

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
	protected $namespace;

	/**
	 * The system resource (Company, device, representative...)
	 *
	 * @var object
	 */
	protected $resource;

	/**
	 * The resource config path
	 *
	 * @var string
	 */
	protected $path;

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
		return new $this->request(new RequestMethod, 
								  new Repository($this->namespace),
								  new Parser(new Filesystem, $this->namespace));
	}

}