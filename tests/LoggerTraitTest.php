<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use Algorit\Synchronizer\Tests\Stubs\LoggerStub;

class LoggerTraitTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->trait = new LoggerStub;
	}

	public function testSetLogger()
	{
		$logger = Mockery::mock('Psr\Log\LoggerInterface');

		$this->trait->setLogger($logger);

		$this->assertInstanceOf('Psr\Log\LoggerInterface', $this->trait->getLogger());
	}
	
}