<?php

class InjuriesInfoliniaController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

	public function getSearch(){
		return View::make('injuries-infolinia.search');
	}

	public function getCreate()
	{
		$injuries_type = Injuries_type::all();
		$receives = Receives::all();
		$invoicereceives = Invoicereceives::all();
		$type_incident = Type_incident::orderBy('order')->get();
		$insurance_companies = Insurance_companies::where('active','=','0')->get();

        return View::make('injuries-infolinia.create', compact( 'injuries_type',  'receives', 'type_incident', 'insurance_companies', 'invoicereceives'));
	}

	public function getSearchRegistration(){
		$vehicle = Vehicles::where('registration', '=', Input::get('registration'))->where('active' ,'=', 0)->orderBy('parent_id', 'desc')->first();

		if( is_null($vehicle)){
			return '-1';
		}

		$vehicles = Vehicles::whereRegistration($vehicle->registration)->get();

		foreach($vehicles as $vehicle){
			$vehiclesA[] = $vehicle->id;
		}

		$injuries = Injury::where('active', '=', '0')->whereIn('vehicle_id', $vehiclesA)->with('vehicle', 'injuries_type', 'user')->orderBy('created_at','desc')->get();

		return View::make('injuries-infolinia.search-result', compact('injuries', 'users', 'counts'));
	}

	public function getSearchContract(){
		$vehicle = Vehicles::where('nr_contract', '=', Input::get('contract'))->where('active' ,'=', 0)->orderBy('parent_id', 'desc')->first();

		if( is_null($vehicle)){
			return '-1';
		}

		$vehicles = Vehicles::whereRegistration($vehicle->registration)->get();

		foreach($vehicles as $vehicle){
			$vehiclesA[] = $vehicle->id;
		}

		$injuries = Injury::where('active', '=', '0')->whereIn('vehicle_id', $vehiclesA)->with('vehicle', 'injuries_type', 'user')->orderBy('created_at','desc')->get();

		return View::make('injuries-infolinia.search-result', compact('injuries', 'users', 'counts'));
	}

	public function getSearchInjury_nr(){

		$injuries = Injury::where('injury_nr', '=', Input::get('injury_nr'))->where('active', '=', '0')->with('vehicle', 'injuries_type', 'user')->orderBy('created_at','desc')->get();

		if( is_null($injuries)){
			return '-1';
		}

		return View::make('injuries-infolinia.search-result', compact('injuries', 'users', 'counts'));
	}

	public function getInfo($id)
	{
		$url = URL::previous();
		if(	$url != NULL && $url != '' && isset($url) && isset($_SERVER['HTTP_REFERER']) ){
			$path = parse_url($url);
			$path = $path['path'];
			if($path == '/injuries/new' || $path == '/injuries/inprogress' || $path == '/injuries/total' || $path == '/injuries/theft' ||
				 $path == '/injuries/completed' || $path == '/injuries/canceled' )

				Session::put('prev', $url);
		}

		Histories::history($id, 133, Auth::user()->id);

		$injury = Injury::find($id);

		$info = Text_contents::find($injury->info);
		$remarks = Text_contents::find($injury->remarks);
		$remarks_damage = Text_contents::find($injury->remarks_damage);
		$damage = Damage_type::all();
		$ct_damage = count($damage);
		$damageSet = InjuryDamage::where('injury_id', '=', $id)->get();
		$damageInjury = array();
		$type_incident = Type_incident::all();
		$history = InjuryHistory::where('injury_id', '=', $id)->orderBy('created_at', 'desc')->with('user', 'history_type', 'injury_history_content')->get();

		$imagesBefore = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 1)->where('active', '=', '0')->with('user')->get();
		$imagesInprogress = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 2)->where('active', '=', '0')->with('user')->get();
		$imagesAfter = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 3)->where('active', '=', '0')->with('user')->get();

		$chat = InjuryChat::distinct()->select('injury_chat.*')->
					join(DB::raw('(select * from `injury_chat_messages` where active = 0 order by created_at DESC) injury_chat_messages_a'), function($join)
			        {
			            $join->on('injury_chat.id', '=', 'injury_chat_messages_a.chat_id');
			        })
					->where('injury_chat.injury_id', '=', $id)
					->whereIn('injury_chat.active', array(0,5))
					->orderBy('injury_chat.active', 'asc')
					->orderBy('injury_chat_messages_a.created_at', 'desc')
					->with('messages', 'user', 'messages.user', 'messages.user')->get();

		$documents = InjuryFiles::where(function($query)
			{
				$query->where('type', '=', 2)->orWhere('type', '=', 3);
			})->where('category', '!=', 0)->where('injury_id', '=', $id)->where('active', '=', '0')->with('user')->orderBy('created_at', 'desc')->get();


		$genDocuments = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 3)->where('active', '=', '0')->with('user')->orderBy('created_at', 'desc')->get();
		foreach ($genDocuments as $k => $v) {
			$genDocumentsA[$v->category][] = $v;
		}

		foreach ($damageSet as $key => $value) {
			$damageInjury[$value->damage_id][$value->param] = 1;
		}



        return View::make('injuries-infolinia.info', compact('injury', 'info', 'remarks', 'damage', 'ct_damage', 'damageSet', 'damageInjury', 'imagesBefore',
        					'imagesInprogress', 'imagesAfter', 'type_incident', 'history', 'documents', 'genDocumentsA', 'remarks_damage', 'chat'));
	}

	public function getVehicleContractList()
	{

		$term = Input::get('term');

        $vehicles = Vehicles::select('id', 'registration', 'brand', 'model', 'VIN', 'nr_contract', 'engine', 'expire', 'client_id', 'year_production',
        	'first_registration', 'mileage', 'owner_id', 'end_leasing', 'insurance_company_id', 'contribution', 'assistance', 'assistance_name', 'insurance',
        	'nr_policy')
        	->where('nr_contract', 'like', '%'.$term.'%')->groupBy('registration')->orderBy('parent_id', 'desc')->get();



        $result = array();
        foreach($vehicles as $k => $v){


        	$result[] = array(
        		"id"=>$v->id,
        		"label"=>$v->nr_contract.' - '.$v->registration.' - '.$v->brand.' - '.$v->model,
        		"value" => $v->nr_contract
        	);
        }

        return json_encode($result);

	}

	public function getVehicleInjury_nrList()
	{

		$term = Input::get('term');

        $injuries = Injury::where('injury_nr', 'like', '%'.$term.'%')->where('active', '=', '0')->with('vehicle')->orderBy('created_at', 'desc')->get();



        $result = array();
        foreach($injuries as $k => $v){


        	$result[] = array(
        		"id"=>$v->id,
        		"label"=>$v->injury_nr.' - '.$v->vehicle->registration.' - '.$v->vehicle->brand.' - '.$v->vehicle->model,
        		"value" => $v->injury_nr
        	);
        }

        return json_encode($result);

	}
}
