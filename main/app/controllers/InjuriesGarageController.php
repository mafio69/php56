<?php

class InjuriesGarageController extends BaseController {

	private $counts;

	public function __construct(){
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));

	    $res = Injury::where('active', '=', 0)->groupBy('step')->get(array('step', DB::raw('count(*) as cnt')));
	    $array = array();
	    foreach ($res as $k => $row) {
	    	$array[$row->step] = $row->cnt;
	    }
	    $this->counts = $array;
	}
		
	public function getIndexInprogress()
	{

        $injuries = Injury::where('active', '=', '0')->where('step', '=', '10')->with('vehicle', 'injuries_type')->get();

        $counts = $this->counts;
     	
        return View::make('injuries-garage.inprogress', compact('injuries', 'counts'));
	}


	
}