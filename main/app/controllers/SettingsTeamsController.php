<?php

class SettingsTeamsController extends \BaseController {

    public function __construct()
    {
    }

    public function getIndex()
    {
        $teams = Team::get();

        return View::make('settings.teams.index', compact('teams'));
    }

    public function getCreate()
    {
        return View::make('settings.teams.create');
    }

    public function postStore()
    {
        Team::create(['name' => Input::get('name'), 'user_id' => Auth::user()->id]);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getEdit($id)
    {
        $team = Team::find($id);

        return View::make('settings.teams.edit', compact('team'));
    }

    public function postUpdate($id)
    {
        $inputs = Input::all();

        $team = Team::find($id);

        $team->update(['name' => $inputs['name'], 'user_id' => Auth::user()->id]);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getDelete($id)
    {
        $team = Team::find($id);

        return View::make('settings.teams.delete', compact('team'));
    }

    public function postDelete($id)
    {
        $team = Team::find($id);

        $team->delete();

        $result['code'] = 0;
        return json_encode($result);
    }
}
