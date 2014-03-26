<?php namespace Algorit\Synchronizer\Tests\Request;

use Mockery;
use StdClass;
use Exception;
use Carbon\Carbon;
use Algorit\Synchronizer\Tests\Stubs\Request as RequestStub;
use Algorit\Synchronizer\Tests\SynchronizerTest;

class RequestTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->repository = Mockery::mock('Algorit\Synchronizer\Request\Repository');
		$this->parser = Mockery::mock('Algorit\Synchronizer\Request\Parser');
		$this->method = Mockery::mock('Algorit\Synchronizer\Request\Methods\Requests');

		$this->entities = array(
	   		'receive' => [
	   			'products'	 => array(
					'name' 	   => 'products',
					'type'	   => 'json',
					// 'filename' => 'products',
					'method'   => 'get',
					'url' 	   => 'v1/product'
				),
	   		]
		);
	}

	public function testInstances()
	{
		$request = new RequestStub($this->method, $this->repository, $this->parser);

		$this->assertInstanceOf('Algorit\Synchronizer\Request\Parser', $request->getParser());
		$this->assertInstanceOf('Algorit\Synchronizer\Request\Repository', $request->getRepository());
	}

	public function testSetEntity()
	{
		$config = Mockery::mock('Algorit\Synchronizer\Request\Config');

		$config->shouldReceive('getEntities')
			   ->once()
			   ->andReturn($this->entities);

		$request = new RequestStub($this->method, $this->repository, $this->parser);

		$request->setConfig($config);
		$request->setRequestOptions('products');

		$this->assertEquals($this->entities['receive']['products'], $request->getEntity());
	}

}