<?php  namespace Idea\Webservice; 

use Illuminate\Support\ServiceProvider;

class WebserviceServiceProvider extends ServiceProvider{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('webservice', 'Idea\Webservice\Webservice');
    }
}