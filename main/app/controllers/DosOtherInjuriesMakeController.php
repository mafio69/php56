<?php

class DosOtherInjuriesMakeController extends \BaseController {

	public function getSearch()
    {
        return View::make('dos.other_injuries.create.search');
    }

    public function postSearchSyjon()
    {
        $syjonService = new \Idea\SyjonService\SyjonService();
        $matcher = new \Idea\SyjonService\Matcher(Input::instance());

        Input::merge(['contract_internal_agreement_type_id' => '1,2,3,4']);

        $results = $syjonService->searchContracts(Input::except('_token'));
        $contracts = json_decode( $results );

        if($contracts && $contracts->total == 0)
        {
            return Response::make('empty results', 200);
        }

        if($contracts) {

            if (Input::get('contract_number') != '') {
                $contracts = $matcher->unprocessed($contracts, 2);
            }
            if (Input::get('contract_number') != ''  || Input::get('nip_company') != '') {
                $contracts = $matcher->dosInjuries($contracts);
            }
        }
        return View::make('dos.other_injuries.create.searched-contracts', compact('contracts'));
    }

    public function postLoadNextSyjon($skip)
    {
        Input::merge(['skip' => $skip]);

        $syjonService = new \Idea\SyjonService\SyjonService();
        $matcher = new \Idea\SyjonService\Matcher(Input::instance());

        Input::merge(['contract_internal_agreement_type_id' => '1,2,3,4']);
        $contracts = json_decode( $syjonService->searchContracts(Input::except('_token')) ) -> data;
        if($contracts) {

            if (Input::get('contract_number') != '') {
                $contracts = $matcher->unprocessed($contracts, 2);
            }
            if (Input::get('contract_number') != ''  || Input::get('nip_company') != '') {
                $contracts = $matcher->dosInjuries($contracts);
            }
        }

        $lp = $skip;
        return View::make('dos.other_injuries.create.searched-contracts', compact('contracts', 'lp'));
    }

    public function postSearchObjects()
    {
        if(! Input::has('contract_number') ){
            return Response::make('empty results', 200);
        }

        $searcher = new \Idea\Searcher\Searcher(null, Input::get('contract_number'));

        $objects = $searcher->searchObjects();

        if(count($objects) == 0){
            return Response::make('empty results', 200);
        }
        $matcher = new \Idea\Searcher\Matcher();

        $objects = $matcher->dosUnprocessed($objects);
        $objects = $matcher->dosInjuries($objects);

        return View::make('dos.other_injuries.create.searched-objects', compact('objects'));
    }

    public function postCreateNewEntity()
    {
        $contract_internal_agreement_id = Input::get('contract_internal_agreement_id');

        $matcher = new \Idea\SyjonService\Matcher(Input::instance());

        if(! Input::has('contract_id')) {
            $object_id = Input::get('object_id');
            $object = Objects::with('insurance_company')->find($object_id);

            $policy = null;

            $contract = null;
            $policy = null;

            $injuries = $matcher->searchDosInjuries($object->nr_contract);
            $insurance_company_id = $object->insurance_company_id;
            $source = 'baza szkód';
            $objectType = $object->assetType;
        }else {
            $contract_id = Input::get('contract_id');
            $object_id = Input::get('object_id');
            $policy_id = Input::get('policy_id');

            $syjonService = new \Idea\SyjonService\SyjonService();

            $contract = json_decode($syjonService->loadContract($contract_id))->data;
            $object = json_decode($syjonService->loadVehicle($object_id, $contract_id))->data;
            $policy = json_decode($syjonService->loadPolicy($policy_id))->data;

            $injuries = $matcher->searchDosInjuries($contract->contract_number);

            $insurance_company_id = null;
            $source = 'syjon';
            $objectType = ObjectAssetType::where('name', $object->object_type)->first();
        }

        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->get();
        $assetTypes = ObjectAssetType::orderBy('name')->get();

        return View::make('dos.other_injuries.create.create',
                compact('assetTypes',  'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives', 'contract', 'object', 'policy', 'contract_internal_agreement_id', 'injuries',  'insurance_company_id',   'source', 'objectType'));
    }

    public function postStore()
    {
        $source = Input::get('source');

        if($source == 'syjon')
        {
            $matcher = new \Idea\SyjonService\Matcher(Input::instance());
            $object = $matcher->searchObject(Input::instance());
            $object->update([
                'syjon_vehicle_id' => Input::get('object_id'),
                'syjon_contract_id' => Input::get('contract_id'),
                'syjon_contract_internal_agreement_id' => Input::get('contract_internal_agreement_id'),
                'syjon_policy_id' => Input::get('policy_id'),
            ]);
        }else{
            $object = Objects::find(Input::get('object_id'));
        }

        if( Input::has('if_map') ) $if_map = 1; else $if_map = 0;
        if( Input::has('if_map_correct') ) $if_map_correct = 1; else $if_map_correct = 0;

        if( Input::get('info') != ''){
            $insert = Text_contents::create(array(
                'content' => Input::get('info')
            ));

            $info_id = $insert->id;
        }else{
            $info_id = '0';
        }

        if( Input::get('remarks') != ''){
            $insert = Text_contents::create(array(
                'content' => Input::get('remarks')
            ));

            $remarks_id = $insert->id;
        }else{
            $remarks_id = '0';
        }

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

        $object->update(Input::except(['owner_id', 'client_id']));

        if( Input::get('contract_status') != mb_strtoupper('aktywna', 'UTF-8') )
            $locked_status = 5;
        else
            $locked_status = 0;

        $last_injury = DosOtherInjury::orderBy('id', 'desc')->limit('1')->get();
        if (!$last_injury->isEmpty()) {
            $case_nr = $last_injury->first()->case_nr;
            if( substr($case_nr, -4) == date('Y') ){
                $case_nr = intval( substr($case_nr, 1, -5) );
                $case_nr++;
                $case_nr = 'A'.$case_nr;
                $case_nr .= '/'.date('Y');
            }else{
                $case_nr = 'A1/'.date('Y');
            }
        }else{
            $case_nr = 'A1/'.date('Y');
        }


        $injury = DosOtherInjury::create(array(
            'user_id' 		=> Auth::user()->id,
            'object_id' 	=> $object->id,
            'client_id' 	=> $object->client_id,
            'notifier_surname' 	=> mb_strtoupper(Input::get('notifier_surname'), 'UTF-8'),
            'notifier_name' 	=> mb_strtoupper(Input::get('notifier_name'), 'UTF-8'),
            'notifier_phone' 	=> mb_strtoupper(Input::get('notifier_phone'), 'UTF-8'),
            'notifier_email' 	=> Input::get('notifier_email'),
            'injuries_type_id' 	=> Input::get('injuries_type'),
            'offender_id'		=>	$id_offender,
            'injury_nr'		=> mb_strtoupper(Input::get('injury_nr'), 'UTF-8'),
            'case_nr'		=> $case_nr,
            'info' 			=> $info_id,
            'remarks' 		=> $remarks_id,
            'police' 		=> mb_strtoupper(Input::get('police'), 'UTF-8'),
            'police_nr' 	=> mb_strtoupper(Input::get('police_nr'), 'UTF-8'),
            'police_unit'	=> mb_strtoupper(Input::get('police_unit'), 'UTF-8'),
            'police_contact'=> mb_strtoupper(Input::get('police_contact'), 'UTF-8'),
            'date_event' 	=> Input::get('date_event'),
            'event_city' 	=> mb_strtoupper(Input::get('event_city'), 'UTF-8'),
            'event_street' 	=> mb_strtoupper(Input::get('event_street'), 'UTF-8'),
            'if_map' 		=> $if_map,
            'if_map_correct' 	=> $if_map_correct,
            'lat' 			=> Input::get('lat'),
            'lng' 			=> Input::get('lng'),
            'receive_id' 	=> Input::get('receives'),
            'invoicereceives_id' 	=> Input::get('invoicereceives'),
            'type_incident_id'	=> Input::get('zdarzenie'),
            'locked_status'	=> $locked_status,
            'way_of'        => (Input::get('insert_role') == 'adm') ? 1 : 2
        ));

        Histories::dos_history($injury->id, 1, Auth::user()->id);

        if($injury->notifier_phone != ''){
            $phone_nb = trim($injury->notifier_phone);
            $phone_nb = str_replace(' ', '', $phone_nb);

            $msg = "Państwa zgłoszenie szkody do obiektu o nr umowy leasingowej".$object->nr_contract." zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy ".$injury->case_nr;

            send_sms($phone_nb, $msg);

            Histories::dos_history($injury->id, 137, Auth::user()->id, $phone_nb);
        }

        if($injury){
            return Redirect::route('dos.other.injuries.new');
        }else{
            return Redirect::back()->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
        }
    }
}