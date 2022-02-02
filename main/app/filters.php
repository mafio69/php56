<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/


App::before(function($request)
{
	$custom_referrer = Request::header('x-custom-referrer');
	$host_name = Request::getHttpHost();
	if($custom_referrer && $host_name != $custom_referrer)
	{
		URL::forceSchema('https');
		Request::instance()->headers->set('host', $custom_referrer);
	}
	/*
    if (Config::get('app.debug') == false  && !$request->secure()) {
        return Redirect::secure($request->path());
    }
	*/
});


App::after(function($request, $response)
{
    //
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
    if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
    return Auth::basic();
});

Route::filter('password.check', function(){
    $user = Auth::user();

    if($user->locked_at)
    {
        return Redirect::to('locked');
    }

    if (\Carbon\Carbon::now()->diffInDays($user->password_expired_at,false)<1 ){
        return Redirect::to('password');
    }
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
    if (Session::token() != Input::get('_token'))
    {
        Log::error('token', Input::all());
        throw new Illuminate\Session\TokenMismatchException;
    }
});


Route::filter('user.session_update', function(){
    if(Auth::check())
    {
        $permissions = [];
        foreach(Auth::user()->groups as $group) {
            foreach($group->permissions as $permission) {
                if(! isset($permissions[$permission->id])) {
                    $permissions[$permission->id] = $permission->short_name;
                }
            }
        }
        Session::put('permissions', $permissions);

        $owners = [];
        foreach (Auth::user()->owners as $owner)
        {
            $owners[] = $owner->id;
        }
        Session::put('owners', $owners);

        $companies = [];
        foreach (Auth::user()->vmanage_companies()->get() as $company)
        {
            $companies[] = $company->id;
        }
        Session::put('companies', $companies);
    }
});

Route::filter('permitted', function($t, $u, $permission){
    if(!Auth::user()->can($permission)) {
        throw new Idea\Exceptions\PermissionException;
    }
});

Route::filter('apikey', function($route, $request, $module_id){
    $api_key = Input::get('api_key');
    if(! $api_key){
        return Response::json(['error' => 'api_key_required'], 400);
    }

    $api_module_key = ApiModuleKey::where('api_module_id', $module_id)->where('api_key', $api_key)->first();

    if(! $api_module_key){
        return Response::json(['error' => 'api_key_invalid'], 401);
    }
});

Route::filter('apilog', function($route, $request){
    $api_key = Input::get('api_key');
    $api_module_key = ApiModuleKey::where('api_key', $api_key)->first();
    $user = JWTAuth::parseToken()->authenticate();

    ApiHistory::create([
        'api_module_id' => $api_module_key->api_module_id,
        'api_user_id' => $user ? $user->id : null,
        'request' => json_encode(['url' => Request::url(), 'method' => Request::getMethod(), 'parameters' => Input::all() ]),
        'ip' => Request::ip()
    ]);
});
