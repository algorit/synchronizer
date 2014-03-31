<?php namespace Algorit\Synchronizer;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
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

		$this->registerMonolog();
	}

	public function registerMonolog()
	{
		$handler = new ConsoleHandler(new ConsoleOutput);
		$handler->setFormatter(new ConsoleFormatter);

		$logger = new Logger('Synchronizer');
		$logger->pushHandler($handler);

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