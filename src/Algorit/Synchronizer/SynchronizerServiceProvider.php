<?php namespace Algorit\Synchronizer;

use Illuminate\Support\ServiceProvider;

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
		$this->app->bind('Algorit\Synchronizer\Contracts\RequestMethodInterface', 'Algorit\Synchronizer\Methods\Requests');

		$this->app['synchronizer'] = $this->app->share(function($app)
        {
        	$sync = $app->make('Application\Storage\Contracts\SyncInterface');

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