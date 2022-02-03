<?php  namespace Idea\Webservice\Facades;

use Illuminate\Support\Facades\Facade;

class Webservice extends Facade{

    protected static function getFacadeAccessor()
    {
        return 'webservice';
    }
}