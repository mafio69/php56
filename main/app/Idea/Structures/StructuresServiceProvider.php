<?php namespace Idea\Structures;

use Illuminate\Support\ServiceProvider;

class StructuresServiceProvider extends ServiceProvider {

    /**
     * Register bindings with IoC container
     */
    public function register()
    {
        $this->app->bind(
            'Idea\Structures\StructureInterface',
            'Idea\Structures\GETVEHICLEDTAInput',
            'Idea\Structures\REGINSISSUEInput',
            'Idea\Structures\CHGISSUETYPEInput',
            'Idea\Structures\ADDISSUEFEEInput',
            'Idea\Structures\CLOSEISSUEInput',
            'Idea\Structures\REOPENISSUEInput',
            'Idea\Structures\GETASSETDTAInput',
            'Idea\Structures\CHKCONTSTATEInput'
        );
    }

}
