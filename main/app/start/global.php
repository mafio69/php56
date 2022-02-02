<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/
Session::set('avoid_query_logging', false);
Date::setLocale('pl');

ClassLoader::addDirectories(array(

    app_path().'/commands',
    app_path().'/controllers',
    app_path().'/models',
    app_path().'/database/seeds',
    app_path().'/classes',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useDailyFiles(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER').'/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/
App::missing(function($exception)
{
    Log::error($exception);
    if(Config::get('app.debug') == false) {
        if(Auth::user())
            $user = Auth::user()->login;
        else
            $user = '';

        if(Session::has('prev'))
            $prev = Session::get('prev');
        else
            $prev = URL::previous();


        $data = array(
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'msg' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'user' => $user,
            'prev' => $prev
        );
        Mail::send('emails.errors.error', $data, function($message)
        {
            $message->to(Config::get('webconfig.WEBCONFIG_SETTINGS_errors'))->subject('[IdeaLeasing] Error notification. Site not found.');
        });

    }
    return Response::view('layouts.error-missing', array(), 404);
});

App::error(function(Tymon\JWTAuth\Exceptions\JWTException $e, $code)
{
    if ($e instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return Response::json(['token_expired'], $e->getStatusCode());
    } else if ($e instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return Response::json(['token_invalid'], $e->getStatusCode());
    }
});

App::error(function(Exception $exception, $code)
{
    Log::error($exception);

    if( Config::get('app.debug') == false) {
        if(Auth::user())
            $user = Auth::user()->login;
        else
            $user = '';

        if(Session::has('prev'))
            $prev = Session::get('prev');
        else
            $prev = URL::previous();

        $request = Request::except('_token');

        $data = array(
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'msg' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'user' => $user,
            'prev' => $prev,
            'request' => $request
        );

        Mail::send('emails.errors.error', $data, function($message)
        {
            $message->to(Config::get('webconfig.WEBCONFIG_SETTINGS_errors'))->subject('[IdeaLeasing] Error notification');
        });

    }

    if($exception instanceof SoapFault)
    {
        Settings::set('as-connection','inactive');
        if(Request::ajax())
            return Response::make('wykryto błąd połączenia z AS', 400);

        Flash::error('Wykryto błąd połączenia z AS');
        return Redirect::to('/');
    }elseif($exception instanceof \Idea\Exceptions\PermissionException)
    {
        Flash::error('Brak wymaganych uprawnień do przeglądania tej strony.');
        return Redirect::to('/');
    }elseif(Request::ajax()){
        return Response::json(['error' => 'wystąpił błąd w trakcie wykonywania' ]);
    }

	if( Config::get('app.debug') == false ) {
		return View::make('layouts.error');
	}
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
    //return View::make('layouts.be-right-back');
    return Response::make("Be right back!", 503);
});

Event::listen('illuminate.query', function ($sql, $bindings, $time) {
    if (Config::get('webconfig.database_log', false)) {
        if(strpos($sql, 'sessions') === false) {
            if(mb_strtolower(substr(trim(preg_replace('/\s\s+/', ' ',$sql)), 0, 6)) != 'select' || Config::get('webconfig.database_log_select', false)) {
                // To get the full sql query with bindings inserted
                $sql = str_replace(array('%', '?'), array('%%', '%s'), $sql);
                $full_sql = vsprintf($sql, $bindings);

                $dateNow = explode('-', date("Y-m-d-H-i-s"));
                $dateNow = array(
                    'year' => $dateNow[0],
                    'month' => $dateNow[1],
                    'day' => $dateNow[2],
                    'hour' => $dateNow[3],
                    'minute' => $dateNow[4],
                    'second' => $dateNow[5]
                );
                $logDir = $dateNow['year'] . '-' . $dateNow['month'];

                if (!is_dir(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER') . '/' . $logDir)) {
                    mkdir(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER') . '/' . $logDir, 0777, true);
                }

                if (isset($_SERVER['REMOTE_ADDR'])) {
                    fwrite(fopen(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER') . "/" . $logDir . "/queries.log", "a"), "~" . date("Y-m-d, H:i:s", time()) . "~" . $_SERVER['REMOTE_ADDR'] . "~" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "~" . $full_sql . "'~\n");
                } else {
                    fwrite(fopen(Config::get('webconfig.WEBCONFIG_LOGS_FOLDER') . "/" . $logDir . "/queries.log", "a"), "~" . date("Y-m-d, H:i:s", time()) . "~" . $full_sql . "'~\n");
                }
            }
        }
    }
});

Injury::observe(new \Idea\Observers\InjuryObserver);
GapAgreement::observe(new \Idea\Observers\GapAgreementObserver);
Company::observe(new \Idea\Observers\CompanyObserver);
CompanyVatCheck::observe(new \Idea\Observers\CompanyObserver);
TaskInstance::observe(new \Idea\Observers\TaskInstanceObserver);
Task::observe(new \Idea\Observers\TaskObserver);
InjuryWreck::observe(new \Idea\Observers\InjuryWreckObserver);
InjuryTheft::observe(new \Idea\Observers\InjuryTheftObserver);

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';
