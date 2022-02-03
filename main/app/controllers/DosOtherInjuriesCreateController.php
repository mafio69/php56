<?php

class DosOtherInjuriesCreateController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia#wprowadz_zlecenie');
    }

    public function index()
    {
        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();

        return View::make('dos.other_injuries.create', compact( 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives'));
    }

    public function indexMobile($injury_id)
    {
        $injury = MobileInjury::find($injury_id);

        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();

        $description = preg_replace('/\<br(\s*)?\/?\>/i','&#13;&#10;',$injury->desc_event);

        $description .= '&#13;&#10;&#13;&#10;Dane klienta: '.$injury->name_client.'; '.$injury->code_client.' '.$injury->city_client.', '.$injury->adres_client;
        if($injury->nip != '')
            $description .= '; NIP:'.$injury->nip;

        if($injury->company != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Warsztat: '.$injury->company;
        }
        if($injury->injuries_type > 0)
        {
            $description .= '&#13;&#10;&#13;&#10;Typ szkody: ';
            switch ($injury->injuries_type){
                case 2:
                    $description .='komunikacyjna OC';
                    break;
                case 1:
                    $description .='komunikacyjna AC';
                    break;
                case 3:
                    $description .='komunikacyjna kradzież';
                    break;
                case 4:
                    $description .='majątkowa';
                    break;
                case 5:
                    $description .='majątkowa kradzież';
                    break;
            }

        }

        if($injury->name_zu != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Ubezpieczyciel: '.$injury->name_zu;
        }

        return View::make('dos.other_injuries.create-mobile', compact( 'description', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives', 'injury'));
    }

    public function indexClear()
    {
        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->get();
        $assetTypes = ObjectAssetType::orderBy('name')->get();

        $owners = Owners::get();

        return View::make('dos.other_injuries.createClear', compact('assetTypes', 'owners', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives'));
    }

    public function indexMobileClear($injury_id)
    {
        $injury = MobileInjury::find($injury_id);

        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->get();
        $assetTypes = ObjectAssetType::orderBy('name')->get();

        $owners = Owners::get();

        $description = preg_replace('/\<br(\s*)?\/?\>/i','&#13;&#10;',$injury->desc_event);

        $description .= '&#13;&#10;&#13;&#10;Dane klienta: '.$injury->name_client.'; '.$injury->code_client.' '.$injury->city_client.', '.$injury->adres_client;
        if($injury->nip != '')
            $description .= '; NIP:'.$injury->nip;

        if($injury->company != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Warsztat: '.$injury->company;
        }
        if($injury->injuries_type > 0)
        {
            $description .= '&#13;&#10;&#13;&#10;Typ szkody: ';
            switch ($injury->injuries_type){
                case 2:
                    $description .='komunikacyjna OC';
                    break;
                case 1:
                    $description .='komunikacyjna AC';
                    break;
                case 3:
                    $description .='komunikacyjna kradzież';
                    break;
                case 4:
                    $description .='majątkowa';
                    break;
                case 5:
                    $description .='majątkowa kradzież';
                    break;
            }

        }

        if($injury->name_zu != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Ubezpieczyciel: '.$injury->name_zu;
        }

        return View::make('dos.other_injuries.create-mobile-clear', compact('description', 'injury', 'assetTypes', 'owners', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives'));
    }

    public function indexInfolinia()
    {
        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->get();

        return View::make('dos.other_injuries.createInfolinia', compact('injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives'));
    }
    public function indexInfoliniaClear(){
        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->get();
        $assetTypes = ObjectAssetType::orderBy('name')->get();

        $clients = Clients::whereActive(0)->orderBy('name', 'asc')->get();
        $owners = Owners::whereWsdl('')->get();

        return View::make('dos.other_injuries.createInfoliniaClear', compact('assetTypes', 'clients', 'owners', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives'));
    }

    public function getIsdlList(){

        $nr_contract = Input::get('nr_contract');
        $username = substr( Auth::user()->login, 0, 10);

        $data = new Idea\Structures\GETASSETDTAInput($nr_contract, $username);

        $owners = Owners::whereActive('0')->where('wsdl', '!=', '')->get();

        $result = array();

        foreach($owners as $owner) {
            if( !isset($owner_id) || (isset($owner_id) && $result[$owner_id]['status'] != 0) ) {
                $owner_id = $owner->id;

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('getAssetDta');

                $xml = $webservice->getResponseXML();

                $errorCode = $xml->Error->ErrorCde;

                if ($errorCode == 'ERR0000') {
                    $xml = $xml->getAsset;

                    $client = Clients::where('NIP', '=', trim($xml->customer->NIP->__toString()))->where('REGON', '=', trim($xml->customer->REGON->__toString()))
                        ->where('active', '=', '0')->orderBy('parent_id', 'desc')->get();
                    if (count($client) == 0) {
                        //brak właściciela w bazie
                        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

                        $registry_post = $xml->customer->address->postalCode->__toString();
                        if (strlen($registry_post) == 6) {
                            $registry_voivodeship_id = $matcher->match($registry_post);
                        } else {
                            $registry_voivodeship_id = null;
                        }

                        $correspond_post = $xml->customer->mailAddress->postalCode->__toString();
                        if (strlen($correspond_post) == 6) {
                            $correspond_voivodeship_id = $matcher->match($correspond_post);
                        } else {
                            $correspond_voivodeship_id = null;
                        }

                        $client = Clients::create(array(
                            'name' => $xml->customer->name->__toString(),
                            'NIP' => trim(str_replace('-', '', $xml->customer->NIP->__toString())),
                            'REGON' => trim($xml->customer->REGON->__toString()),
                            'registry_post' => $xml->customer->address->postalCode->__toString(),
                            'registry_city' => $xml->customer->address->city->__toString(),
                            'registry_street' => $xml->customer->address->street->__toString(),
                            'registry_voivodeship_id' => $registry_voivodeship_id,
                            'correspond_post' => $xml->customer->mailAddress->postalCode->__toString(),
                            'correspond_city' => $xml->customer->mailAddress->city->__toString(),
                            'correspond_street' => $xml->customer->mailAddress->street->__toString(),
                            'correspond_voivodeship_id' => $correspond_voivodeship_id,
                            'phone' => $xml->customer->phone->__toString(),
                            'email' => $xml->customer->email->__toString()
                        ));
                        $client_id = $client->id;

                    } else {
                        $client_id = $client->first()->id;
                    }

                    $insurance_company_name = trim($xml->assetPolicy->insCompany->__toString());

                    $lessor = $xml->contract->lessor->__toString();
                    if($lessor == 'SKA')
                    {
                        $owner_id = 2;
                    }else{
                        $owner_id = 1;
                    }

                    $object = Objects::where('factoryNbr', '=', $xml->assetData->factoryNbr->__toString())->whereNr_contract($xml->contract->number->__toString())->orderBy('parent_id', 'desc')->get();

                    if (count($object) == 0) {
                        //przedmiot nie istnieje w bazie
                        $xmlAssetType = trim(mb_strtoupper($xml->assetData->assetType->__toString(), 'UTF-8'));
                        $assetType = ObjectAssetType::where('name', '=', $xmlAssetType)->first();
                        if (!$assetType) {
                            $assetType = ObjectAssetType::create(array(
                                'name' => $xmlAssetType
                            ));
                        }



                        $object = Objects::create(array(
                            'owner_id' => $owner_id,
                            'client_id' => $client_id,
                            'factoryNbr' => mb_strtoupper($xml->assetData->factoryNbr->__toString(), 'UTF-8'),
                            'description' => mb_strtoupper($xml->assetData->description->__toString(), 'UTF-8'),
                            'assetType_id' => $assetType->id,
                            'year_production' => $xml->assetData->year->__toString(),
                            'insurance_company_name' => mb_strtoupper($insurance_company_name, 'UTF-8'),
                            'expire' => $xml->assetPolicy->expDate->__toString(),
                            'nr_contract' => trim(mb_strtoupper($xml->contract->number->__toString(), 'UTF-8')),
                            'end_leasing' => $xml->contract->endDate->__toString(),
                            'contract_status' => mb_strtoupper($xml->contract->status->__toString(), 'UTF-8')
                        ));

                        $id_object = $object->id;
                        $object = Objects::find($id_object);

                    } else {
                        $object = $object->first();
                    }


                    $result[$owner_id] = array(
                        'status' => 0,
                        "id" => $object->id,
                        'object_id' => $object->id,
                        "label" => mb_strtoupper($xml->assetData->factoryNbr->__toString(), 'UTF-8') . ' ' . mb_strtoupper($xml->assetData->description->__toString(), 'UTF-8'),
                        "value" => mb_strtoupper($xml->assetData->factoryNbr->__toString(), 'UTF-8'),
                        'nr_contract' => trim(mb_strtoupper($xml->contract->number->__toString(), 'UTF-8')),
                        'client_id' => $object->client_id,
                        'client' => $object->client()->first()->name,
                        'owner_id' => $object->owner_id,
                        'owner' => ($object->owner()->first()->old_name) ? $object->owner()->first()->name.' ('.$object->owner()->first()->old_name.')' : $object->owner()->first()->name,

                        'factoryNbr_show' => mb_strtoupper($xml->assetData->factoryNbr->__toString(), 'UTF-8'),
                        'description_show' => mb_strtoupper($xml->assetData->description->__toString(), 'UTF-8'),
                        'assetType_show' => mb_strtoupper($xml->assetData->assetType->__toString(), 'UTF-8'),
                        'year_production_show' => $xml->assetData->year->__toString(),
                        'expire_show' => $xml->assetPolicy->expDate->__toString(),
                        'end_leasing_show' => $xml->contract->endDate->__toString(),
                        'insurance_company_name_show' => mb_strtoupper($insurance_company_name, 'UTF-8'),
                        'insurance_company_id' => $object->insurance_company_id,
                        'contract_status_show' => mb_strtoupper($xml->contract->status->__toString(), 'UTF-8')
                    );

                } else if ($errorCode == 'ERR0003') {
                    //brak umowy o zadanych parametrach
                    $result[$owner_id] = array(
                        'status' => 1,
                        'des' => $xml->Error->ErrorDes->__toString()
                    );
                } else {
                    //pojawił się błąd
                    $result[$owner_id] = array(
                        'status' => 2,
                        'des' => $xml->Error->ErrorDes->__toString()
                    );
                }
            }
        }
        return json_encode($result);

    }

    public function getContractListNonIsdl(){
        $term = Input::get('term');

        $objects = Objects::where('nr_contract', 'like', '%'.$term.'%')->whereHas('owner', function($q)
        {
            $q->where('wsdl', '=', '' );
        })->groupBy('description')->orderBy('parent_id', 'desc')->get();

        $result = array();
        foreach($objects as $k => $v){
            $result[] = array(
                "id"=>$v->id,
                "label"=>$v->description,
                "value"=>trim($v->nr_contract)
            );
        }

        return json_encode($result);
    }

    public function getContractList()
    {
        $term = Input::get('term');

        $objects = Objects::where('nr_contract', 'like', '%'.$term.'%')->groupBy('description')->orderBy('parent_id', 'desc')->get();

        $result = array();
        foreach($objects as $k => $v){
            $result[] = array(
                "id"=>$v->id,
                "label"=>$v->description,
                "value"=>trim($v->nr_contract)
            );
        }

        return json_encode($result);
    }

    public function getObjectData(){
        $object = Input::get('object_id');
        $object = Objects::find($object);

        $result = array(
            'client_id' => $object->client_id,
            'owner_id' => $object->owner_id,
            'client_name' => $object->client->name,
            'factoryNbr' => $object->factoryNbr,
            'description' => $object->description,
            'assetType' => $object->assetType,
            'year_production' => $object->year_production,
            'expire' => $object->expire,
            'end_leasing' => $object->end_leasing,
            'insurance_company_id' => $object->insurance_company_id,
            'contract_status' => $object->contract_status
        );
        return json_encode($result);
    }

    public function getObjectCheckInjuries()
    {
        $objects = DB::select( DB::raw('
				SELECT T2.id
				FROM (
				    SELECT
				        @r AS _id,
				        (SELECT @r := parent_id FROM objects WHERE id = _id) AS parent_id,
				        @l := @l + 1 AS lvl
				    FROM
				        (SELECT @r := '.Input::get('object_id').', @l := 0) vars,
				        objects h
				    WHERE @r <> 0) T1
				JOIN objects T2
				ON T1._id = T2.id
				ORDER BY T1.lvl DESC
			') );


        $objectsA = array_map(
            function($oObject){
                $aConverted = get_object_vars($oObject);
                return $aConverted['id'];
            },
            $objects);


        $injuries = DosOtherInjury::whereIn('object_id', $objectsA )->where('active', '=', 0)->with('getInfo')->get();
        $result = array();
        $temp_i = $injuries->toArray();
        if(!is_null($injuries) && !empty( $temp_i )){
            $result['exists'] = 1;
            $result['dataHtml']	= '
				<table class="table table-hover">
					<thead>
						<th>data zgłoszenia</th>
						<th>osoba zgłaszająca</th>
						<th>miejsce zdarzenia</th>
						<th>nr sprawy</th>
						<th>nr szkodu (ZU)</th>
						<th>opis zdarzenia</th>
						<th>status</th>
					</thead>';
            foreach ($injuries as $k => $injury) {
                $result['dataHtml'] .= '
					<tr>
						<td>
							'.substr($injury->created_at, 0, -3).'
						</td>
						<td>
							'.$injury->notifier_surname.' '.$injury->notifier_name.'<br>
							tel:'.$injury->notifier_phone.' email:'.$injury->notifier_email.'
						</td>
						<td>
							'.$injury->event_city.' '.$injury->event_street.'
							<br>
							'.$injury->date_event.'
						</td>
						<td>';
                    $result['dataHtml'] .= ' <a type="button" class="btn btn-link" target="_blank" href="'.URL::route('injuries-info', array($injury->id)).'" >';

                $result['dataHtml'] .= $injury->case_nr;
                    $result['dataHtml'] .= '</a>';
                $result['dataHtml'] .= '
						</td>
						<td>
							'.(($injury->injury_nr == '') ? '---' : $injury->injury_nr).'
						</td>
						<td>
							'.(($injury->info != 0 && $injury->info!= null) ? $injury->getInfo->content : '---').'
						</td>
						<td>';
                switch ($injury->step) {
                    case '-10':
                        $result['dataHtml'] .="szkoda anulowana";
                        break;
                    case '-5':
                        $result['dataHtml'] .="szkoda całkowita";
                        break;
                    case '-3':
                        $result['dataHtml'] .="kradzież";
                        break;
                    case '0':
                        $result['dataHtml'] .="nowe";
                        break;
                    case '5':
                        $result['dataHtml'] .="w obsłudze";
                        break;
                    case '10':
                        $result['dataHtml'] .="w trakcie naprawy";
                        break;
                    case '15':
                        $result['dataHtml'] .= "zakończone w normalnym trybie";
                        break;
                    case '17':
                        $result['dataHtml'] .= "zakończone bez likwidacji";
                        break;
                    case '19':
                        $result['dataHtml'] .= "zakończone bez naprawy";
                        break;
                    case '20':
                        $result['dataHtml'] .= "odmowa zakładu ubezpieczeń";
                        break;
                }
                $result['dataHtml'] .= '
						</td>
					</tr>';
            }
            $result['dataHtml']	.= '</table>';
            $result['count'] = $injuries->count();
        }else{
            $result['exists'] = 0;
        }

        return json_encode($result);
    }

    public function post()
    {
        $data = Input::all();
        $rules = array(
            'client_id'  => 'required'
        );
        $validation = Validator::make($data, $rules);

        if ($validation->fails())
        {
            Flash::error('Proszę wskazać istniejącego leasingobiorcę.');
            return Redirect::back()->withInput();
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

        if(Input::has('assetType_id')){
            $assetType = ObjectAssetType::find(Input::get('assetType_id'));
        }else {
            $assetType = ObjectAssetType::where('name', '=', Input::get('assetType'))->first();
            if (!$assetType) {
                if (trim(Input::get('assetType')) != '') {
                    $assetType = ObjectAssetType::create(array(
                        'name' => Input::get('assetType')
                    ));
                    $assetType = $assetType->id;
                }

            }
        }

        $object_new = Objects::create(array(
            'owner_id' 		=> Input::get('owner_id'),
            'client_id'		=> Input::get('client_id'),
            'parent_id'		=> Input::get('object_id'),
            'nr_contract' => Input::get('nr_contract'),
            'factoryNbr' => Input::get('factoryNbr'),
            'description' => Input::get('description'),
            'assetType_id' => $assetType->id,
            'year_production' => Input::get('year_production'),
            'expire' => Input::get('expire'),
            'end_leasing' => Input::get('end_leasing'),
            'insurance_company_name' => Input::get('insurance_company_name'),
            'insurance_company_id' => Input::get('insurance_company_id'),
            'contract_status' => Input::get('contract_status')
        ));

        if(
            ! str_contains(mb_strtoupper(Input::get('contract_status'), 'UTF-8'), 'AKTYWNA')
        )
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

        $mobile_injury = MobileInjury::find(Input::get('injury_id'));

        $injury = DosOtherInjury::create(array(
            'user_id' 		=> Auth::user()->id,
            'object_id' 	=> $object_new->id,
            'client_id' 	=> Input::get('client_id'),
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
            'way_of'        => $mobile_injury ? (($mobile_injury->source == 0)? 3 : 4) : 0,
        ));

        Histories::dos_history($injury->id, 1, Auth::user()->id);

        if($injury->notifier_phone != ''){
            $phone_nb = trim($injury->notifier_phone);
            $phone_nb = str_replace(' ', '', $phone_nb);

            $msg = "Państwa zgłoszenie szkody do obiektu o nr umowy leasingowej".$object_new->nr_contract." zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy ".$injury->case_nr;

            send_sms($phone_nb, $msg);

            Histories::dos_history($injury->id, 137, Auth::user()->id, $phone_nb);
        }

        if($mobile_injury) {
            $mobile_injury->active = '-1';
            $mobile_injury->injury_id = $injury->id;
            $mobile_injury->save();
        }

        if(Input::get('insert_role') == 'adm'){
            if($injury){
                return Redirect::route('dos.other.injuries.new');
            }else{
                return Redirect::route('dos.other.injuries.create')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }else{
            if($injury){
                return Redirect::route('home');
            }else{
                return Redirect::route('injuries-create-i')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }
    }

    public function createClient(){
        return View::make('dos.other_injuries.dialog.create-client');
    }
    public function checkClientNIP(){
        $nip = Input::get('NIP');
        $nip = str_replace('-','', $nip);
        $client = Clients::where('NIP', '=', $nip)->get();
        if($client->isEmpty())
            return '0';

        return '1';
    }

    public function storeClient(){
        $input = Input::all();

        $input['NIP'] = trim(str_replace('-', '', $input['NIP']));
        $client = Clients::create($input);

        return $client->id;

    }

    public function listClients(){
        $clients = Clients::where('active', '=', '0')->orderBy('name', 'asc')->get();

        $last_id = $clients->max('id');

        $result = '<option value="">---wybierz---</option>';

        foreach ($clients as $key => $client) {
            if($client->id == $last_id) $select = "selected";
            else $select = "";
            $result .= '<option value="'.$client->id.'" '.$select.'>'.$client->name.'</option>';
        }
        return $result;
    }

    public function checkIfObjectExist()
    {
        $term = Input::get('term');

        $object = Objects::where('nr_contract', '=', $term)->orderBy('parent_id', 'desc')->first();


        if(!is_null($object))
            return $object->id;

        return '0';
    }

    public function dialogCreateCategory()
    {
        return View::make('dos.other_injuries.dialog.createCategory');
    }

    public function dialogPostCategory()
    {
        $input = Input::all();

        $validator = Validator::make($input ,
            array(
                'name' => 'required|Unique:object_assetType'
            )
        );

        if($validator -> fails()){
            $result['status'] = 'error';
            $result['msg'] = 'Wystąpił błąd w trakcie dodawania kategorii. Podana kategoria istnieje już w systemie.';
        }else{
            $newAssetType = ObjectAssetType::create(array(
                'name' => Input::get('name')
            ));

            $assetTypes = ObjectAssetType::orderBy('name')->get();

            $html = '<option value="">---wybierz kategorię---</option>';

            foreach ($assetTypes as $key => $assetType) {
                if($assetType->id == $newAssetType->id) $select = "selected";
                else $select = "";
                $html .= '<option value="'.$assetType->id.'" '.$select.'>'.$assetType->name.'</option>';
            }

            $result['status'] = 'success';
            $result['html'] = $html;
        }
        return json_encode($result);
    }

    public function getSearchClient()
    {
        $term = Input::get('term');
        $result = array();

        if($term != '') {
            $clients = DB::select(DB::raw('
                SELECT * FROM clients c WHERE
                (
                    c.name LIKE "%' . $term . '%" AND
                    (
                        (SELECT count(*) from clients c_2 WHERE c_2.parent_id = c.id) = 0
                        OR
                        (SELECT count(*) from clients c_3 WHERE c_3.parent_id = c.id AND name NOT LIKE "%' . $term . '%") >= 1
                    )
                )
                OR
                (
                    c.NIP LIKE "%' . $term . '%" AND
                    (
                        (SELECT count(*) from clients c_2 WHERE c_2.parent_id = c.id) = 0
                        OR
                        (SELECT count(*) from clients c_3 WHERE c_3.parent_id = c.id AND NIP NOT LIKE "%' . $term . '%") >= 1
                    )
                )
                OR
                (
                    c.REGON LIKE "%' . $term . '%" AND
                    (
                        (SELECT count(*) from clients c_2 WHERE c_2.parent_id = c.id) = 0
                        OR
                        (SELECT count(*) from clients c_3 WHERE c_3.parent_id = c.id AND REGON NOT LIKE "%' . $term . '%") >= 1
                    )
                )
            '));

            foreach ($clients as $k => $client) {
                $desc = '';
                if($client->name != '')
                    $desc .= $client->name;
                if($client->NIP != '')
                    $desc .= ' - NIP: '.$client->NIP;

                if($client->REGON != '')
                    $desc .= ' - REGON: '.$client->REGON;

                $result[] = array(
                    "id" => $client->id,
                    "label" => $desc,
                    "value" => $desc,
                );
            }
        }

        return json_encode($result);
    }

    public function searchInInsurances()
    {
        $term = Input::get('term');

        $agreement = LeasingAgreement::where('nr_contract', $term)->with('objects', 'objects.object_assetType')->first();

        if($agreement)
        {
            $response = [
                'status' => '1',
                'client_id' =>  $agreement->client_id,
                'owner_id'  =>  $agreement->owner_id,
                'client_name'   =>  $agreement->client->name,

                'expire' => ($agreement->activeInsurance()) ? $agreement->activeInsurance()->date_to : null,
                'end_leasing' => null,
                'insurance_company_id' => ($agreement->activeInsurance()) ? $agreement->activeInsurance()->insurance_company_id : null,
                'contract_status' => null
            ];
            if($agreement->objects->count() > 1)
            {
                foreach ($agreement->objects as $object)
                {
                    $response['objects'][] = [
                        'id'            =>  $object->id,
                        'description'   =>  $object->name,
                        'factoryNbr'    =>  $object->fabric_number,
                        'assetType_id'  =>  $object->object_assetType_id,
                        'assetType'  =>     ($object->object_assetType) ? $object->object_assetType->name : '',
                        'year_production'   =>  $object->production_year
                    ];
                }
            }else{
                $object = $agreement->objects->first();
                $response['object'] = [
                    'description'   =>  $object->name,
                    'factoryNbr'    =>  $object->fabric_number,
                    'assetType_id'  =>  $object->object_assetType_id,
                    'year_production'   =>  $object->production_year
                ];
            }

            return json_encode($response);
        }

        return json_encode(['status' => 0]);
    }

    public function getSelectInsuranceObject()
    {
        $objects = Input::get('objects');

        return View::make('dos.other_injuries.dialog.select-insurance-object', compact('objects'));
    }

}
