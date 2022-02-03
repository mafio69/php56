<?php

namespace Idea\Logging\Custom;


use Illuminate\Support\Facades\Facade;

class CustomLogFacade extends Facade{

    protected static function getFacadeAccessor() { return 'customLog'; }
}