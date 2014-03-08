<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use Carbon\Carbon;
use Algorit\Synchronizer\Sender;

class SenderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->sender = new Sender;
	}

	public function testSendToErp()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');

		$request->shouldReceive('send')
				->once()
				->andReturn(true);

		$response = [
			'error' => false,
			'data'  => [
				'foo' => 'bar'
			]
		];

		$assert = $this->sender->toErp($request, array(), $response);

		$this->assertTrue($assert);
	}

	public function testSendToErpWithNoData()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');

		$response = [
			'error' => false,
			'data'  => null
		];

		$assert = $this->sender->toErp($request, array(), $response);

		$this->assertNull($assert['data']);
	}



}