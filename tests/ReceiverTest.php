<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use Carbon\Carbon;
use Algorit\Synchronizer\Receiver;

class ReceiverTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->receiver = new Receiver;

		$this->response = [
			'error' => false,
			'data'  => [
				'foo' => 'bar'
			]
		];

		// $this->responseNull = [
		// 	'error' => false,
		// 	'data'  => null
		// ];
	}

	public function testReceiveFromErp()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Request\RequestInterface');

		$request->shouldReceive('receive')
				->once()
				->andReturn(true);

		$assert = $this->receiver->fromErp($request, $this->response, Mockery::type('string'));

		$this->assertTrue($assert);
	}

	public function testReceiveFromDatabase()
	{
		$repository = Mockery::mock('Algorit\Synchronizer\Request\Caller');

		$repository->shouldReceive('repository')
				   ->once()
				   ->andReturn(Mockery::mock(['get' => true]));

		$request = Mockery::mock('Algorit\Synchronizer\Request\RequestInterface');

		$request->shouldReceive('getCaller')
				->twice() // Using twice to call it from tests too.
				->andReturn($repository);

		$assert = $this->receiver->fromDatabase($request, Mockery::type('string'), Mockery::type('string'));

		$this->assertTrue($assert);
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Caller', $request->getCaller());
	}

	public function testReceiveFromApi()
	{
		$assert = $this->receiver->fromApi($this->response);
		$this->assertEquals($assert, $this->response);
	}
}