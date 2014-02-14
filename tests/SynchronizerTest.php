<?php namespace Algorit\Synchronizer\Tests;

use Config, Artisan;
use Orchestra\Testbench\TestCase;

class SynchronizerTest extends TestCase {

	protected function getPackageProviders()
	{
		return ['Algorit\Synchronizer\SynchronizerServiceProvider'];
	}

	public function setUp()
	{
		parent::setUp();

		$this->synchronizer = $this->app['synchronizer'];
	}

	protected function prepare()
	{
		Config::set('database.connections', array(
			'sqlite' => array(
	            'driver'   => 'sqlite',
	            'database' => ':memory:',
	            'prefix'   => ''
	        )
        ));

		Config::set('database.default', 'sqlite');

		Artisan::call('migrate', array('--package' => 'Algorit/Synchronizer'));
	}
}