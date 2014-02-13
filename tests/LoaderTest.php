<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use Algorit\Synchronizer\Loader;
use Algorit\Synchronizer\Tests\Stubs\ExampleSystem;

class RequestLoaderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$builder = Mockery::mock('Algorit\Synchronizer\Builder');

		$config  = Mockery::mock('Algorit\Synchronizer\Request\Config');
		$config->shouldReceive('setup')->andReturn($config);

		$this->loader = new Loader($builder, $config);
	}

	/**
	 * Test if Laravel is correctly instantianting the class.
	 *
	 * @param  null
	 * @return assert
	 */
	public function testInstance()
	{
		$this->assertInstanceOf('Algorit\Synchronizer\Loader', $this->loader);
	}

	/**
	 * Test if Laravel is injecting the class dependencies
	 *
	 * @param  null
	 * @return assert
	 */
	public function testBuilderInstance()
	{
		$this->assertInstanceOf('Algorit\Synchronizer\Builder', $this->loader->getBuilder());
	}

	public function testLoadSystemInstance()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Tests\Stubs\Request');
		$request->shouldReceive('setConfig')->andReturn(array());

		$system = Mockery::mock('Algorit\Synchronizer\Tests\Stubs\ExampleSystem');
		$system->shouldReceive('loadRequest')->andReturn($request);

		$this->loader->loadSystem($system);

		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\SystemInterface', $this->loader->getSystem());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\RequestInterface', $this->loader->getRequest());
	}

	public function testLoaderMock()
	{
		// $loader = Mockery::mock('Algorit\Synchronizer\Loader');
		// $loader->shouldReceive('loadSystem')->andReturn($loader);
	}

}