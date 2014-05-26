<?php namespace Algorit\Synchronizer;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

use Monolog\Handler\StreamHandler;
use Algorit\Synchronizer\Request\Config;

use Algorit\Synchronizer\Storage\Sync;
use Algorit\Synchronizer\Storage\SyncEloquentRepository;

class SynchronizerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('algorit/synchronizer');

		$this->app['algorit.synchronizer'] = $this->app->share(function($app)
		{
			$sync = new SyncEloquentRepository(new Sync);

			$builder = new Builder(new Sender, new Receiver, $sync);

			return new Loader($app, $builder, new Config(new Filesystem));
		});

		require 'helpers.php';

		$this->bootLogger();
	}

	public function bootLogger()
	{
		$logger = $this->app['log'];
			
		if($logger == false)
		{
			return false;
		}

		$handler = new StreamHandler('php://output');

		$monolog = $logger->getMonolog();
		$monolog->pushHandler($handler);

		$this->app['algorit.synchronizer']->setLogger($logger);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register(){}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('algorit.synchronizer');
	}

}