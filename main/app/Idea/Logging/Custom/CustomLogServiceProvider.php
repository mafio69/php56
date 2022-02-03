<?php

namespace Idea\Logging\Custom;


use Illuminate\Support\ServiceProvider;

class CustomLogServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('customLog', function()
        {
            return new CustomLog();
        });
    }
}