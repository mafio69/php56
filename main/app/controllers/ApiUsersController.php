<?php

class ApiUsersController extends \BaseController {

    public function getIndex()
    {
        $users = ApiUser::with('apiModules')->paginate(Session::get('search.pagin', '10'));

        return View::make('settings.api.users.index', compact('users'));
    }

    public function getCreate()
    {
        return View::make('settings.api.users.create');
    }

    public function postStore()
    {
        $validator = Validator::make(Input::all(), [
            'name' => 'required|max:255',
            'login' => 'required|max:255|unique:api_users'
        ]);

        if($validator -> fails()){
            return json_encode(['code' => 2, 'error' => $validator->errors()->all()]);
        }else{
            $inputs = Input::all();
            $password = Str::random();
            $inputs['password'] = Hash::make($password);
            ApiUser::create($inputs);
        }

        return json_encode(['code' => 2, 'error' => 'Hasło: '.$password]);
    }

    public function getShow($user_id)
    {
        $user = ApiUser::withTrashed()->with('apiHistories', 'apiModules')->findOrFail($user_id);

        return View::make('settings.api.users.show', compact('user'));
    }

    public function getManageModules($user_id)
    {
        $user = ApiUser::withTrashed()->findOrFail($user_id);
        $modules = ApiModule::get();

        return View::make('settings.api.users.manage-modules', compact('user', 'modules'));
    }

    public function postAppendModules($user_id)
    {
        $user = ApiUser::withTrashed()->findOrFail($user_id);
        $user->apiModules()->sync(Input::get('modules', []));

        return json_encode(['code'=>0]);
    }

    public function getEit($user_id)
    {
        $user = ApiUser::withTrashed()->findOrFail($user_id);

        return View::make('settings.api.users.edit', compact('user'));
    }

    public function postUpdate($user_id)
    {
        $user = ApiUser::withTrashed()->findOrFail($user_id);
        $user->update(Input::all());

        return json_encode(['code' => 0]);
    }

    public function getLockAccount($user_id)
    {
        $user = ApiUser::findOrFail($user_id);

        return View::make('settings.api.users.lock', compact('user'));
    }

    public function postLockAccount($user_id)
    {
        $user = ApiUser::findOrFail($user_id);
        $user->delete();

        Flash::success('Zablokowano konto');

        return json_encode(['code'=>0]);
    }

    public function getUnlockAccount($user_id)
    {
        $user = ApiUser::withTrashed()->findOrFail($user_id);
        return View::make('settings.users.unlock', compact('user'));
    }

    public function postUnlockAccount($user_id)
    {
        $user = ApiUser::withTrashed()->findOrFail($user_id);
        $user->restore();

        Flash::success('Odblokowano konto');

        return json_encode(['code'=>0]);
    }
}