<?php namespace Algorit\Synchronizer\Tests;

use Algorit\Synchronizer\Tests\Stubs\ExampleSystem;

class SyncStubTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->loader = $this->synchronizer->loadSystem(new ExampleSystem);
	}

	public function testInstance()
	{

		$this->assertInstanceOf('Algorit\Synchronizer\Builder', $this->loader->getBuilder());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\SystemInterface', $this->loader->getSystem());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\RequestInterface', $this->loader->getRequest());
	}

}