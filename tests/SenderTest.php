<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use StdClass;
use Carbon\Carbon;
use Algorit\Synchronizer\Builder;

class SenderTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

	}

	public function testSendToErp()
	{
		$request = Mockery::mock('Algorit\Synchronizer\Request\Contracts\RequestInterface');
		$request->shouldReceive('toErp');
	}



}