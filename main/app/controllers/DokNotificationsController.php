<?php

class DokNotificationsController extends BaseController {

	private $counts;

	public function __construct(){
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));

		$res = DokNotifications::where('active', '=', 0)
		    ->where(function($query)
		        {
					if(Session::get('search.user_id', '0') != 0)
						$query ->where('user_id', '=', Session::get('search.user_id') );

					if(Session::get('search.processes_user_id', '0') != 0)
						$query ->whereHas('process', function($q)
						{
							$q->whereHas('users', function($q2)
							{
								$q2->where('user_id', '=', Session::get('search.processes_user_id') );
							});
						});

		        })
		    ->groupBy('step')->get(array('step', DB::raw('count(*) as cnt')));



	    $array = array();
	    foreach ($res as $k => $row) {
	    	$array[$row->step] = $row->cnt;
	    }
	    $this->counts = $array;
	}


	public function indexNew($id = 0)
	{
		$users = User::where('active','=',0)->get();

		$notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','0')
			->where(function($query)
	        {
	        	if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

				if(Session::get('search.processes_user_id', '0') != 0)
					$query ->whereHas('process', function($q)
					{
						$q->whereHas('users', function($q2)
						{
							$q2->where('user_id', '=', Session::get('search.processes_user_id') );
						});
					});

	        })
			->with('process' ,'vehicle', 'user')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

		$counts = $this->counts;

        $step = 0;

        return View::make('dok.notifications.new', compact('notifications',  'counts',  'step', 'users'));
	}

	public function indexInprogress($id = 0)
	{
		$users = User::where('active','=',0)->get();

        $notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','5')
        	->where(function($query)
	        {
	        	if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

				if(Session::get('search.processes_user_id', '0') != 0)
					$query ->whereHas('process', function($q)
					{
						$q->whereHas('users', function($q2)
						{
							$q2->where('user_id', '=', Session::get('search.processes_user_id') );
						});
					});

	        })
        	->with('process', 'vehicle', 'user')->paginate(Session::get('search.pagin', '10'));

		$counts = $this->counts;

        $step = 0;

        return View::make('dok.notifications.inprogress', compact('notifications',  'counts',  'step', 'users'));
	}

	public function indexCompleted($id = 0)
	{
		$users = User::where('active','=',0)->get();

        $notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','10')
			->where(function($query)
	        {
	        	if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

				if(Session::get('search.processes_user_id', '0') != 0)
					$query ->whereHas('process', function($q)
					{
						$q->whereHas('users', function($q2)
						{
							$q2->where('user_id', '=', Session::get('search.processes_user_id') );
						});
					});

	        })
        	->with('process', 'vehicle', 'user')->paginate(Session::get('search.pagin', '10'));

		$counts = $this->counts;

        $step = 0;

        return View::make('dok.notifications.completed', compact('notifications',  'counts',  'step', 'users'));
	}

	public function indexCanceled($id = 0)
	{
		$users = User::where('active','=',0)->get();

        $notifications = DokNotifications::where('active', '=' , '0')->where('step', '=','-5')
        	->where(function($query)
	        {
	        	if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

				if(Session::get('search.processes_user_id', '0') != 0)
					$query ->whereHas('process', function($q)
					{
						$q->whereHas('users', function($q2)
						{
							$q2->where('user_id', '=', Session::get('search.processes_user_id') );
						});
					});

	        })
        	->with('process', 'vehicle', 'user')->paginate(Session::get('search.pagin', '10'));

		$counts = $this->counts;

        $step = 0;

        return View::make('dok.notifications.canceled', compact('notifications',  'counts',  'step', 'users'));
	}

	public function indexInfo($id)
	{
		Session::put('last_notification', $id);
		$url = URL::previous();
		if(	$url != NULL && $url != '' && isset($url) && isset($_SERVER['HTTP_REFERER']) ){
			$path = parse_url($url);
			$path = $path['path'];
			if($path == '/dok/notifications/new' || $path == '/dok/notifications/inprogress' || $path == '/dok/notifications/completed' || $path == '/dok/notifications/canceled' )

				Session::put('prev', $url);
		}

		$notification = DokNotifications::find($id);

		$procesess = array();
		$process_t = DokProcesses::find($notification->process_id);
		$procesess[] = $process_t->name;
		while($process_t = $process_t->process){

			$procesess[] = $process_t->name;

			if($process_t->parent_id == 0) break;
		}

		$procesess = array_reverse($procesess);

		$info = Text_contents::find($notification->info);

		$history = DokNotification_history::where('dok_notification_id', '=', $id)->orderBy('created_at', 'desc')->with('user', 'history_type', 'dok_history_content')->get();

		$documents = DokFiles::where(function($query)
			{
				$query->where('type', '=', 2)->orWhere('type', '=', 3);
			})->where('category', '!=', 0)->where('dok_notification_id', '=', $id)->where('active', '=', '0')->with('user')->orderBy('created_at', 'desc')->get();


		$genDocuments = DokFiles::where('dok_notification_id', '=', $id)->where('type', '=', 3)->where('active', '=', '0')->with('user')->orderBy('created_at', 'desc')->get();
		foreach ($genDocuments as $k => $v) {
			$genDocumentsA[$v->category][] = $v;
		}

		$chat = DokChat::distinct()->select('dok_chat.*')->
					join(DB::raw('(select * from `dok_chat_messages` where active = 0 order by created_at DESC) dok_chat_messages_a'), function($join)
			        {
			            $join->on('dok_chat.id', '=', 'dok_chat_messages_a.chat_id');
			        })
					->where('dok_chat.dok_notification_id', '=', $id)
					->whereIn('dok_chat.active', array(0,5))
					->orderBy('dok_chat.active', 'asc')
					->orderBy('dok_chat_messages_a.created_at', 'desc')
					->with('messages', 'user', 'messages.user', 'messages.user')->get();

		return View::make('dok.notifications.info', compact('notification', 'info', 'history', 'documents', 'genDocumentsA', 'chat', 'procesess'));

	}

	public function getCreate()
	{
		$dok_wayof  = DokWayof::whereActive(0)->get();
		$processes = DokProcesses::where('active', '=', '0')->where('parent_id', '=', 0)->get();
        return View::make('dok.notifications.create', compact('processes', 'dok_wayof'));
	}

	public function getProcesses()
	{

		$processes = DokProcesses::find(Input::get('process_id'))->processes;

		if( $processes->count() > 0 ){
	        $result = '<div class="col-md-3 child_process"><div class="btn-group-vertical notifi-list" data-toggle="buttons">';


	        foreach($processes as $k => $process) {
                $result .= '
	        		<label class="btn btn-default">
						<input type="radio" class="notifi-process sr-only" count="' . Input::get('count') . '" value="' . $process->id . '"> ' . $process->name;
                if ($process->description != ''){
                    $result .= '<i class="fa fa-info-circle pull-right blue tips" title = "'.$process->description.'" ></i >';
                }
                $result.='</label>';
	        }

	        $result .= '</div></div>';

	        return $result;
    	}
    	return 0;

	}

	public function getNewGroup()
	{

		$count = Input::get('count')+1;

		$processes = DokProcesses::where('active', '=', '0')->where('parent_id', '=', 0)->get();

        $result = '

        	<div class="row">
				<div class="panel panel-primary">
					<div class="panel-heading">Zgłoszenie - '.$count.' <i class="fa fa-trash-o pull-right del-notifi"></i></div>
					<div class="panel-body">
						<div class="col-md-12">
							<label >Proces:</label>
						</div>
  						<div class="col-md-3">
				  			<div class="btn-group-vertical notifi-list" data-toggle="buttons">';
				  				foreach($processes as $k => $process){
				  					$result.='<label class="btn btn-default">';
								    	$result.='<input type="radio" class="notifi-process sr-only required" count="'.$count.'" value="'.$process->id.'" required> '.$process->name;
                                        if ($process->description != ''){
                                            $result .= '<i class="fa fa-info-circle pull-right blue tips" title = "'.$process->description.'" ></i >';
                                        }
								    $result.='</label>';
							  	}
							$result.='</div>
						</div>
						<div class="col-md-12 marg-btm" style="margin-top:20px;">
							<div class="btn-group" data-toggle="buttons">
							  <label class="btn btn-danger priority">
							    <input type="checkbox" name="priority['.$count.']" value="1"> <i class="fa fa-bolt"></i> zgłoszenie priorytetowe
							  </label>
							</div>
						</div>
						<div class="col-md-12" >
							<label >Informacja wewnętrzna:</label>
							'.Form::textarea('info['.$count.']', '', array('class' => 'form-control  bold', 'placeholder' => 'Informacja wewnętrzna')).'  
						</div>
						'.Form::hidden('process['.$count.']', '', array('id' => 'process_'.$count, 'class' => 'process_hidden required')).'
				</div>
			</div>
		';
        return $result;

	}

	public function store(){

		$input = Input::all();

		$vehicle = Vehicles::find(Input::get('vehicle_id'));

		if(Input::get('vin_grp') == 0 ){
			$new_vin = mb_strtoupper(Input::get('vin'), 'UTF-8');
		}else{
			$new_vin = $vehicle->VIN;
		}

		if(Input::get('brand_grp') == 0){
			$new_brand = mb_strtoupper(Input::get('brand'), 'UTF-8');
		}else{
			$new_brand = $vehicle->brand;
		}

		if(Input::get('model_grp') == 0){
			$new_model = mb_strtoupper(Input::get('model'), 'UTF-8');
		}else{
			$new_model = $vehicle->model;
		}

		if (Input::has('engine'))
			$new_engine = mb_strtoupper(Input::get('engine'), 'UTF-8');
		else
			$new_engine = $vehicle->engine;

		if(Input::get('year_production_grp') == 0){
			$new_year_production = mb_strtoupper(Input::get('year_production'), 'UTF-8');
		}else{
			$new_year_production = $vehicle->year_production;
		}

		if (Input::has('first_registration'))
			$new_first_registration = mb_strtoupper(Input::get('first_registration'), 'UTF-8');
		else
			$new_first_registration = $vehicle->first_registration;

		$new_mileage = mb_strtoupper(Input::get('mileage'), 'UTF-8');

		$new_owner_id = Input::get('owner_id');

		$new_client_id = Input::get('client_id');

		if(Input::get('end_leasing_grp') == 0){
			$new_end_leasing = Input::get('end_leasing');
		}else{
			$new_end_leasing = $vehicle->end_leasing;
		}

		if(Input::get('contract_status_grp') == 0){
			$new_contract_status = mb_strtoupper(Input::get('contract_status'), 'UTF-8');
		}else{
			$new_contract_status = $vehicle->contract_status;
		}

		$new_insurance_company_id = $vehicle->insurance_company_id;

		$new_insurance_name = $vehicle->insurance_company_name;

		$new_expire = $vehicle->expire;

		$new_contribution = $vehicle->contribution;

		$new_assistance = $vehicle->assistance;

		$new_assistance_name = $vehicle->assistance_name;

		$new_netto_brutto = $vehicle->netto_brutto;

		$new_insurance = $vehicle->insurance;

		$new_nr_policy = $vehicle->nr_policy;

		$vehicle_new = Vehicles::create(array(
			'owner_id' 		=> $new_owner_id,
			'client_id'		=> $new_client_id,
			'parent_id'		=> Input::get('vehicle_id'),
			'registration' 	=> mb_strtoupper(Input::get('registration'), 'UTF-8'),
			'VIN'			=> mb_strtoupper($new_vin, 'UTF-8'),
			'brand'			=> mb_strtoupper($new_brand, 'UTF-8'),
			'model'			=> mb_strtoupper($new_model, 'UTF-8'),
			'engine'		=> mb_strtoupper($new_engine, 'UTF-8'),
			'nr_contract'	=> mb_strtoupper(Input::get('nr_contract'), 'UTF-8'),
			'year_production'	=> $new_year_production,
			'first_registration'=> $new_first_registration,
			'mileage'		=> $new_mileage,
			'expire'		=> $new_expire,
			'contribution'	=> mb_strtoupper($new_contribution, 'UTF-8'),
			'assistance'	=> mb_strtoupper($new_assistance, 'UTF-8'),
			'assistance_name'	=> mb_strtoupper($new_assistance_name, 'UTF-8'),
			'insurance'		=> mb_strtoupper($new_insurance, 'UTF-8'),
			'nr_policy'		=> mb_strtoupper($new_nr_policy, 'UTF-8'),
			'contract_status'	=> mb_strtoupper($new_contract_status, 'UTF-8'),
			'insurance_company_id'	=> $new_insurance_company_id,
			'insurance_company_name'	=> mb_strtoupper($new_insurance_name, 'UTF-8'),
			'end_leasing'	=> $new_end_leasing,
			'netto_brutto'	=> $new_netto_brutto
			));

		if(
            ! str_contains(mb_strtoupper($new_contract_status, 'UTF-8'), 'AKTYWNA')
        )
			$locked_status = 5;
		else
			$locked_status = 0;

		if(Input::has('wayof_id') )
			$wayof_id = Input::get('wayof_id');
		else
			$wayof_id = 1;

		$allGenerate = array();
		foreach (Input::get('info') as $key => $value) {

			if(Input::has('process') && isset(Input::get('process')[$key]) ){

				if( Input::get('info')[$key] != ''){
					$insert = Text_contents::create(array(
						'content' => Input::get('info')[$key]
					));

					$info_id = $insert->id;
				}else{
					$info_id = '0';
				}

				$last_notification = DokNotifications::orderBy('id', 'desc')->limit('1')->get();
				if (!$last_notification->isEmpty()) {
					$case_nr = $last_notification->first()->case_nr;
					if( substr($case_nr, -2) == date('y') ){
						$case_nr = intval( substr($case_nr, 0, -3) );
						$case_nr++;
						$case_nr .= '/'.date('y');
					}else{
						$case_nr = '1/'.date('y');
					}
				}else{
					$case_nr = '1/'.date('y');
				}

				if(Input::has('priority') && isset(Input::get('priority')[$key]) )
					$priority = 1;
				else
					$priority = 0;

				$notification = DokNotifications::create(array(
						'user_id'	=> Auth::user()->id,
						'vehicle_id'=> $vehicle_new->id,
						'notifier_surname' 	=> mb_strtoupper(Input::get('notifier_surname'), 'UTF-8'),
						'notifier_name' 	=> mb_strtoupper(Input::get('notifier_name'), 'UTF-8'),
						'notifier_phone' 	=> mb_strtoupper(Input::get('notifier_phone'), 'UTF-8'),
						'notifier_email' 	=> Input::get('notifier_email'),
						'process_id'		=> Input::get('process')[$key],
						'info' 				=> $info_id,
						'case_nr'			=> $case_nr,
						'priority'			=> $priority,
						'wayof_id'			=> $wayof_id
					)
				);
				$allGenerate[$notification->id] = $notification->case_nr;
				Histories::dok_history($notification->id, 1, Auth::user()->id);

			}

		}

		foreach ($allGenerate as $key => $nt) {
				$hist_content = '';
				foreach ($allGenerate as $key2 => $nt2) {
					if($key != $key2)
					$hist_content .= '<i>'.$nt2.'</i> ';
				}
				Histories::dok_history($key, 7, Auth::user()->id, $hist_content);
			}

		return Redirect::route('dok.notifications.new');

	}

	public function setCancel($id)
	{
		$notification = DokNotifications::find($id);
		$notification->step = '-5';
		$notification->date_end = date("Y-m-d H:i:s");
		$notification->touch();

		Histories::dok_history($id, 2, Auth::user()->id);

		if( $notification->save() ){
			$result['code'] = 0;
			return json_encode($result);
		}
	}

	public function setInprogress($id)
	{
		$notification = DokNotifications::find($id);
		$notification->step = '5';
		$notification->touch();

		Histories::dok_history($id, 3, Auth::user()->id);

		if( $notification->save() ){
			$result['code'] = 0;
			return json_encode($result);
		}
	}

	public function setComplete($id)
	{
		$notification = DokNotifications::find($id);
		$notification->step = '10';
		$notification->date_end = date("Y-m-d H:i:s");
		$notification->touch();

		Histories::dok_history($id, 4, Auth::user()->id);

		if( $notification->save() ){
			$result['code'] = 0;
			return json_encode($result);
		}
	}





}
