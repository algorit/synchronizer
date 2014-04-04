<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use Carbon\Carbon;
use Algorit\Synchronizer\Sender;

class SenderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->sender = new Sender;

		$this->response = [
			'error' => false,
			'data'  => [
				'foo' => 'bar'
			]
		];

		$this->responseNull = [
			'error' => false,
			'data'  => null
		];
	}

	public function testSendNullData()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');

		$erp = $this->sender->toErp($request, array(), $this->responseNull);
		$database = $this->sender->toDatabase($request, array(), $this->responseNull);

		$this->assertNull($erp['data']);
		$this->assertNull($database['data']);
	}

	public function testSendToErp()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');

		$request->shouldReceive('send')
				->once()
				->andReturn(true);

		$assert = $this->sender->toErp($request, array(), $this->response);

		$this->assertTrue($assert);
	}

	public function testSendToDatabase()
	{
		$repository = Mockery::mock('Algorit\Synchronizer\Request\Caller');

		$repository->shouldReceive('repository')
				   ->once()
				   ->andReturn(Mockery::mock(['sync' => true]));

		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');

		$request->shouldReceive('getCaller')
				->twice() // Using twice to call it from tests too.
				->andReturn($repository);

		$assert = $this->sender->toDatabase($request, array(), $this->response);

		$this->assertTrue($assert);
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Caller', $request->getCaller());
	}

	public function testSendToApi()
	{
		$assert = $this->sender->toApi($this->response);

		$this->assertEquals($assert, $this->response);
	}

	public function testSendToApiClosure()
	{
		$assert = $this->sender->toApi($this->response, function($data)
		{
			return json_encode($data);
		});

		$this->assertJson($assert);
	}	

}