<?php

class SettingsCustomReportsController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:przypisywanie_raportow#wejscie');
    }

    public function index()
    {
        $reports = Custom_report_type::all();

        return View::make('settings.custom_reports.index', compact('reports'));
    }

    public function edit($id){
        $report = Custom_report_type::with('users')->find($id);
        $reportUsers = array();
        foreach($report->users as $user)
        {
            $reportUsers[$user->id] = 1;
        }
        $users = User::whereActive(0)->get();
        return View::make('settings.custom_reports.edit', compact('report', 'users', 'reportUsers'));
    }

    public function update($id){
        \Debugbar::disable();

        $report = Custom_report_type::find($id);


        if(Input::has('users'))
        {
            $report->users()->sync(Input::get('users'));
        }else{
            $report->users()->detach();
        }

        $result['code'] = 0;
        return json_encode($result);
    }

}
