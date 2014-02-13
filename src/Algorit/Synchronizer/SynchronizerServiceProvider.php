<?php namespace Algorit\Synchronizer;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Algorit\Synchronizer\Request\Config;
use Algorit\Synchronizer\Storage\SyncEntity;
use Algorit\Synchronizer\Storage\SyncEloquentRepository as SyncRepository;

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
		// Using Requests as RequestMethod (https://github.com/rmccue/Requests)
		// $this->app->bind('Algorit\Synchronizer\Methods\MethodInterface', 'Algorit\Synchronizer\Methods\Requests');
		// $this->app->bind('Algorit\Synchronizer\Storage\SyncInterface', 'Algorit\Synchronizer\Storage\SyncEloquentRepository');

		// $repository = new SyncRepository(new SyncEntity);
		// $builder    = new Builder(new Sender, new Receiver, $repository);
		// $config     = new Config(new Filesystem);
		// $loader     = new Loader($builder, $config);

		$this->app['synchronizer'] = $this->app->share(function($app)
		{
			$repository = $app['config']->get('synchronizer::repository.instance');

			if($repository == false)
			{
				$sync = new SyncRepository(new SyncEntity);
			}
			else
			{
				$sync = $app->make($repository);
			}

			$builder = new Builder(new Sender, new Receiver, $sync);

			return new Loader($builder, new Config($app['files']));
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