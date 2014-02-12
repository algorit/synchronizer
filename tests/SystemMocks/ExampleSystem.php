<?php namespace Algorit\Synchronizer\Tests\SystemMocks;

use Mockery;
use Algorit\Synchronizer\Request\System;

class ExampleSystem extends System {

	public $namespace = 'Algorit\Synchronizer\Tests';

	public $path = __DIR__;

	public function __construct()
	{
		$this->request = $this->namespace . '\\RequestExample';

		parent::__construct();
	}

}