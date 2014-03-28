<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use StdClass;
use Exception;
use Carbon\Carbon;
use Algorit\Synchronizer\Container;

class ContainerTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->container = new Container;
	}

	public function testSetGetNamespace()
	{
		$namespace = 'Algorit\Synchronizer';

		$this->container->setNamespace($namespace);

		$this->assertEquals($namespace, $this->container->getNamespace());
	}

}