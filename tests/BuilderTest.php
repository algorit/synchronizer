<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use StdClass;
use Exception;
use Carbon\Carbon;
use Algorit\Synchronizer\Builder;

class BuilderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->request  = Mockery::mock('Algorit\Synchronizer\Request\RequestInterface');

		$options = (object) [
			'url' 	   => 'test',
			'lastSync' => Mockery::mock(array('toDateTimeString' => true))
		];
		
		$this->request->shouldReceive('getOptions')
			 		  ->andReturn($options);

		$this->logger = Mockery::mock('Illuminate\Log\Writer');
		
		$this->logger->shouldReceive('info')
					 ->andReturn(true);

		$this->logger->shouldReceive('error')
					 ->andReturn(true);

		$this->resource = Mockery::mock('Algorit\Synchronizer\Request\Contracts\ResourceInterface');
	}

	private function getMockedRepository()
	{
		$lastSync = (object) ['created_at' => Carbon::now()];

		$repository = Mockery::mock('Algorit\Synchronizer\Storage\SyncRepositoryInterface');

		$repository->shouldReceive('create')
				   ->once()
				   ->andReturn($repository);

		$repository->shouldReceive('getLastSync')
				   ->once()
				   ->andReturn($lastSync);

		$repository->shouldReceive('touchCurrentSync')
				   ->once()
				   ->andReturn(true);

		$repository->shouldReceive('updateCurrentSync')
				   ->once()
				   ->andReturn(true);

		$repository->shouldReceive('setCurrentSync')
				   ->once()
				   ->andReturn($repository);

		return $repository;
	}

	public function testStart()
	{
		$sender = Mockery::mock('Algorit\Synchronizer\Sender');
		$receiver = Mockery::mock('Algorit\Synchronizer\Receiver');
		$repository = Mockery::mock('Algorit\Synchronizer\Storage\SyncRepositoryInterface');

		$builder = new Builder($sender, $receiver, $repository);
		$builder->start($this->request, $this->resource);

		$this->assertInstanceOf('Algorit\Synchronizer\Request\RequestInterface', $builder->getRequest());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Contracts\ResourceInterface', $builder->getResource());
	}

	public function testFromErpToDatabase()
	{	
		$receiver = Mockery::mock('Algorit\Synchronizer\Receiver');

		// Mock the fromErp method on Receiver class
		$receiver->shouldReceive('fromErp')
				 ->once()
				 ->andReturn(array());

		$sender = Mockery::mock('Algorit\Synchronizer\Sender');

		// Mock the toDatabase method on Sender class
		$sender->shouldReceive('toDatabase')
			   ->once()
			   ->andReturn(array());

		$builder = new Builder($sender, $receiver, $this->getMockedRepository());
		$builder->setLogger($this->logger);
		$builder->start($this->request, $this->resource);

		$assert = $builder->fromErpToDatabase(Mockery::type('string'));

		$this->assertArrayHasKey('response', $assert); 
	}

	public function testFromDatabaseToErp()
	{
		$receiver = Mockery::mock('Algorit\Synchronizer\Receiver');

		// Mock the fromDatabase method on Receiver class
		$receiver->shouldReceive('fromDatabase')
				 ->once()
				 ->andReturn(array());
				 
		$sender = Mockery::mock('Algorit\Synchronizer\Sender');

		// Mock the toErp method on Sender class
		$sender->shouldReceive('toErp')
			   ->once()
			   ->andReturn(array());

		$builder = new Builder($sender, $receiver, $this->getMockedRepository());
		$builder->setLogger($this->logger);
		$builder->start($this->request, $this->resource);

		$assert = $builder->fromDatabaseToErp(Mockery::type('string'));

		$this->assertArrayHasKey('response', $assert);
	}

	public function testFromDatabaseToApi()
	{
		$receiver = Mockery::mock('Algorit\Synchronizer\Receiver');

		// Mock the fromDatabase method on Receiver class
		$receiver->shouldReceive('fromDatabase')
				 ->once()
				 ->andReturn(array());
				 
		$sender = Mockery::mock('Algorit\Synchronizer\Sender');

		// Mock the toApi method on Sender class
		$sender->shouldReceive('toApi')
			   ->once()
			   ->andReturn(array());

		$builder = new Builder($sender, $receiver, $this->getMockedRepository());
		$builder->setLogger($this->logger);
		$builder->start($this->request, $this->resource);

		$assert = $builder->fromDatabaseToApi(Mockery::type('string'));

		$this->assertArrayHasKey('response', $assert);
	}

	public function testFromApiToDatabase()
	{
		$receiver = Mockery::mock('Algorit\Synchronizer\Receiver');
		$sender = Mockery::mock('Algorit\Synchronizer\Sender');

		// Mock the toDatabase method on Sender class
		$sender->shouldReceive('toDatabase')
			   ->once()
			   ->andReturn(array());

		$builder = new Builder($sender, $receiver, $this->getMockedRepository());
		$builder->setLogger($this->logger);
		$builder->start($this->request, $this->resource);

		$assert = $builder->fromApiToDatabase(array(), Mockery::type('string'));

		$this->assertArrayHasKey('response', $assert);
	}

	public function testFailedSync()
	{
		$receiver = Mockery::mock('Algorit\Synchronizer\Receiver');

		$receiver->shouldReceive('fromErp')
				 ->once()
				 ->andReturn(array());

		$sender = Mockery::mock('Algorit\Synchronizer\Sender');

		// We will throw an exception here
		// it will be cought on process method.
		// $sender->shouldReceive('toDatabase')
		// 	   ->once()
		// 	   ->andThrow(new Exception('An error ocurred!'));

		$repository = Mockery::mock('Algorit\Synchronizer\Storage\SyncRepositoryInterface');

		$repository->shouldReceive('create')
				   ->once()
				   ->andReturn(false);

		$repository->shouldReceive('setCurrentSync')
				   ->once()
				   ->andReturn($repository);

		$repository->shouldReceive('getLastSync')
				   ->once()
				   ->andReturn(false);

		$repository->shouldReceive('touchCurrentSync')
				   ->once()
				   ->andReturn(false);

		$repository->shouldReceive('updateFailedSync')
				   ->once()
				   ->andReturn(false);

		$builder = new Builder($sender, $receiver, $repository);
		$builder->setLogger($this->logger);
		$builder->start($this->request, $this->resource);

		$assert = $builder->fromErpToDatabase(false);

		$this->assertFalse($assert);
	}

}