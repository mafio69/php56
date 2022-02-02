<?php

class UserGroupsController extends \BaseController {

    /**
     * UserGroupsController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:grupy#dodawanie_grupy#wejscie', ['only' => ['getCreate', 'postStore']]);
        $this->beforeFilter('permitted:grupy#edycja_grupy#uprawnienia_dla_grupy', ['only' => ['getPermissions', 'getManagePermissions', 'postUpdatePermissions', 'postSearchParameter', 'postLoadPermissions']]);
        $this->beforeFilter('permitted:grupy#edycja_grupy#uzytkownicy_dla_grupy', ['only' => ['getShow', 'getManage', 'postLoadUsers', 'postUpdateGroupUsers']]);
        $this->beforeFilter('permitted:grupy#edycja_grupy#wejscie', ['only' => ['getEdit', 'postUpdate']]);
        $this->beforeFilter('permitted:grupy#lista_grup#usuwanie_grupy', ['only' => ['getDelete', 'postDelete']]);
        $this->beforeFilter('permitted:grupy#lista_grup#wejscie', ['only' => ['getIndex']]);
    }

    public function getIndex()
    {
        $groups = UserGroup::orderBy('name')->with('users',  'permissions')->get();

        return View::make('settings.users.groups.index', compact('groups'));
    }

    public function getCreate()
    {
        return View::make('settings.users.groups.create');
    }

    public function postStore()
    {
        $group = UserGroup::create(Input::all());

        Flash::success('Grupa '.$group->name.' została utworzona.');
        return json_encode(['code' => 0]);
    }

    public function getShow($id)
    {
        $group = UserGroup::find($id);
        return View::make('settings.users.groups.show', compact('group'));
    }

    public function getAppend($id)
    {
        $group = UserGroup::find($id);
        $users = User::where(function($query) use($id){
            $query->where('user_group_id', '!=', $id);
            $query->orWhereNull('user_group_id');
        })->with('group')->get();
        return View::make('settings.users.groups.append', compact('group', 'users'));
    }

    public function postAppend( $group_id)
    {
        $user = User::find(Input::get('user_id'));
        $user->update(['user_group_id' => $group_id]);

        return json_encode(['code' => 0]);
    }

    public function getManage($group_id)
    {
        $group = UserGroup::with('users')->find($group_id);

        $users = User::orderBy('name')->get();

        return View::make('settings.users.groups.manage', compact('group', 'users'));
    }

    public function postLoadUsers()
    {
        $users = User::where(function ($query) {
            if(Input::get('login') != '')
            {
                $query->where('login', 'like', Input::get('login').'%');
            }
            if(Input::get('email') != '')
            {
                $query->where('email', 'like', Input::get('email').'%');
            }
            if(Input::get('name') != '')
            {
                $query->where('name', 'like', Input::get('name').'%');
            }
        })->orderBy('name')->get();

        $group = UserGroup::with('users')->find(Input::get('group_id'));
        return View::make('settings.users.groups.users-table', compact('users', 'group'));
    }

    public function postUpdateGroupUsers($group_id)
    {
        $group = UserGroup::find($group_id);
        $group->users()->sync(Input::get('users', []));

        return Redirect::to(url('settings/user/groups/show', [$group_id]));
    }

    public function getEdit($group_id)
    {
        $group = UserGroup::find($group_id);

        return View::make('settings.users.groups.edit', compact('group', 'threadTypes'));
    }

    public function postUpdate($group_id)
    {
        $group = UserGroup::find($group_id);

        $group->update(Input::all());

        return json_encode(['code' => 0]);
    }

    public function getPermissions($id)
    {
        $group = UserGroup::with('permissionHistories.permission.module', 'permissionHistories.triggererUser', 'permissions.module')->find($id);

        $modules = Module::get();


        return View::make('settings.users.groups.permissions', compact('group', 'modules'));
    }

    public function getManagePermissions($id)
    {
        $group = UserGroup::with('permissions.module')->find($id);

        $modules = [0=>'--- wybierz ---'] +   Module::orderBy('name')->lists('name', 'id');

        return View::make('settings.users.groups.manage-permissions', compact('group', 'modules'));
    }

    public function postUpdatePermissions($id)
    {
        $group = UserGroup::find($id);

        $group_permissions = $group->permissions->lists('id');
        $permissions = Input::get('permissions', []);

        $new_permissions = array_diff($permissions, $group_permissions);
        $del_permissions = array_diff($group_permissions, $permissions);


        foreach($del_permissions as $del_permission)
        {
            PermissionHistory::create([
                'triggerer_user_id' => Auth::user()->id,
                'permission_id'   =>  $del_permission,
                'user_group_id' => $id,
                'mode' => 'detach'
            ]);

        }

        foreach($new_permissions as  $new_permission)
        {
            if($new_permission > 0) {
                PermissionHistory::create([
                    'triggerer_user_id' => Auth::user()->id,
                    'permission_id' => $new_permission,
                    'user_group_id' => $id,
                    'mode' => 'attach'
                ]);

            }
        }

        $group->permissions()->sync(Input::get('permissions', []));

        Flash::success('Zaktualizowano uprawnienia grupy');

        return Redirect::to('settings/user/groups/permissions/'.$id);
    }

    public function postSearchParameter()
    {
        $result = [];


        switch (Input::get('col_name'))
        {
            case 'module_id':
                $paths = Permission::where(function ($query){
                    if(Input::get('module_id') > 0)  $query->where('module_id', Input::get('module_id'));
                })->orderBy('path')->lists('path', 'path')->toArray();
                $names = Permission::where(function ($query) {
                    if(Input::get('module_id') > 0)  $query->where('module_id', Input::get('module_id'));
                })->orderBy('name')->lists('name', 'name')->toArray();

                $result = [
                    'path' => $paths,
                    'name' => $names
                ];

                if(Input::get('module_id') == 0){
                    $modules = Module::orderBy('name')->lists('name', 'id')->toArray();
                    $result['module_id'] = $modules;
                }
                break;
            case 'path':
                $names = Permission::where(function ($query) {
                    if(Input::get('module_id') > 0) $query->where('module_id', Input::get('module_id'));

                    if(Input::get('path') != '0') $query->where('path', Input::get('path'));
                })->orderBy('name')->lists('name', 'name')->toArray();

                $result = [
                    'name' => $names
                ];
                break;
            default :
                break;
        }

        return json_encode($result);
    }

    public function postLoadPermissions()
    {
        $permissions = Permission::where(function($query) {
            $module_id = Input::get('module_id');
            $path = Input::get('path');
            $name = Input::get('name');

            if($module_id > 0) {
                $query->where('module_id', $module_id);
            }

            if($path != ''){
                $query->where('path', 'like', $path.'%');
            }

            if($name != ''){
                $query->where('name', 'like', $name.'%');
            }
        })->with('module')->orderBy('module_id')->orderBy('path')->orderBy('name')->get();

        $col_name = Input::get('col_name');

        $group = UserGroup::find(Input::get('group_id'));

        return View::make('settings.users.groups.permissions-table', compact('permissions', 'col_name', 'group'));
    }

    public function getDelete($group_id)
    {
        $group = UserGroup::find($group_id);

        return View::make('settings.users.groups.delete', compact('group'));
    }

    public function postDelete($group_id)
    {
        $group = UserGroup::find($group_id);
        $group->delete();

        return json_encode(['code' => 0]);
    }


}
