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

		$this->transport = Mockery::mock('Algorit\Synchronizer\Request\Transport');
		$this->method = Mockery::mock('Algorit\Synchronizer\Request\Methods\Requests');

		$this->entities = array(
	   		'receive' => [
	   			'products'  => array(
					'name' 	   => 'products',
					'url' 	   => 'v1/product'
				)
	   		],
	   		'send' => [
				'categories' => array(
					'name' 	   => 'categories',
					'url' 	   => 'v1/category'
				),
	   		]
		);
	}

	public function testInstances()
	{
		$request = new RequestStub($this->method, $this->transport);

		$this->assertInstanceOf('Algorit\Synchronizer\Request\Transport', $request->getTransport());
	}

	public function testSetEntity()
	{
		$request = new RequestStub($this->method, $this->transport);

		$config = Mockery::mock('Algorit\Synchronizer\Request\Config');
		$config->shouldReceive('getEntities')
			   ->once()
			   ->andReturn($this->entities);

		$request->setConfig($config);
		$request->setOptions('products');

		$this->assertEquals($this->entities['receive']['products'], $request->getOptions()->entity);
	}

	public function testSetOptions()
	{
		$request = new RequestStub($this->method, $this->transport);

		$config = Mockery::mock('Algorit\Synchronizer\Request\Config');
		$config->shouldReceive('getEntities')
			   ->once()
			   ->andReturn($this->entities);

		$request->setConfig($config);

		$now = Carbon::now();

		$request->setOptions('categories', $now, 'send');

		$options = $request->getOptions();


		$this->assertEquals($options->type, 'send');
		$this->assertEquals($options->entity, $this->entities['send']['categories']);
		$this->assertEquals($options->lastSync, $now);
	}

	public function testProcessRequestData()
	{
		$request = new RequestStub($this->method, $this->transport);

		$body = json_encode(array('bla' => true));

		$process = $request->processRequestData((object) ['body' => $body], function($data)
		{
			return $data;
		});

		$this->assertEquals(json_decode($body, true), $process);
	}

}