<?php namespace Algorit\Synchronizer\Tests\Request;

use Mockery;
use Carbon\Carbon;
use Algorit\Synchronizer\Tests\Stubs\EntityStub;
use Algorit\Synchronizer\Tests\SynchronizerTest;

class EntityTraitTest extends SynchronizerTest {

	public function setUp()
	{
		parent::setUp();

		$this->trait = new EntityStub;

		$this->entity = 'Products';
	}

	public function testSetGetEntity()
	{
		$this->trait->setEntity($this->entity);

		$this->assertEquals($this->entity, $this->trait->getEntity());
	}

	public function testGetEntityFromName()
	{
		$singular = $this->trait->getFromEntityName($this->entity);

		$this->assertEquals($singular, 'Product');
	}
	
}