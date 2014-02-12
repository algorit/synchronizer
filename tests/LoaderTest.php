<?php namespace Algorit\Synchronizer\Tests;

use Algorit\Synchronizer\Tests\Stubs\ExampleSystem;

class RequestLoaderTest extends SynchronizerTest {

	/**
	 * Test if Laravel is correctly instantianting the class.
	 *
	 * @param  null
	 * @return assert
	 */
	public function testInstance()
	{
		$this->assertInstanceOf('Algorit\Synchronizer\Loader', $this->synchronizer);
	}

	/**
	 * Test if Laravel is injecting the class dependencies
	 *
	 * @param  null
	 * @return assert
	 */
	public function testBuilderInstance()
	{
		$this->assertInstanceOf('Algorit\Synchronizer\Builder', $this->synchronizer->getBuilder());
	}

	public function testLoadSystem()
	{

		$this->synchronizer->loadSystem(new ExampleSystem);

	}

}