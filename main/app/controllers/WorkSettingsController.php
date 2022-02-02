<?php

class WorkSettingsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:edycja_godzin_pracy#wejscie');
    }

	public function index()
	{
        $work_hours = WorkHours::all();
		$work_holidays = WorkHolidays::all();

        return View::make('settings.hours.index', compact('work_hours', 'work_holidays'));
	}

    public function edit($id)
    {
        $work_hour = WorkHours::find($id);
        return View::make('settings.hours.edit', compact('work_hour'));
    }

    public function set($id)
    {
        $work_hour = WorkHours::find($id);

        if(Input::has('free') && Input::get('free') == 1) {
            $work_hour->free = 1;
        }else{
            $work_hour->work_from = Input::get('work_from');
            $work_hour->work_to = Input::get('work_to');
            $work_hour->free = 0;
        }

        $work_hour->touch();
        $work_hour->save();

        return 1;
    }

    public function registerHoliday()
    {
        WorkHolidays::create(array(
                'day' => Input::get('year').'-'.Input::get('month').'-'.Input::get('day')
            )
        );
        return 1;
    }

    public function unregisterHoliday()
    {
        $holiday = WorkHolidays::where('day' ,'=', Input::get('year').'-'.Input::get('month').'-'.Input::get('day') )->first();
        if($holiday->count() > 0){
            $holiday->delete();
        }
        return 1;
    }

    public function getHolidays()
    {
        $holidays = WorkHolidays::all();
        $result = array();
        foreach($holidays as $holiday)
        {
            $result[date('m', strtotime($holiday->day)).'-'.date('d', strtotime($holiday->day)).'-'.date('Y', strtotime($holiday->day))] = '1';
        }

        return json_encode($result);
    }


}
