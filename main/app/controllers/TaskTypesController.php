<?php

class TaskTypesController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:grupy_zadan#wejscie');
    }

    public function getIndex()
    {
        $types = TaskType::with('group', 'group.users', 'subgroup')->get()->groupBy('task_group_id');

        $taskGroups = TaskGroup::lists('name', 'id');
        $taskSubgroups = TaskSubgroup::lists('name', 'id');
        return View::make('tasks.types.index', compact('types', 'taskGroups', 'taskSubgroups'));
    }

    public function getUsersTypeGroup($type_group_id)
    {
        $type = TaskGroup::find($type_group_id);
        $name = $type->name;
        $users = User::whereHas('taskGroups' , function($query)use($type_group_id){
            $query->where('id', $type_group_id);
        })->get();

        return View::make('tasks.types.users-list', compact('users', 'name', 'type'));
    }

    public function getEditUsers($type_group_id)
    {
        $type = TaskGroup::with('users')->find($type_group_id);

        $users = User::orderBy('name')->get();

        return View::make('tasks.types.edit-users', compact('users', 'type'));
    }

    public function postUpdateUsers($type_group_id)
    {
        $type = TaskGroup::with('users')->find($type_group_id);

        $task_group_users = $type->users->lists('id');
        $users = Input::get('package', []);

        $new_users = array_diff($users, $task_group_users);
        $del_users = array_diff($task_group_users, $users);

        foreach($del_users as $del_user)
        {
            UserTaskGroupHistory::create([
                'triggerer_user_id' => Auth::user()->id,
                'user_id'   =>  $del_user,
                'task_group_id' => $type->id,
                'mode' => 'detach'
            ]);
        }

        foreach($new_users as $new_user)
        {
            if($new_user > 0) {
                UserTaskGroupHistory::create([
                    'triggerer_user_id' => Auth::user()->id,
                    'user_id' => $new_user,
                    'task_group_id' => $type->id,
                    'mode' => 'attach'
                ]);
            }
        }

        $type->users()->sync(Input::get('package', []));

        return Redirect::to(url('tasks/types'));
    }
}