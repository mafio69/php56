<?php

    use Carbon\Carbon;

    class LoginController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function getLogin(){
        return View::make('login.main');
    }

    public function postLogin(){

        $input = Input::all();

        $validator = Validator::make($input ,
            array(
                'login' => 'required',
                'password' => 'required'
            )
        );


        if($validator -> fails()){
            Flash::error('Login i hasło jest wymagany.');
            return Redirect::route('login-main')->withInput();
        }else{
            $login = Input::get('login');
            $user = User::where('login',$login)->first();
            if($user){
                if(!Hash::check(Input::get('password'), $user->password)){
                    $user->failed_attempts = $user->failed_attempts+1;
                    $user->save();
                    if($user->failed_attempts>3){
                        $user->password_expired_at = \Carbon\Carbon::now();
                        $user->save();
                        return Redirect::back()->withInput(Input::except('password'))->with('status',['type'=>'danger','text'=>'Przekroczyłęś limit błędnych logowań, twoje konto zostało zablokowane. W celu odblokowania skontaktuj się z administratorem ']);
                    }
                    return Redirect::back()->withInput(Input::except('password'))->with('status',['type'=>'danger','text'=>'Podane hasło jest niepoprawne']);
                }
            }
            else{
                return Redirect::back()->withInput(Input::except('password'))->with('status',['type'=>'danger','text'=>'Podany użytkownik nieistnieje']);
            }
            $user->failed_attempts=0;
            $user->save();

            $auth = Auth::attempt(array(
                'login' => Input::get('login'),
                'password' => Input::get('password'),
                'active' => 0
            ));

            if($auth){

                if($user->active_to && $user->active_to->startOfDay()->lte(Carbon::now()))
                {
                    $user->locked_at = \Carbon\Carbon::now();
                    $user->save();
                    return Redirect::intended();
                }

                $modules = Module::get()->toArray();
                \Session::set('app_modules', $modules);

                $user->update(['failed_attempts' => 0]);

                $user->logins()->create([
                    'ip' => Request::ip()
                ]);

                $permissions = [];
                foreach($user->groups as $group) {
                    foreach($group->permissions as $permission) {
                        if(! isset($permissions[$permission->id])) {
                            $permissions[$permission->id] = $permission->short_name;
                        }
                    }
                }
                Session::put('permissions', $permissions);

                return Redirect::intended();
            }else{
                Flash::error('Podano błędny login lub hasło.');
                return Redirect::route('login-main')->withInput();
            }
        }
    }

}
