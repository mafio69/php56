<?php namespace Idea\Setting;

use Illuminate\Support\ServiceProvider;
use Idea\Setting\interfaces\LaravelFallbackInterface;

class SettingServiceProvider extends ServiceProvider {

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
        $this->package('idea/setting');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['setting'] = $this->app->share(function($app)
        {
            $path     = app_path().'/storage/meta';
            $filename = 'setting.json';
            
            return new Setting($path, $filename, true ? new LaravelFallbackInterface() : null);
        });
        
        $this->app->booting(function($app)
        {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Setting', 'Idea\Setting\Facades\Setting');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('setting');
    }

}
