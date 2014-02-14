<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use StdClass;
use Carbon\Carbon;
use Algorit\Synchronizer\Builder;

class RequestBuilderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$repository = Mockery::mock('Algorit\Synchronizer\Storage\SyncInterface');

		$repository->shouldReceive('setCurrentSync')->andReturn($repository);
		$repository->shouldReceive('create')->andReturn($repository);

		$lastSync = new StdClass;
		$lastSync->created_at = Carbon::now();

		$repository->shouldReceive('getLastSync')->andReturn($lastSync);

		$sender     = Mockery::mock('Algorit\Synchronizer\Sender');
		$receiver   = Mockery::mock('Algorit\Synchronizer\Receiver');

		$this->builder  = new Builder($sender, $receiver, $repository);
	}

	public function testStart()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');

		$this->builder->start($request, 'company');
	}

	/**
	 * Test if Laravel is correctly instantianting the class.
	 *
	 * @param  null
	 * @return assert
	 */
	public function testInstance()
	{
		// $this->assertInstanceOf('Algorit\Synchronizer\Builder', $this->loader->getBuilder());
	}

	/**
	 * Test if the correct system was instantiated.
	 *
	 * @param  null
	 * @return assert
	 */
	public function testSystem()
	{
		// $this->assertTrue($this->loader->getSystem() instanceof DeltaconRequest);
	}

}