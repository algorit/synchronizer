<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use Algorit\Synchronizer\Loader;
use Algorit\Synchronizer\Tests\Stubs\ExampleSystem;

class RequestLoaderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$builder = Mockery::mock('Algorit\Synchronizer\Builder');
		$builder->shouldReceive('start');

		$config  = Mockery::mock('Algorit\Synchronizer\Request\Config');
		$config->shouldReceive('setup')->andReturn($config);

		$this->loader = new Loader($builder, $config);

		$this->request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');
		$this->request->shouldReceive('setConfig')->andReturn(array());
		$this->request->shouldReceive('setResource')->andReturn(array());

		$this->system = Mockery::mock('Algorit\Synchronizer\Tests\Stubs\ExampleSystem');
		$this->system->shouldReceive('loadRequest')->andReturn($this->request);
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
		$resource = Mockery::mock('Algorit\Synchronizer\Request\Contracts\ResourceInterface');

		$this->loader->loadSystem($this->system)->start($resource);

		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\SystemInterface', $this->loader->getSystem());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\RequestInterface', $this->loader->getRequest());
	}

}