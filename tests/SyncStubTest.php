<?php namespace Algorit\Synchronizer\Tests;

use Algorit\Synchronizer\Tests\Stubs\ExampleSystem;

class SyncStubTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();
	}

	public function testInstance()
	{
		$loader = $this->synchronizer->loadSystem(new ExampleSystem);

		$this->assertInstanceOf('Algorit\Synchronizer\Builder', $loader->getBuilder());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\SystemInterface', $loader->getSystem());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\RequestInterface', $loader->getRequest());
	}

}