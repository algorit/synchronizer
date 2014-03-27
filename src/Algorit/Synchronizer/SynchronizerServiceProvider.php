<?php namespace Algorit\Synchronizer;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Storage\Sync;
use Algorit\Synchronizer\Storage\SyncEloquentRepository;

class SynchronizerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('algorit/synchronizer');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{	
		$this->app['synchronizer'] = $this->app->share(function($app)
		{
			$sync = $app->make('Algorit\Synchronizer\Storage\SyncEloquentRepository');

			$builder = new Builder(new Sender, new Receiver, $sync);

			return new Loader(new Container, $builder, new Config(new Filesystem));
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}