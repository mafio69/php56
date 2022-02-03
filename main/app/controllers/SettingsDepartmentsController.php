<?php

class SettingsDepartmentsController extends \BaseController
{

    public function __construct()
    {
    }

    public function getIndex()
    {
        $departments = Department::get();

        return View::make('settings.departments.index', compact('departments'));
    }

    public function getCreate()
    {
        return View::make('settings.departments.create');
    }

    public function postStore()
    {
        Department::create(['name' => Input::get('name'), 'user_id' => Auth::user()->id]);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getEdit($id)
    {
        $department = Department::find($id);

        return View::make('settings.departments.edit', compact('department'));
    }

    public function postUpdate($id)
    {
        $inputs = Input::all();

        $department = Department::find($id);

        $department->update(['name' => $inputs['name'], 'user_id' => Auth::user()->id]);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getDelete($id)
    {
        $department = Department::find($id);

        return View::make('settings.departments.delete', compact('department'));
    }

    public function postDelete($id)
    {
        $department = Department::find($id);

        $department->delete();

        $result['code'] = 0;
        return json_encode($result);
    }

}
