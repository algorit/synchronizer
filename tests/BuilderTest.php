<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use StdClass;
use Carbon\Carbon;
use Algorit\Synchronizer\Builder;

class RequestBuilderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$lastSync = new StdClass;
		$lastSync->created_at = Carbon::now();

		$repository = Mockery::mock('Algorit\Synchronizer\Storage\SyncInterface');
		$repository->shouldReceive('create')->andReturn($repository);
		$repository->shouldReceive('getLastSync')->andReturn($lastSync);
		$repository->shouldReceive('touchCurrentSync')->andReturn(true);
		$repository->shouldReceive('updateFailedSync')->andReturn(true);
		$repository->shouldReceive('updateCurrentSync')->andReturn(true);
		$repository->shouldReceive('setCurrentSync')->andReturn($repository);

		$receiver = Mockery::mock('Algorit\Synchronizer\Receiver');
		$receiver->shouldReceive('fromErp')->once()->andReturn(array());
		$receiver->shouldReceive('fromDatabase')->once()->andReturn(array());

		$sender = Mockery::mock('Algorit\Synchronizer\Sender');
		$sender->shouldReceive('toErp')->once()->andReturn(true);
		$sender->shouldReceive('toApi')->once()->andReturn(true);
		$sender->shouldReceive('toDatabase')->once()->andReturn(true);

		$this->builder = new Builder($sender, $receiver, $repository);

		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');
		$resource = Mockery::mock('Algorit\Synchronizer\Request\Contracts\ResourceInterface');

		$this->builder->start($request, $resource);
	}

	public function testStart()
	{
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\RequestInterface', $this->builder->getRequest());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\ResourceInterface', $this->builder->getResource());
	}

	public function testFromErpToDatabase()
	{
		$try = $this->builder->fromErpToDatabase('stub');

		$this->assertTrue($try);
	}

	public function testFromDatabaseToErp()
	{
		$try = $this->builder->fromDatabaseToErp('stub');

		$this->assertTrue($try);
	}

	public function testFromDatabaseToApi()
	{
		$try = $this->builder->fromDatabaseToApi('stub');

		$this->assertTrue($try);
	}

	public function testToApiToDatabase()
	{
		$try = $this->builder->fromApiToDatabase(array(), 'stub');

		$this->assertTrue($try);
	}

}