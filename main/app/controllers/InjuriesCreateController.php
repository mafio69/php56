<?php

class InjuriesCreateController extends \BaseController {


    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia_(szkody)#zarzadzaj', ['only' => ['getCreateNewEntityMobile']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#wyszukaj_pojazd', ['only' => ['getSearch', 'postSearchSyjonVehicles', 'postSearchVmanageVehicles', 'postSearchNonAsVehicles']]);
    }

    public function index()
	{
        return View::make('injuries.create.index');
	}

    public function getSearch()
    {
        return View::make('injuries.create.search');
    }

    public function postSearchSyjonVehicles()
    {
        $syjonService = new \Idea\SyjonService\SyjonService();
        $matcher = new \Idea\SyjonService\Matcher(Input::instance());

        Input::merge(['contract_internal_agreement_type_id' => 5]);

        $results = $syjonService->searchContracts(Input::except('_token'));
        $contracts = json_decode( $results );

        if($contracts && $contracts->total == 0)
        {
            return Response::make('empty results', 200);
        }
        if($contracts) {
            if (Input::get('registration') != '' || Input::get('contract_number') != '') {
                $contracts = $matcher->letters($contracts);
                $contracts = $matcher->unprocessed($contracts);
            }
            if (Input::get('registration') != '' || Input::get('contract_number') != '' || Input::get('vin') != '' || Input::get('nip_company') != '') {
                $contracts = $matcher->injuries($contracts);
            }
            $contracts = $matcher->salesPrograms($contracts);
        }
        return View::make('injuries.create.searched-contracts', compact('contracts'));
    }

    public function postSearchVmanageVehicles()
    {
        if(! Input::has('registration') && ! Input::has('contract_number') && ! Input::has('vin')){
            return Response::make('empty results', 200);
        }

        $searcher = new \Idea\Searcher\Searcher(Input::get('registration'), Input::get('contract_number'), Input::get('vin'));

        $vmanageVehicles = $searcher->searchVmanageVehicles();

        if(count($vmanageVehicles) == 0){
            return Response::make('empty results', 200);
        }
        if(isset($vmanageVehicles['status']) && $vmanageVehicles['status'] == 1){
            return Response::make($vmanageVehicles['des'], 200);
        }
        
        $matcher = new \Idea\Searcher\Matcher();
        $vmanageVehicles = $matcher->letters($vmanageVehicles);
        $vehicles = $matcher->unprocessed($vmanageVehicles);
        $vehicles = $matcher->injuries($vehicles);

        return View::make('injuries.create.searched-vmanage-vehicles', compact('vehicles'));
    }

    public function postSearchNonAsVehicles()
    {
        if(! Input::has('registration') && ! Input::has('contract_number') && ! Input::has('vin')){
            return Response::make('empty results', 200);
        }

        $searcher = new \Idea\Searcher\Searcher(Input::get('registration'), Input::get('contract_number'), Input::get('vin'));

        $vehicles = $searcher->searchNonAsVehicles();

        if(count($vehicles) == 0){
            return Response::make('empty results', 200);
        }

        $matcher = new \Idea\Searcher\Matcher();
        $vehicles = $matcher->letters($vehicles);
        $vehicles = $matcher->unprocessed($vehicles);
        $vehicles = $matcher->injuries($vehicles);

        return View::make('injuries.create.searched-vehicles', compact('vehicles'));
    }

    public function postSearchAsVehicles()
    {
        if(! Input::has('registration') && ! Input::has('contract_number') && ! Input::has('vin')){
            return Response::make('empty results', 200);
        }

        $searcher = new \Idea\Searcher\Searcher(Input::get('registration'), Input::get('contract_number'), Input::get('vin'));

        $vehicles = $searcher->searchAsVehicles();
        if(count($vehicles) == 0){
            return Response::make('empty results', 200);
        }

        $matcher = new \Idea\Searcher\Matcher();
        $vehicles = $matcher->letters($vehicles);
        $vehicles = $matcher->unprocessed($vehicles);
        $vehicles = $matcher->injuries($vehicles);

        return View::make('injuries.create.searched-vehicles', compact('vehicles'));
    }

    public function postLoadNextSyjonVehicles($skip)
    {
        Input::merge(['skip' => $skip]);

        $syjonService = new \Idea\SyjonService\SyjonService();
        $matcher = new \Idea\SyjonService\Matcher(Input::instance());

        Input::merge(['contract_internal_agreement_type_id' => 5]);
        $results = $syjonService->searchContracts(Input::except('_token'));
        $contracts = json_decode( $results );

        if($contracts && $contracts->total == 0)
        {
            return Response::make('empty results', 200);
        }
        if($contracts) {
            if (Input::get('registration') != '' || Input::get('contract_number') != '') {
                $contracts = $matcher->letters($contracts);
                $contracts = $matcher->unprocessed($contracts);
            }
            if (Input::get('registration') != '' || Input::get('contract_number') != '' || Input::get('vin') != '' || Input::get('nip_company') != '') {
                $contracts = $matcher->injuries($contracts);
            }
            $contracts = $matcher->salesPrograms($contracts);
        }
        $contracts = $contracts->data;
        $lp = $skip;
        return View::make('injuries.create.searched-contract-entities', compact('contracts', 'lp'));
    }

    public function postCreateNewEntity()
    {
        $vmanage_vehicle_id = Input::get('vmanage_vehicle_id');
        $contract_internal_agreement_id = Input::get('contract_internal_agreement_id');
        $is_as_vehicle = Input::get('as', 0);

        $matcher = new \Idea\SyjonService\Matcher(Input::instance());

        $policy_insurance_company_id = null;

        if($vmanage_vehicle_id) {
            $vehicle = VmanageVehicle::with('insurance_company')->withTrashed()->find($vmanage_vehicle_id);
            $policy = null;

            $contract = null;
            $policy = null;

            $letters = $matcher->searchLetters($vehicle->registration, $vehicle->nr_contract);
            $injuries = $matcher->searchInjuries($vehicle->registration, $vehicle->nr_contract);
            $liquidation_card = $matcher->searchLiquidationCard($vehicle->registration, $vehicle->vin, $vehicle->nr_contract);
            $insurance_company_id = $vehicle->insurance_company_id;
            $policy_insurance_company_id = $vehicle->policy_insurance_company_id;
            $vehicle_type = 'VmanageVehicle';
            $source = 'baza firm';

            $program = $vehicle->salesProgram ? $vehicle->salesProgram->name_key : null;
        }elseif($is_as_vehicle == 1) {
            $vehicle_id = Input::get('vehicle_id');
            $vehicle = Vehicles::with('insurance_company')->find($vehicle_id);

            $policy = null;

            $contract = null;
            $policy = null;

            $letters = $matcher->searchLetters($vehicle->registration, $vehicle->nr_contract);
            $injuries = $matcher->searchInjuries($vehicle->registration, $vehicle->nr_contract);
            $liquidation_card = $matcher->searchLiquidationCard($vehicle->registration, $vehicle->vin, $vehicle->nr_contract);
            $insurance_company_id = $vehicle->insurance_company_id;
            $policy_insurance_company_id = $vehicle->policy_insurance_company_id;
            $vehicle_type = 'Vehicles';
            $source = 'baza szkód';

            $program = $vehicle->salesProgram ? $vehicle->salesProgram->name_key : null;
        }else {
            $contract_id = Input::get('contract_id');
            $vehicle_id = Input::get('vehicle_id');
            $policy_id = Input::get('policy_id');

            $syjonService = new \Idea\SyjonService\SyjonService();

            $contract = json_decode($syjonService->loadContract($contract_id))->data;
            $vehicle = json_decode($syjonService->loadVehicle($vehicle_id, $contract_id))->data;
            $vehicle->VIN = $vehicle->vin;
            $policy = json_decode($syjonService->loadPolicy($policy_id))->data;
            if(!is_null($vehicle->nip_dost) && !is_null($vehicle->name_dost))
            {
                $seller = VehicleSellers::where('nip', $vehicle->nip_dost)->where('name', $vehicle->name_dost)->first();
                if(is_null($seller)) {
                    $seller = VehicleSellers::create(array('nip' => $vehicle->nip_dost, 'name' => $vehicle->name_dost));
                }
                $vehicle->seller_id = $seller->id;
            } else {
                $vehicle->seller_id = null;
            }
            $letters = $matcher->searchLetters($vehicle->registration, $contract->contract_number);
            $injuries = $matcher->searchInjuries($vehicle->registration, $contract->contract_number);
            $liquidation_card = $matcher->searchLiquidationCard($vehicle->registration, $vehicle->vin, $contract->contract_number);
            $policy_insurance_company_id = $matcher->parseInsuranceCompany($policy);

            $syjonProgram = SyjonProgram::find($contract->program_id);
            $program = $syjonProgram->name_key;

            $insurance_company_id = null;
            $vehicle_type = 'Vehicles';
            $source = 'Syjon';
        }

        $injuries_type = Injuries_type::where('if_injury_vehicle', 1)->get();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = Type_incident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();

        $mobileInjury = MobileInjury::find( Input::get('mobile_injury_id') );
        $eaInjury = EaInjury::find(Input::get('ea_injury_id'));

        $damage = Damage_type::all();
        $ct_damage = count($damage);

        $contract_status = $contract ? $contract->contract_status : $vehicle->contract_status;
        $contract_status = ContractStatus::where('name', $contract_status)->first();

        $if_company_groups = false;

        if(Input::has('branch_id') || ($eaInjury && $eaInjury->workshop_id))
        {
            if(Input::has('branch_id')) {
                $branch = Branch::find(Input::get('branch_id'));
            }else{
                $branch = Branch::find($eaInjury->workshop_id);
            }

            if($program) {
                foreach ($branch->branchPlanGroups as $branchPlanGroup) {
                    if (
                        $branchPlanGroup->plan_group
                        &&
                        $branchPlanGroup->plan_group->plan->sales_program == $program
                        &&
                        count($branchPlanGroup->plan_group->company_groups) > 0
                    ) {
                        $if_company_groups = true;
                    }

                }
            }
        }else{
            $branch = null;
        }

        return View::make('injuries.create.create', compact('damage', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives', 'contract', 'vehicle', 'policy', 'contract_internal_agreement_id', 'letters', 'injuries', 'liquidation_card', 'mobileInjury', 'damage', 'ct_damage', 'insurance_company_id', 'vehicle_type', 'is_as_vehicle', 'source', 'contract_status', 'branch', 'if_company_groups', 'eaInjury', 'policy_insurance_company_id'));
    }

    public function getCreateNewEntityMobile($mobile_injury_id)
    {
        $mobileInjury = MobileInjury::find($mobile_injury_id);

        $syjonService = new \Idea\SyjonService\SyjonService();

        $parameters = $mobileInjury->generateSearchParameters();
        $parameters['contract_internal_agreement_type_id'] = 5;
        $parameters['multi'] = 1;
        $parameters['take'] = 100;

        $results = $syjonService->searchContracts($parameters);

        $contracts = json_decode( $results );
        if($contracts->total == 0)
        {
            $searcher = new \Idea\Searcher\Searcher($mobileInjury->registration, $mobileInjury->nr_contract);
            $vmanageVehicles = $searcher->searchVmanageVehicles();

            if(count($vmanageVehicles) == 1) {
                return View::make('injuries.create.redirector', [
                    'url' => url('injuries/make/create-new-entity'),
                    'data' => [
                        'vmanage_vehicle_id' => $vmanageVehicles[0]['id'],
                        'mobile_injury_id' => $mobileInjury->id
                    ]
                ]);
            }elseif(count($vmanageVehicles) > 1){
                throw new Exception();
            }

            $vehicles = $searcher->searchNonAsVehicles();

            if(count($vehicles) == 1) {
                reset($vehicles);
                $first_key = key($vehicles);

                return View::make('injuries.create.redirector', [
                    'url' => url('injuries/make/create-new-entity'),
                    'data' => [
                        'vehicle_id' => $vehicles[$first_key]['id'],
                        'mobile_injury_id' => $mobileInjury->id,
                        'as' => 1
                    ]
                ]);
            }elseif(count($vehicles) > 1){
                throw new Exception();
            }

            $vehicles = $searcher->searchAsVehicles();
            if(count($vehicles) == 1) {
                reset($vehicles);
                $first_key = key($vehicles);

                return View::make('injuries.create.redirector', [
                    'url' => url('injuries/make/create-new-entity'),
                    'data' => [
                        'vehicle_id' => $vehicles[$first_key]['id'],
                        'mobile_injury_id' => $mobileInjury->id,
                        'as'    => 1
                    ]
                ]);
            }elseif(count($vehicles) > 1){
                throw new Exception();
            }


            if(count($vmanageVehicles) == 0){
                return View::make('injuries.create.missing', compact('mobileInjury'));
            }

        }

        if($contracts->total == 1 && count($contracts->data[0]->vehicles) == 1)
        {
            $contract_id = $contracts->data[0]->id;
            $vehicle_id = $contracts->data[0]->vehicles[0]->id;
            $contract_internal_agreement_id = $contracts->data[0]->vehicles[0]->contract_internal_agreements[0]->id;
            $policy_id = (isset($contracts->data[0]->vehicles[0]->contract_internal_agreements[0]) && isset($contracts->data[0]->vehicles[0]->contract_internal_agreements[0]->policies[0]))
                            ? $contracts->data[0]->vehicles[0]->contract_internal_agreements[0]->policies[0]->policy_id
                            : null;

            return View::make('injuries.create.redirector', [
                'url' => url('injuries/make/create-new-entity'),
                'data' => [
                    'contract_id' => $contract_id,
                    'vehicle_id' => $vehicle_id,
                    'contract_internal_agreement_id' => $contract_internal_agreement_id,
                    'policy_id' => $policy_id,
                    'mobile_injury_id' => $mobileInjury->id
                ]
            ]);
        }

        foreach($contracts->data as $contract){
            foreach($contract->vehicles as $vehicle){
                if($contract->contract_number == $mobileInjury->nr_contract && $vehicle->registration == $mobileInjury->registration )
                {
                    $contract_id = $contract->id;
                    $vehicle_id = $vehicle->id;
                    $contract_internal_agreement_id = $vehicle->contract_internal_agreements[0]->id;
                    $policy_id = (isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0]))
                        ? $vehicle->contract_internal_agreements[0]->policies[0]->policy_id
                        : null;

                    return View::make('injuries.create.redirector', [
                        'url' => url('injuries/make/create-new-entity'),
                        'data' => [
                            'contract_id' => $contract_id,
                            'vehicle_id' => $vehicle_id,
                            'contract_internal_agreement_id' => $contract_internal_agreement_id,
                            'policy_id' => $policy_id,
                            'mobile_injury_id' => $mobileInjury->id
                        ]
                    ]);
                }
            }
        }
        return View::make('injuries.create.pick-syjon-vehicle', ['contracts' => $contracts->data, 'mobileInjury' => $mobileInjury]);
    }

    public function postStore()
    {
        $vehicle_type = Input::get('vehicle_type');
        $matcher = new \Idea\SyjonService\Matcher(Input::instance());
        if($vehicle_type == 'Vehicles') {
            if(Input::get('is_as_vehicle') == 1){
                $vehicle = Vehicles::find(Input::get('vehicle_id'));
            }else {
                $vehicle = $matcher->searchVehicle(Input::instance());
                $vehicle->update([
                    'syjon_vehicle_id' => Input::get('vehicle_id'),
                    'syjon_contract_id' => Input::get('contract_id'),
                    'syjon_contract_internal_agreement_id' => Input::get('contract_internal_agreement_id'),
                    'syjon_policy_id' => Input::get('policy_id'),
                    'seller_id' => Input::get('seller_id'),
                ]);
            }
        }else{
            $source_vehicle = VmanageVehicle::withTrashed()->find(Input::get('vehicle_id'));
            $vehicle = VmanageVehicle::create($source_vehicle->toArray());

            $source_vehicle->outdated = 1;
            $source_vehicle->save();

            $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $source_vehicle->id)->orWhere('previous_vmanage_vehicle_id', $source_vehicle->id)->first();

            if($existing_history)
            {
                VmanageVehicleHistory::create([
                    'history_id' => $existing_history->history_id,
                    'vmanage_vehicle_id'    =>  $vehicle->id,
                    'previous_vmanage_vehicle_id'   => $source_vehicle->id
                ]);
            }else{
                $highest_history = VmanageVehicleHistory::orderBy('history_id', 'desc')->first();
                if($highest_history)
                {
                    $history_id = $highest_history->history_id + 1;
                }else{
                    $history_id = 1;
                }

                VmanageVehicleHistory::create([
                    'history_id' => $history_id,
                    'vmanage_vehicle_id'    =>  $vehicle->id,
                    'previous_vmanage_vehicle_id'   => $source_vehicle->id
                ]);
            }
        }
        if(! Input::get('policy_insurance_company_id') || Input::get('policy_insurance_company_id') == ''){
            Input::merge(['policy_insurance_company_id' => Input::get('insurance_company_id')]);
        }
        if(! Input::has('year_production')){
            Input::merge(['year_production' => $vehicle->year_production]);
        }
        if(! Input::has('first_registration')){
            Input::merge(['first_registration' => $vehicle->first_registration]);
        }
        $vehicle->update(Input::except(['owner_id', 'client_id']));
        
        $mobile_injury = MobileInjury::find( Input::get('mobile_injury_id') );
        $ea_injury = EaInjury::find(Input::get('ea_injury_id'));

        if( Input::has('if_map') ) $if_map = 1; else $if_map = 0;
        if( Input::has('if_map_correct') ) $if_map_correct = 1; else $if_map_correct = 0;
        if( Input::has('contact_person') ) $contact_person = 2; else $contact_person = 1;

        if( Input::has('driver_id') &&  Input::get('driver_id') != ''){
            $driver_id = Input::get('driver_id');
        }else{
            if(Input::get('driver_surname') != '' || Input::get('driver_name') != '' || Input::get('driver_phone') != '') {
                $driver = Drivers::create(array(
                    'client_id' => $matcher->getClientId(),
                    'surname' => mb_strtoupper(Input::get('driver_surname'), 'UTF-8'),
                    'name' => mb_strtoupper(Input::get('driver_name'), 'UTF-8'),
                    'phone' => mb_strtoupper(Input::get('driver_phone'), 'UTF-8'),
                    'city' => mb_strtoupper(Input::get('driver_city'), 'UTF-8'),
                    'email' => Input::get('driver_email'),
                ));
                $driver_id = $driver->id;
            }else
                $driver_id = '';
        }

        if( Input::get('info') != ''){
            $insert = Text_contents::create(array(
                'content' => nl2br(Input::get('info'))
            ));

            $info_id = $insert->id;
        }else{
            $info_id = '0';
        }

        if( Input::get('remarks') != ''){
            $insert = Text_contents::create(array(
                'content' => nl2br(Input::get('remarks'))
            ));

            $remarks_id = $insert->id;
        }else{
            $remarks_id = '0';
        }

        if( Input::has('zdarzenie') && Input::get('zdarzenie') == 12 ) $if_theft = 1; else $if_theft = 0;

        if(Input::get('injuries_type') == '2' || Input::get('injuries_type') == '4' || Input::get('injuries_type') == '5'){
            $offender = Offenders::create(array(
                'surname'	=>	mb_strtoupper( Input::get('offender_surname'), 'UTF-8'),
                'name'		=>	mb_strtoupper( Input::get('offender_name'), 'UTF-8'),
                'post'		=>	mb_strtoupper( Input::get('offender_post'), 'UTF-8'),
                'city'		=>	mb_strtoupper( Input::get('offender_city'), 'UTF-8'),
                'street'	=>	mb_strtoupper( Input::get('offender_street'), 'UTF-8'),
                'registration'	=>	mb_strtoupper( Input::get('offender_registration'), 'UTF-8'),
                'car'		=>	mb_strtoupper( Input::get('offender_car'), 'UTF-8'),
                'oc_nr'		=>	mb_strtoupper( Input::get('offender_oc_nr'), 'UTF-8'),
                'zu'		=>	mb_strtoupper( Input::get('offender_zu'), 'UTF-8'),
                'expire'	=> 	mb_strtoupper( Input::get('offender_expire'), 'UTF-8'),
                'owner'		=>	mb_strtoupper( Input::get('offender_owner'), 'UTF-8'),
                'remarks'	=>	Input::get('offender_remarks')
            ));
            $id_offender = $offender->id;
        }else $id_offender = 0;


        $last_injury = Injury::orderBy('id', 'desc')->limit('1')->get();
        if( isCasActive() ) {
            if (!$last_injury->isEmpty()) {
                $case_nr = $last_injury->first()->case_nr;
                if (strpos($case_nr, 'C') !== false) {
                    $case_nr = substr($case_nr, 0, -2);
                }

                if (substr($case_nr, -4) == date('Y')) {
                    $case_nr = intval(substr($case_nr, 0, -5));
                    $case_nr++;
                    $case_nr .= '/' . date('Y').'/C';
                } else {
                    $case_nr = '1/' . date('Y').'/C';
                }
            } else {
                $case_nr = '1/' . date('Y').'/C';
            }
        }else{
            if (!$last_injury->isEmpty()) {
                $case_nr = $last_injury->first()->case_nr;
                if (substr($case_nr, -4) == date('Y')) {
                    $case_nr = intval(substr($case_nr, 0, -5));
                    $case_nr++;
                    $case_nr .= '/' . date('Y');
                } else {
                    $case_nr = '1/' . date('Y');
                }
            } else {
                $case_nr = '1/' . date('Y');
            }
        }

        if( isset($new_contract_status)
            &&
            ! str_contains(mb_strtoupper($new_contract_status, 'UTF-8'), 'AKTYWNA')
        ) {
            $locked_status = 5;
        }else {
            $locked_status = 0;
        }

        if($mobile_injury && $mobile_injury->source == 0) {
            if ($mobile_injury->source == 0) {
                $way_of = 3;
            } else {
                $way_of = 4;
            }
        }elseif($ea_injury){
            $way_of = 5;
        } else {
            if(Input::get('insert_role') == 'adm'){
                $way_of = 1;
            }else{
                $way_of = 2;
            }
        }

        if( Input::get('damage_info', '') != ''){
            $insert = Text_contents::create(array(
                'content' => Input::get('damage_info')
            ));

            $damage_info_id = $insert->id;
        }else{
            $damage_info_id = '0';
        }

        $injuryPolicy = InjuryPolicy::create([
            'insurance_company_id' => Input::get('policy_insurance_company_id'),
            'expire' => $vehicle->expire,
            'nr_policy' => $vehicle->nr_policy,
            'insurance' => $vehicle->insurance,
            'contribution' => $vehicle->contribution,
            'netto_brutto' => $vehicle->netto_brutto,
            'assistance' => $vehicle->assistance,
            'assistance_name' => $vehicle->assistance_name,
            'risks' => $vehicle->risks,
            'gap' => $vehicle->gap,
            'legal_protection' => $vehicle->legal_protection
        ]);

        $injury = Injury::create(array(
            'user_id' 		=> Auth::user()->id,
            'vehicle_id' 	=> $vehicle->id,
            'vehicle_type'  => $vehicle_type,
            'client_id' 	=> $matcher->getClientId(),
            'driver_id' 	=> $driver_id,
            'notifier_surname' 	=> mb_strtoupper(Input::get('notifier_surname'), 'UTF-8'),
            'notifier_name' 	=> mb_strtoupper(Input::get('notifier_name'), 'UTF-8'),
            'notifier_phone' 	=> mb_strtoupper(Input::get('notifier_phone'), 'UTF-8'),
            'notifier_email' 	=> Input::get('notifier_email'),
            'injuries_type_id' 	=> Input::get('injuries_type'),
            'offender_id'		=>	$id_offender,
            'info' 			=> $info_id,
            'remarks' 		=> $remarks_id,
            'remarks_damage' => $damage_info_id,
            'police' 		=> mb_strtoupper(Input::get('police'), 'UTF-8'),
            'police_nr' 	=> mb_strtoupper(Input::get('police_nr'), 'UTF-8'),
            'police_unit'	=> mb_strtoupper(Input::get('police_unit'), 'UTF-8'),
            'police_contact'=> mb_strtoupper(Input::get('police_contact'), 'UTF-8'),
            'injury_nr'		=> mb_strtoupper(Input::get('injury_nr'), 'UTF-8'),
            'case_nr'		=> $case_nr,
            'date_event' 	=> Input::get('date_event'),
            'time_event' 	=> (Input::get('time_event')!='') ? Input::get('time_event') : null,
            //'event_post' 	=> Input::get('event_post'),
            'event_city' 	=> mb_strtoupper(Input::get('event_city'), 'UTF-8'),
            'event_street' 	=> mb_strtoupper(Input::get('event_street'), 'UTF-8'),
            'if_map' 		=> $if_map,
            'if_map_correct' 	=> $if_map_correct,
            'lat' 			=> Input::get('lat'),
            'lng' 			=> Input::get('lng'),
            'receive_id' 	=> Input::get('receives'),
            'invoicereceives_id' 	=> Input::get('invoicereceives'),
            'type_incident_id'	=> Input::get('zdarzenie'),
            'contact_person'    => $contact_person,
            'if_statement'	=> Input::get('if_statement'),
            'if_registration_book'	=> Input::get('if_registration_book'),
            'if_towing'		=> Input::get('if_towing'),
            'if_courtesy_car'	=> Input::get('if_courtesy_car'),
            'if_door2door'	=> Input::get('if_door2door'),
            'if_theft'		=> $if_theft,
            'locked_status'	=> $locked_status,
            'way_of'        => $way_of,
            'settlement_cost_estimate' => (Input::has('settlement_cost_estimate')) ? 1 : 0,
            'dsp_notification' => (Input::has('dsp_notification')) ? 1 : 0,
            'vindication' => (Input::has('vindication')) ? 1 : 0,
            'if_driver_fault'   => Input::get('if_driver_fault'),
            'if_vip'            => (Input::has('if_vip') ) ? 1 : null,
            'reported_ic' => (Input::has('reported_ic')&&Input::get('reported_ic')=='1') ? 1 : 0,
            'in_service' 		=> mb_strtoupper(Input::get('in_service'), 'UTF-8'),
            'if_il_repair' 		=> mb_strtoupper(Input::get('if_il_repair'), 'UTF-8'),
            'il_repair_info' 		=> Input::get('il_repair_info'),
            'il_repair_info_description' => mb_strtoupper(Input::get('il_repair_info_description'), 'UTF-8'),
            'source'=>0,
            'is_cas_case' => (isCasActive()) ? 1 : 0,
            'cas_offer_agreement' => Input::get('cas_offer_agreement', 0),
            'insurance_company_id' => Input::get('insurance_company_id'),
            'injury_policy_id' => $injuryPolicy->id,
            'ea_case_number' => $ea_injury ? $ea_injury->case_number : null
        ));

        Histories::history($injury->id, 1, Auth::user()->id);

        if(!Input::has('dont_send_sms') || Input::get('dont_send_sms') != 1 ){
            if($contact_person == 1) {
                $driver = Drivers::find($driver_id);
                if ($driver_id != '' && $driver->phone != '') {
                    $phone_nb = trim($driver->phone);
                    $phone_nb = str_replace(' ', '', $phone_nb);

                    $msg = "Państwa zgłoszenie szkody do pojazdu " . $vehicle->registration . " zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy " . $injury->case_nr;

                    send_sms($phone_nb, $msg);

                    Histories::history($injury->id, 137, Auth::user()->id, $phone_nb);
                }
            }else{
                if (Input::get('notifier_phone') != '') {
                    $phone_nb = trim(Input::get('notifier_phone'));
                    $phone_nb = str_replace(' ', '', $phone_nb);

                    $msg = "Państwa zgłoszenie szkody do pojazdu " . $vehicle->registration . " zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy " . $injury->case_nr;

                    send_sms($phone_nb, $msg);

                    Histories::history($injury->id, 137, Auth::user()->id, $phone_nb);
                }
            }
        }

        //przypisanie pism do szkody
        if(Input::has('matchedLetters')) {
            foreach(Input::get('matchedLetters') as $letter_id) {
                $letter = InjuryLetter::find($letter_id);
                $file = InjuryFiles::create(array(
                    'injury_id' => $injury->id,

                    'type' => 2,
                    'category' => $letter->category,
                    'document_id' => $letter->category,
                    'document_type' => 'InjuryUploadedDocumentType',

                    'user_id' => Auth::id(),
                    'file' => $letter->file,
                    'name' => $letter->name
                ));

                Histories::history($injury->id, 158, Auth::id(), 'Kategoria ' . $file->document->name . ' - <a target="_blank" href="' . URL::route('routes.get', ['injuries', 'docs', 'downloadDoc',$file->id]) . '">pobierz</a>');

                if ($file->document_id == 3 || $file->document_id == 4) {
                    InjuryInvoices::create(array(
                            'initial_company_vat_check_id' => ($injury->branch && $injury->branch->company->companyVatCheck) ? $injury->branch->company->companyVatCheck->id : null,
                            'injury_id' => $file->injury_id,
                            'injury_files_id' => $file->id,
                            'invoicereceives_id' => $file->injury()->first()->invoicereceives_id,
                            'created_at' => $file->created_at,
                            'updated_at' => $file->updated_at
                        )
                    );
                }
                if ($file->document_id == 6 || $file->document_id == 37) {
                    InjuryCompensation::create(array(
                        'injury_id' => $file->injury_id,
                        'injury_files_id' => $file->id,
                        'user_id' => Auth::user()->id
                    ));
                }

                if($file->document_id  == 2)
                {
                    InjuryEstimate::create(array(
                        'injury_id' => $file->injury_id,
                        'injury_file_id'	=> $file->id,
                        'user_id' => Auth::user()->id
                    ));
                }

                $letter->injury_file_id = $file->id;
                $letter->save();
            }
        }
        $error=false;
        $set_branch=false;


        //dodanie uszkodzień
        if(Input::has('uszkodzenia')){
            foreach(Input::get('uszkodzenia') as $k => $v){
                if(Input::has('strona'.$v)){
                    foreach(Input::get('strona'.$v) as $k2 => $v2){
                        InjuryDamage::create(array(
                            'injury_id' => $injury->id,
                            'damage_id' => $v,
                            'param'		=> $v2
                        ));
                    }
                }else{
                    InjuryDamage::create(array(
                        'injury_id' => $injury->id,
                        'damage_id' => $v,
                        'param'		=> 0
                    ));
                }
            }
        }
        if(Input::has('branch_id')&&Input::get('branch_id')!=0){
                $set_branch=true;
                if( is_null($injury->prev_step) && $injury->step == 0 && $injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
                    $contract = $injury->vehicle->nr_contract;
                    $issuedate = $injury->date_event;
                    $issuenumber = $injury->case_nr;
                    $issuetype = 'B';
                    $username = substr(Auth::user()->login, 0, 10);

                    $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                    $owner_id = $injury->vehicle->owner_id;

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                    $xml = $webservice->getResponseXML();

                    if ($xml->Error->ErrorCde != 'ERR0000') {
                        if($xml->Error->ErrorCde == 'ERR0006'){
                            $ISSUENUMBER = $injury->case_nr;
                            $COMMENT = Input::get('content');
                            $USERNAME = substr(Auth::user()->login, 0, 10);
                            $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

                            $owner_id = $injury->vehicle->owner_id;

                            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');

                            $xml = $webservice->getResponseXML();
                            if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                if($xml->Error->ErrorCde ==  'ERR0014'){
                                    $data = new Idea\Structures\CHGISSUETYPEInput($ISSUENUMBER, 'B',$USERNAME);
                                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');
                                    $xml = $webservice->getResponseXML();
                                    if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                        $result['code'] = 2;
                                        $result['error'] = $xml->Error->ErrorDes->__toString();
                                        $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                        $error=true;
                                        //return json_encode($result);
                                    }
                                }else {
                                    $result['code'] = 2;
                                    $result['error'] = $xml->Error->ErrorDes->__toString();
                                    $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                    $error=true;
                                    //return json_encode($result);
                                }
                            }
                        }else{
                            $result['code'] = 2;
                            $result['error'] = $xml->Error->ErrorDes->__toString();
                            $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                            $error=true;
                            //return json_encode($result);
                        }
                    }
                }
                if(!$error){
                    $injury->branch_id = Input::get('branch_id');

                    $injury->step = 10;

                    $injury->touch();

                    Histories::history($injury->id, 31, Auth::user()->id);

                    if(!Input::has('branch_dont_send_sms') || Input::get('branch_dont_send_sms') != 1 ){
                        $branch = Branch::find($injury->branch_id);
                        if($branch->company->groups->contains(1) || $branch->company->groups->contains(5)) {
                            if ($injury->contact_person == 1) {
                                if ($injury->driver_id != '') {
                                    $driver = Drivers::find($injury->driver_id);
                                    $phone_nb = trim($driver->phone);
                                    $phone_nb = str_replace(' ', '', $phone_nb);
                                } else
                                    $phone_nb = '';
                            } else {
                                $phone_nb = trim($injury->notifier_phone);
                                $phone_nb = str_replace(' ', '', $phone_nb);
                            }

                            if ($phone_nb != '') {
                                $vehicle = $injury->vehicle;
                                $branch = Branch::find($injury->branch_id);

                                $msg = "Informujemy, że likwidację szkody w pojezdzie " . $vehicle->registration . " wykona serwis: " . $branch->short_name . ", ulica " . $branch->street . ", " . $branch->city . ", tel. " . $branch->phone . ". CAS w imieniu ".$vehicle->owner->name;

                                send_sms($phone_nb, $msg);

                                Histories::history($injury->id, 138, Auth::user()->id, $phone_nb);
                            }
                        }
                    }elseif(Input::has('branch_dont_send_sms')||Input::get('branch_dont_send_sms') == 1){
                        Log::info('zrezygnowano z wysyłki sms dla sprawy id: '.$injury->id);
                    }


                    if( !$injury->save() ) {
                        $error=true;
                    }
                    else{
                        Log::info('przypisano warsztat przy towrzeniu zlecenia');
                    }
                }
            }


        if( (Input::has('reported_ic') && Input::get('reported_ic')=='1') || ( $injury->injury_nr && $injury->injury_nr != '' ) ) {
            $injury->update(['injury_step_stage_id' => 2]);
            InjuryStepStageHistory::create([
                'injury_id' =>  $injury->id,
                'injury_step_stage_id' => 2
            ]);
        }else{
            $injury->update(['injury_step_stage_id' => 1]);
            InjuryStepStageHistory::create([
                'injury_id' =>  $injury->id,
                'injury_step_stage_id' => 1
            ]);
        }

        if($mobile_injury) {
            if (Input::has('pictures')) {
                foreach (Input::get('pictures') as $k => $v) {
                    $picture = MobileInjuryFile::find($v);

                    InjuryFiles::create(array(
                        'injury_id' => $injury->id,
                        'type' => 1,
                        'category' => 1,
                        'user_id' => Auth::user()->id,
                        'file' => $picture->file,
                    ));

                    $path = '/images/full';
                    $path_min = '/images/min';
                    $path_thumb = '/images/thumb';

                    $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile' . $path . '/' . $picture->file);
                    $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path . '/' . $picture->file);

                    $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile' . $path_min . '/' . $picture->file);
                    $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_min . '/' . $picture->file);

                    $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/mobile' . $path_thumb . '/' . $picture->file);
                    $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_thumb . '/' . $picture->file);
                }
            }

            $mobile_injury->active = '-1';
            $mobile_injury->injury_id = $injury->id;
            $mobile_injury->save();
        }

        if($ea_injury){
            $ea_injury->update([
               'injury_id' => $injury->id
            ]);

            $html = View::make('ea.info-template', compact('eaInjury'));

            $pdf = PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false);
            $pdf->save(storage_path('uploads/files/'.$ea_injury->case_number . '_' . $injury->case_nr . '.pdf'));

            InjuryFiles::create(array(
                'injury_id' => $injury->id,
                'type'		=> 2,
                'category'	=> 45,
                'document_type' =>  'InjuryUploadedDocumentType',
                'document_id'   =>  45,
                'user_id'	=> Auth::user()->id,
                'file'		=> $ea_injury->case_number . '_' . $injury->case_nr . '.pdf'
            ));

            Histories::history($injury->id, 214, Auth::user()->id);

            $ea_injury->delete();
        }

        if($set_branch){
            if(!$error){
                return Redirect::route('injuries-info',array($injury->id));
            }
            else{
                return Redirect::route('injuries-new')->withErrors('Zlecenia dodano poprawnie, ale wystąpił błąd w trakcie przypisywania warsztatu . Skontaktuj się z administratorem.');
            }
        }
        else{
            return Redirect::route('injuries-new');
        }
    }

    public function getShowSyjonOwner($contract_id)
    {
        $syjonService = new \Idea\SyjonService\SyjonService();
        $contract = json_decode( $syjonService->loadContract($contract_id) );

        return View::make('injuries.create.show-owner', ['owner' => $contract->data->owner]);
    }

    public function getShowSyjonObjectUser($contract_id)
    {
        $syjonService = new \Idea\SyjonService\SyjonService();
        $contract = json_decode( $syjonService->loadContract($contract_id) );

        return View::make('injuries.create.show-client', ['object_user' => $contract->data->object_user]);
    }

    public function getShowDlsOwner($owner_id)
    {
        $owner = Owners::find($owner_id);

        return View::make('injuries.create.show-dls-owner', compact('owner'));
    }

    public function getShowDlsObjectUser($client_id)
    {
        $client = Clients::find($client_id);

        return View::make('injuries.create.show-dls-client', compact('client'));
    }

    public function postLoadMapSuggestion()
    {
        $vehicle_type = Input::get('vehicle_type');
        $contract_id = Input::get('contract_id');
        $vehicle_id = Input::get('vehicle_id');
        $contract_internal_agreement_id = Input::get('contract_internal_agreement_id');
        $policy_id = Input::get('policy_id');

        if($vehicle_type == 'vmanage')
        {
            $vehicle = VmanageVehicle::withTrashed()->where('id', $vehicle_id)->first();
        }else{
            $syjonService = new \Idea\SyjonService\SyjonService();
            $vehicle = json_decode($syjonService->loadVehicle($vehicle_id, $contract_id))->data;
            $contract = json_decode($syjonService->loadContract($contract_id))->data;
            $syjonProgram = SyjonProgram::find($contract->program_id);
            $vehicle->salesProgram = $syjonProgram;
        }

        $typegarages = Typegarage::lists('name', 'id');
        $groups = CompanyGroup::lists('name', 'id');

        return View::make('injuries.create.map-suggestion', compact('vehicle', 'typegarages', 'groups', 'vehicle_type', 'contract_id', 'contract_internal_agreement_id', 'policy_id'));
    }

    public function getBranchSearch()
    {
        $request = Input::all();
        return View::make('injuries.create.map-search', compact('request'));
    }

    public function getCreateNewEntityEa($ea_injury_id)
    {
        $eaInjury = EaInjury::find($ea_injury_id);

        switch ($eaInjury->vehicle_type){
            case 1:
                VmanageVehicle::findOrFail($eaInjury->vehicle_id);
                return View::make('injuries.create.redirector', [
                    'url' => url('injuries/make/create-new-entity'),
                    'data' => [
                        'vmanage_vehicle_id' => $eaInjury->vehicle_id,
                        'ea_injury_id' => $ea_injury_id
                    ]
                ]);
                break;
            case 2:
                Vehicles::findOrFail($eaInjury->vehicle_id);
                return View::make('injuries.create.redirector', [
                    'url' => url('injuries/make/create-new-entity'),
                    'data' => [
                        'vehicle_id' => $eaInjury->vehicle_id,
                        'ea_injury_id' => $ea_injury_id,
                        'as' => 1
                    ]
                ]);
                break;
            case 3:
                $syjonService = new \Idea\SyjonService\SyjonService();
                $contracts = json_decode($syjonService->loadVehicleContracts($eaInjury->vehicle_id));

                $contract_id = $contracts->data[0]->contract->id;
                $contract_internal_agreement_id = $contracts->data[0]->contract_internal_agreements[0]->id;
                $policy_id = (isset($contracts->data[0]->contract_internal_agreements[0]) && isset($contracts->data[0]->contract_internal_agreements[0]->policies[0]))
                    ? $contracts->data[0]->contract_internal_agreements[0]->policies[0]->policy_id
                    : null;

                return View::make('injuries.create.redirector', [
                    'url' => url('injuries/make/create-new-entity'),
                    'data' => [
                        'contract_id' => $contract_id,
                        'vehicle_id' => $eaInjury->vehicle_id,
                        'contract_internal_agreement_id' => $contract_internal_agreement_id,
                        'policy_id' => $policy_id,
                        'ea_injury_id' => $ea_injury_id
                    ]
                ]);
                break;
            default:
                throw new Exception('missing vehicle type');
                break;
        }
    }

    public function getAttach($ea_injury_id, $injury_id)
    {
        $eaInjury = EaInjury::find($ea_injury_id);
        $injury = Injury::find($injury_id);

        $eaInjury->update(['injury_id', $injury_id]);
        $injury->update(['ea_case_number' => $eaInjury->case_number]);

        $html = View::make('ea.info-template', compact('eaInjury'));

        $pdf = PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false);
        $pdf->save(storage_path('uploads/files/'.$eaInjury->case_number . '_' . $injury->case_nr . '.pdf'));

        InjuryFiles::create(array(
            'injury_id' => $injury_id,
            'type'		=> 2,
            'category'	=> 45,
            'document_type' =>  'InjuryUploadedDocumentType',
            'document_id'   =>  45,
            'user_id'	=> Auth::user()->id,
            'file'		=> $eaInjury->case_number . '_' . $injury->case_nr . '.pdf'
        ));

        Histories::history($injury_id, 214, Auth::user()->id);

        $eaInjury->delete();
        return Redirect::to(url('injuries/info', [$injury_id]));
    }
}
