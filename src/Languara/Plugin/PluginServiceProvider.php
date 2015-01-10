<?php namespace Languara\Plugin;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider {

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
		$this->package('languara/plugin');
        
        include __DIR__.'/routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->package('languara/plugin');
        
        $this->app['languara.pull'] = $this->app->share(function(){
            return new Commands\LanguaraPull;
        });
        $this->app['languara.push'] = $this->app->share(function(){
            return new Commands\LanguaraPush;
        });
        
        $this->commands(array(
            'languara.push',
            'languara.pull',
        ));
        include __DIR__ . '/routes.php';
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
