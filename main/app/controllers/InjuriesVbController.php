<?php

/**
 * Class InjuriesVbController
 * Dodawanie szkody z na samochodzie z systemy VB Leasing
 */
class InjuriesVbController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia_(szkody)#wyszukaj_pojazd', ['only' => ['uploadCarFile', 'uploadDialog']]);
    }

    public function uploadCarFile()
    {
        \Debugbar::disable();

        $result = array();
        $file = Input::file('file');

        $mimes = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        if( !in_array($mimes , ['text/plain', 'application/vnd.ms-fontobject']) || $extension != 'csv'){
            $result['status'] = 'error';
            $result['msg'] = 'Niepoprawny format pliku. Obsługiwany format to .csv';
            return json_encode($result);
        }

        if($file) {
            $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/vb/';

            $randomKey  = sha1( time() . microtime() );
            $filename = $randomKey.'.'.$file->getClientOriginalExtension();

            $upload_success = Input::file('file')->move($destinationPath, $filename);

            if ($upload_success) {
                $result = $this->parseCSV($filename);
                return json_encode($result);
            } else {
                $result['status'] = 'error';
                $result['msg'] = 'Wystąpił błąd w trakcie wgrywania pliku. Skontaktuj się z administratorem.';
                return json_encode($result);
            }
        }
        return Response::json('error', 400);
    }

    public function parseCSV($filename)
    {
        $vbCarImport = new Idea\VB\VbCarImport($filename);
        if($vbCarImport->loadCSV()){
            if(!$vbCarImport->parseWorksheet())
            {
                $result['msg'] = $vbCarImport->msg;
                $result['status'] = 'error';
            }else {
                $result['redirect'] = URL::route('injuries.vb.get', ['create', substr($filename, 0, -4)]);
                $result['status'] = 'success';
            }
        }else{
            $result['msg'] = $vbCarImport->msg;
            if($vbCarImport->rows == 0)
                $result['status'] = 'error';
            else{
                $result['status'] = 'toSelect';
                $result['redirect'] = URL::route('injuries.vb.get', ['create', substr($filename,0,-4)]);
                $result['rows'] = $vbCarImport->rows;
            }

        }

        return $result;
    }

    public function uploadDialog()
    {
        return View::make('injuries.dialog.vbFileUpload');
    }

    public function create($filename, $row = 1)
    {
        $vbCarImport = new Idea\VB\VbCarImport($filename.'.csv');
        $vbCarImport->loadCSV();

        $vehicle_info = $vbCarImport->parseWorksheet($row);
        $vbCarInfo = $vbCarImport->getVehicleData();

        $injuries_type = Injuries_type::whereIf_injury_vehicle(1)->get();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = Type_incident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->get();
        if($vehicle_info)
            $vehicle = Vehicles::find($vehicle_info['vehicle_id']);
        else
            $vehicle = null;

        $owners_db = Owners::whereActive(0)->get();
        $owners = [];
        foreach ($owners_db as $owner)
        {
            $owners[$owner->id] = ($owner->old_name) ? $owner->name.' ('.$owner->old_name.')' : $owner->name;
        }
        $owners[''] = '--- wybierz właściciela---';
        $damage = Damage_type::all();

        $is_vip = VipClient::where('registration', $vehicle->registration)->first();

        return View::make('injuries.create-vb', compact('vehicle_info', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives', 'vehicle', 'vbCarInfo', 'owners','damage', 'is_vip'));
    }

    public function store(){
        $inputs = Input::all();

        if( Input::has('if_map') ) $if_map = 1; else $if_map = 0;
        if( Input::has('if_map_correct') ) $if_map_correct = 1; else $if_map_correct = 0;
        if( Input::has('contact_person') ) $contact_person = 2; else $contact_person = 1;

        if( Input::get('driver_id') != ''){
            $driver_id = Input::get('driver_id');
        }else{
            if(Input::get('driver_surname') != '' || Input::get('driver_name') != '' || Input::get('driver_phone') != '') {
                $driver = Drivers::create(array(
                    'client_id' => Input::get('client_id'),
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

        $vehicle = Vehicles::find(Input::get('vehicle_id'));

        $inputs['VIN'] = mb_strtoupper(Input::get('VIN'), 'UTF-8');
        $inputs['brand'] = mb_strtoupper(Input::get('brand'), 'UTF-8');
        $inputs['model'] = mb_strtoupper(Input::get('model'), 'UTF-8');
        $inputs['engine'] = mb_strtoupper(Input::get('engine'), 'UTF-8');
        $inputs['contract_status'] = mb_strtoupper(Input::get('contract_status'), 'UTF-8');
        $inputs['assistance_name'] = mb_strtoupper(Input::get('assistance_name'), 'UTF-8');
        $inputs['parent_id'] = $vehicle->id;
        $inputs['gap'] = $vehicle->gap;
        $inputs['legal_protection'] = $vehicle->legal_protection;
        $inputs['register_as'] = 0;
        $inputs['policy_insurance_company_id'] = Input::get('insurance_company_id');

        $vehicle_new = Vehicles::create($inputs);

        if( $vehicle_new->contract_status
            &&
            ! str_contains(mb_strtoupper($vehicle_new->contract_status, 'UTF-8'), 'AKTYWNA')
        ) {
            $locked_status = 5;
        }else {
            $locked_status = 0;
        }

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

        $injuryPolicy = InjuryPolicy::create([
            'insurance_company_id' => Input::get('policy_insurance_company_id'),
            'expire' => $vehicle_new->expire,
            'nr_policy' => $vehicle_new->nr_policy,
            'insurance' => $vehicle_new->insurance,
            'contribution' => $vehicle_new->contribution,
            'netto_brutto' => $vehicle_new->netto_brutto,
            'assistance' => $vehicle_new->assistance,
            'assistance_name' => $vehicle_new->assistance_name,
            'risks' => $vehicle_new->risks,
            'gap' => $vehicle_new->gap,
            'legal_protection' => $vehicle_new->legal_protection
        ]);

        $injury = Injury::create(array(
            'user_id' 		=> Auth::user()->id,
            'vehicle_id' 	=> $vehicle_new->id,
            'client_id' 	=> Input::get('client_id'),
            'driver_id' 	=> $driver_id,
            'notifier_surname' 	=> mb_strtoupper(Input::get('notifier_surname'), 'UTF-8'),
            'notifier_name' 	=> mb_strtoupper(Input::get('notifier_name'), 'UTF-8'),
            'notifier_phone' 	=> mb_strtoupper(Input::get('notifier_phone'), 'UTF-8'),
            'notifier_email' 	=> Input::get('notifier_email'),
            'injuries_type_id' 	=> Input::get('injuries_type'),
            'offender_id'		=>	$id_offender,
            'info' 			=> $info_id,
            'remarks' 		=> $remarks_id,
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
            'way_of'        => (Input::get('insert_role') == 'adm') ? 1 : 2,
            'settlement_cost_estimate' => (Input::has('settlement_cost_estimate')),
            'if_driver_fault'   => Input::get('if_driver_fault'),
            'if_vip'            => (Input::has('if_vip') ) ? 1 : null,
            'reported_ic' => (Input::has('reported_ic')&&Input::get('reported_ic')=='1') ? 1 : 0,
            'in_service' 		=> mb_strtoupper(Input::get('in_service'), 'UTF-8'),
            'if_il_repair' 		=> mb_strtoupper(Input::get('if_il_repair'), 'UTF-8'),
            'il_repair_info' 		=> Input::get('il_repair_info'),
            'il_repair_info_description' => mb_strtoupper(Input::get('il_repair_info_description'), 'UTF-8'),
            'source'=>0,
	        'is_cas_case' => (isCasActive()) ? 1 : 0,
            'insurance_company_id' => Input::get('insurance_company_id'),
            'injury_policy_id' => $injuryPolicy->id
        ));

        Histories::history($injury->id, 1, Auth::user()->id);

        if( !Input::has('dont_send_sms') || Input::get('dont_send_sms') != 1 ){
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

            if($phone_nb != ''){
                $phone_nb = str_replace(' ', '', $phone_nb);

                $msg = "Państwa zgłoszenie szkody do pojazdu ".$vehicle_new->registration." zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy ".$injury->case_nr;

                send_sms($phone_nb, $msg);

                Histories::history($injury->id, 137, Auth::user()->id, $phone_nb);
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
                    'document_type' =>  'InjuryUploadedDocumentType',
                    'user_id' => Auth::id(),
                    'file' => $letter->file,
                    'name' => $letter->name
                ));

                Histories::history($injury->id, 158, Auth::id(), 'Kategoria ' . Config::get('definition.fileCategory.' . $file->category) . ' - <a target="_blank" href="' . URL::route('injuries-downloadDoc', array($file->id)) . '">pobierz</a>');

                if ($file->category == 3 || $file->category == 4) {
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
                if ($file->category == 6) {
                    InjuryCompensation::create(array(
                        'injury_id' => $file->injury_id,
                        'injury_files_id' => $file->id,
                        'user_id' => Auth::user()->id
                    ));
                }

                $letter->injury_file_id = $file->id;
                $letter->save();
            }
        }
        $error=false;
        $set_branch=false;
        if($injury){
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
                if( is_null($injury->prev_step) && $injury->step == 0 && $vehicle_new->owner->wsdl != '' && $vehicle_new->register_as == 1) {
                    $contract = $vehicle_new->nr_contract;
                    $issuedate = $injury->date_event;
                    $issuenumber = $injury->case_nr;
                    $issuetype = 'B';
                    $username = substr(Auth::user()->login, 0, 10);

                    $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                    $owner_id = $vehicle_new->owner_id;

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                    $xml = $webservice->getResponseXML();

                    if ($xml->Error->ErrorCde != 'ERR0000') {
                        if($xml->Error->ErrorCde == 'ERR0006'){
                            $ISSUENUMBER = $injury->case_nr;
                            $COMMENT = Input::get('content');
                            $USERNAME = substr(Auth::user()->login, 0, 10);
                            $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

                            $owner_id = $vehicle_new->owner_id;

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

                    if( !Input::has('branch_dont_send_sms') || Input::get('branch_dont_send_sms') != 1 ){
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
                                $branch = Branch::find($injury->branch_id);

                                $msg = "Informujemy, że likwidację szkody w pojezdzie " . $vehicle_new->registration . " wykona serwis: " . $branch->short_name . ", ulica " . $branch->street . ", " . $branch->city . ", tel. " . $branch->phone . ". CAS w imieniu ".$vehicle_new->owner->name;

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
        }

        if( (Input::get('reported_ic')=='1') || ( Input::get('injury_nr') != '' ) ) {
            $injury->update(['injury_step_stage_id' => 2]);
            InjuryStepStageHistory::create([
                'injury_id' => $injury->id,
                'injury_step_stage_id' => 2
            ]);
        }else{
            $injury->update(['injury_step_stage_id' => 1]);
            InjuryStepStageHistory::create([
                'injury_id' => $injury->id,
                'injury_step_stage_id' => 1
            ]);
        }


        if(Input::get('insert_role') == 'adm'){
            if($injury){
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
            }else{
                return Redirect::route('injuries-create')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }else{
            if($injury){
                if($set_branch){
                    if(!$error){
                        return Redirect::route('injuries-info',array($injury->id));
                    }
                    else{
                        return Redirect::route('home')->withErrors('Zlecenia dodano poprawnie, ale wystąpił błąd w trakcie przypisywania warsztatu . Skontaktuj się z administratorem.');
                    }
                }
                else{
                    return Redirect::route('home');
                }
            }else{
                return Redirect::route('injuries-create-i')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }

    }
}
