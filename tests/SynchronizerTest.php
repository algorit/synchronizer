<?php namespace Algorit\Synchronizer\Tests;

use Mockery;
use PHPUnit_Framework_TestCase;

class SynchronizerTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		date_default_timezone_set('America/Sao_Paulo');

		parent::setUp();
	}

	public function tearDown()
	{
		Mockery::close();
	}
}