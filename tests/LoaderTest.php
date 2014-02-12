<?php namespace Algorit\Synchronizer;

// use Synchronizer\Loader;
// use Synchronizer\Builder;

class RequestLoaderTest extends SynchronizerTest {

	// public function setUp()
	// {
	// 	parent::setUp();
		
	// 	$this->loader = $this->app->make('Synchronizer\Loader');
	// }

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

}