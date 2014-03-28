<?php namespace Algorit\Synchronizer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Algorit\Synchronizer\Storage\Sync;
use Algorit\Synchronizer\Request\Config;
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

		$logger = new Logger('Sync');
		$logger->pushHandler(new StreamHandler('php://output', Logger::DEBUG));

		$this->app['synchronizer']->setLogger($logger);
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