<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use Algorit\Synchronizer\Loader;
use Illuminate\Support\Collection;
use Algorit\Synchronizer\Container;
use Algorit\Synchronizer\Tests\Stubs\System as SystemStub;
use Algorit\Synchronizer\Tests\Stubs\Resource as ResourceStub;

class LoaderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$builder = Mockery::mock('Algorit\Synchronizer\Builder');
		$builder->shouldReceive('start');
				// ->once();

		$config = Mockery::mock('Algorit\Synchronizer\Request\Config');
		$config->shouldReceive('setup')
			   // ->once();
			   ->andReturn($config);

		$this->loader = new Loader(new Container, $builder, $config);
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Algorit\Synchronizer\Loader', $this->loader);
	}

	public function testBuilderInstance()
	{
		$this->assertInstanceOf('Algorit\Synchronizer\Builder', $this->loader->getBuilder());
	}

	public function testLogger()
	{
		$this->loader->setLogger(Mockery::mock('Psr\Log\LoggerInterface'));

		$this->assertInstanceOf('Psr\Log\LoggerInterface', $this->loader->getLogger());
	}

	public function testSystemInstance()
	{
		$resource = Mockery::mock('Algorit\Synchronizer\Request\Contracts\ResourceInterface');

		$this->loader->loadSystem(new SystemStub(new ResourceStub))
					 ->start($resource);

		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\SystemInterface', $this->loader->getSystem());
	}

	public function testRequestInstance()
	{
		$resource = Mockery::mock('Algorit\Synchronizer\Request\Contracts\ResourceInterface');

		$this->loader->loadSystem(new SystemStub(new ResourceStub))
					 ->start($resource);

		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\RequestInterface', $this->loader->getRequest());
	}

	public function testResourceInstance()
	{
		$this->loader->loadSystem(new SystemStub(new ResourceStub));

		$this->assertInstanceOf('Algorit\Synchronizer\Tests\Stubs\Resource', $this->loader->getSystem()->getResource());
	}

	public function testCollectionAsResource()
	{
		$this->loader->loadSystem(new SystemStub(new Collection));

		$this->assertInstanceOf('Illuminate\Support\Collection', $this->loader->getSystem()->getResource());
	}

	/**
	 * @expectedException Exception
	 */
	public function testExceptionWithFalseResource()
	{
		$this->loader->loadSystem(new SystemStub(false));
	}

}