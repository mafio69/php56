<?php

class DosOtherInjuriesCardController extends BaseController
{
    public function __construct(){
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia#zarzadzaj', ['except' => ['index', 'previewDoc']]);
    }

    public function index($id)
    {
        Session::put('last_injury', $id);
        $url = URL::previous();
        if(	$url != NULL && $url != '' && isset($url) && isset($_SERVER['HTTP_REFERER']) ){
            $path = parse_url($url);
            $path = $path['path'];
            if($path == '/dos/other/injuries/new' || $path == '/dos/other/injuries/inprogress' || $path == '/dos/other/injuries/total-finished' || $path == '/dos/other/injuries/theft' ||
                $path == '/dos/other/injuries/completed' || $path == '/dos/other/injuries/canceled' || $path == '/dos/other/injuries/search/global' || $path == '/dos/other/injuries/theft-finished')

                Session::put('prev', $url);
        }

        $injury = DosOtherInjury::with('compensations.injury_file')->find($id);

        $info = Text_contents::find($injury->info);
        $remarks = Text_contents::find($injury->remarks);
        $remarks_damage = Text_contents::find($injury->remarks_damage);

        $type_incident = Type_incident::all();
        $history = DosOtherInjuryHistory::where('injury_id', '=', $id)->orderBy('created_at', 'desc')->with('user', 'history_type', 'injury_history_content')->get();

        $imagesBefore = DosOtherInjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 1)->where('active', '=', '0')->with('user')->get();
        $imagesInprogress = DosOtherInjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 2)->where('active', '=', '0')->with('user')->get();
        $imagesAfter = DosOtherInjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 3)->where('active', '=', '0')->with('user')->get();

        $chat = DosOtherInjuryChat::distinct()->select('dos_other_injury_chat.*')->
        join(DB::raw('(select * from `dos_other_injury_chat_messages` where active = 0 order by created_at DESC) injury_chat_messages_a'), function($join)
            {
                $join->on('dos_other_injury_chat.id', '=', 'injury_chat_messages_a.chat_id');
            })
            ->where('dos_other_injury_chat.injury_id', '=', $id)
            ->whereIn('dos_other_injury_chat.active', array(0,5))
            ->orderBy('dos_other_injury_chat.active', 'asc')
            ->orderBy('injury_chat_messages_a.created_at', 'desc')
            ->with('messages', 'user', 'messages.user', 'messages.user')->get();

        $documentsTypes = DosOtherInjuryDocumentType::whereActive(0)->get();

        $documents = DosOtherInjuryFiles::where(function($query)
        {
            $query->where('type', '=', 2)->orWhere('type', '=', 3)->orWhere('type', '=', 4);
        })->where('category', '!=', 0)->where('injury_id', '=', $id)->where('active', '=', '0')->with('user', 'document_type')->orderBy('created_at', 'desc')->get();

        $invoices = DosOtherInjuryInvoices::where('injury_id', '=', $id)->where('active','=', '0')->with('injury_files', 'parent', 'invoicereceive')->get();


        $genDocuments = DosOtherInjuryFiles::where('injury_id', '=', $id)->where('type', '=', 3)->where('active', '=', '0')->with('user')->orderBy('created_at', 'desc')->get();
        $genDocumentsA = array();
        foreach ($genDocuments as $k => $v) {
            $genDocumentsA[$v->category][] = $v;
        }

        $templates = SmsTemplates::whereActive(0)->get();

        $notifier_phone = trim($injury->notifier_phone);

        $phonesSMS = array();

        if($notifier_phone != ''){
            $phonesSMS[] = array(
                'name' => 'tel. zgłaszającego: '.$notifier_phone,
                'value' => $notifier_phone
            );
        }

        $theftAcceptation = DosOtherInjuryTheftAcceptationType::active()->get();
        return View::make('dos.other_injuries.info', compact('injury', 'info', 'remarks', 'imagesBefore',
            'imagesInprogress', 'imagesAfter', 'type_incident', 'history', 'documentsTypes', 'documents', 'genDocumentsA', 'remarks_damage',
            'chat', 'invoices', 'templates', 'phonesSMS' , 'theftAcceptation'));

    }

    public function setEditInjury($id)
    {
        $injury = DosOtherInjury::find($id);

        if(Input::get('injuries_type') == '2' || Input::get('injuries_type') == '4' || Input::get('injuries_type') == '5'){
            if($injury->offender_id == 0){
                $offender = Offenders::create(array(
                    'surname'	=>	mb_strtoupper(Input::get('offender_surname'), 'UTF-8'),
                    'name'		=>	mb_strtoupper(Input::get('offender_name'), 'UTF-8'),
                    'post'		=>	mb_strtoupper(Input::get('offender_post'), 'UTF-8'),
                    'city'		=>	mb_strtoupper(Input::get('offender_city'), 'UTF-8'),
                    'street'	=>	mb_strtoupper(Input::get('offender_street'), 'UTF-8'),
                    'registration'	=>	mb_strtoupper(Input::get('offender_registration'), 'UTF-8'),
                    'car'		=>	mb_strtoupper(Input::get('offender_car'), 'UTF-8'),
                    'oc_nr'		=>	mb_strtoupper(Input::get('offender_oc_nr'), 'UTF-8'),
                    'zu'		=>	mb_strtoupper(Input::get('offender_zu'), 'UTF-8'),
                    'expire'	=> 	mb_strtoupper(Input::get('offender_expire'), 'UTF-8'),
                    'owner'		=>	mb_strtoupper(Input::get('offender_owner'), 'UTF-8'),
                    'remarks'	=>	Input::get('offender_remarks')
                ));
            }else{
                $offender = Offenders::find($injury->offender_id);

                $offender->surname	=	mb_strtoupper(Input::get('offender_surname'), 'UTF-8');
                $offender->name		=	mb_strtoupper(Input::get('offender_name'), 'UTF-8');
                $offender->post		=	mb_strtoupper(Input::get('offender_post'), 'UTF-8');
                $offender->city		=	mb_strtoupper(Input::get('offender_city'), 'UTF-8');
                $offender->street	=	mb_strtoupper(Input::get('offender_street'), 'UTF-8');
                $offender->registration	=	mb_strtoupper(Input::get('offender_registration'), 'UTF-8');
                $offender->car		=	mb_strtoupper(Input::get('offender_car'), 'UTF-8');
                $offender->oc_nr	=	mb_strtoupper(Input::get('offender_oc_nr'), 'UTF-8');
                $offender->zu		=	mb_strtoupper(Input::get('offender_zu'), 'UTF-8');
                $offender->expire	= 	mb_strtoupper(Input::get('offender_expire'), 'UTF-8');
                $offender->owner	=	mb_strtoupper(Input::get('offender_owner'), 'UTF-8');
                $offender->remarks	=	Input::get('offender_remarks');

                $offender->save();

            }
            $id_offender = $offender->id;
        }else $id_offender = 0;



        $object = Objects::find($injury->object_id);
        if($object->insurance_company_id != Input::get('insurance_company_id')){
            $object->insurance_company_id = Input::get('insurance_company_id');
            $object->save();
            Histories::dos_history($id, 127, Auth::user()->id, $object->insurance_company()->first()->name);
        }

        if($injury->injuries_type_id != Input::get('injuries_type') )
            Histories::dos_history($id, 107, Auth::user()->id, $injury->injuries_type()->first()->name);
        $injury->injuries_type_id = Input::get('injuries_type');

        $injury->offender_id = $id_offender;

        if($injury->receive_id != Input::get('receives') )
            Histories::dos_history($id, 108, Auth::user()->id);
        $injury->receive_id = Input::get('receives');

        if(Input::get('receives') == 1)
        {
            $injury->receiver_name = Input::get('receiver_name');
            $injury->receiver_address = Input::get('receiver_address');
        }else{
            $injury->receiver_name = null;
            $injury->receiver_address = null;
        }

        if($injury->invoicereceives_id != Input::get('invoicereceives') )
            Histories::dos_history($id, 124, Auth::user()->id);
        $injury->invoicereceives_id = Input::get('invoicereceives');

        if($injury->date_event != Input::get('date_event') )
            Histories::dos_history($id, 109, Auth::user()->id, Input::get('date_event'));
        $injury->date_event = Input::get('date_event');

        if($injury->injury_nr != Input::get('injury_nr') )
            Histories::dos_history($id, 2, Auth::user()->id, mb_strtoupper(Input::get('injury_nr'), 'UTF-8'));
        $injury->injury_nr = mb_strtoupper(Input::get('injury_nr'), 'UTF-8');

        if($injury->police != Input::get('police') )
            Histories::dos_history($id, 7, Auth::user()->id, Input::get('police')==1 ? 'tak' : 'nie');
        $injury->police = Input::get('police');

        $injury->police_nr = mb_strtoupper(Input::get('police_nr'), 'UTF-8');
        $injury->police_unit = mb_strtoupper(Input::get('police_unit'), 'UTF-8');
        $injury->police_contact = mb_strtoupper(Input::get('police_contact'), 'UTF-8');

        if($injury->type_incident_id != Input::get('zdarzenie') ){

            $type_incident = DosOtherInjuryTypeIncident::find(Input::get('zdarzenie'));

            Histories::dos_history($id, 150, Auth::user()->id, $type_incident->name);
        }
        $injury->type_incident_id = Input::get('zdarzenie');

        $injury->touch();
        $injury->save();


        return Response::json(['code' => 0]);
    }

    public function setEditInjuryInsurance($id)
    {
        $injury = DosOtherInjury::find($id);
        $object = $injury->object;
        $object->insurance = Input::get('insurance');
        $object->contribution = Input::get('contribution');
        $object->gap = Input::get('gap');
        $object->legal_protection = Input::get('legal_protection');
        $object->netto_brutto = Input::get('netto_brutto');
        $object->touch();
        if ($object->save() ) echo 0;
    }


    public function setEditInjuryClientContact($id)
    {

        $injury = DosOtherInjury::find($id);

        $old_client = Clients::find($injury->client_id);

        if( $old_client->correspond_post != Input::get('correspond_post') || $old_client->correspond_street != Input::get('correspond_post') ||
            $old_client->correspond_city != Input::get('correspond_city') || $old_client->phone != Input::get('phone') || $old_client->email != Input::get('email')){

            $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
            $registry_post = $old_client->registry_post;
            if(strlen($registry_post) == 6)
            {
                $voivodeship_id = $matcher->match($registry_post);
                $registry_voivodeship_id = $voivodeship_id;
            }else
                $registry_voivodeship_id = null;

            $correspond_post = Input::get('correspond_post');
            if(strlen($correspond_post) == 6)
            {
                $voivodeship_id = $matcher->match($correspond_post);
                $correspond_voivodeship_id = $voivodeship_id;
            }else
                $correspond_voivodeship_id = null;

            $client = Clients::create(array(
                'parent_id' => $old_client->id,
                'firmID' => $old_client->firmID,
                'name' 		=> $old_client->name,
                'NIP'		=> $old_client->NIP,
                'REGON'		=> $old_client->REGON,
                'registry_post'		=> $old_client->registry_post,
                'registry_city'		=> $old_client->registry_city,
                'registry_street'	=> $old_client->registry_street,
                'registry_voivodeship_id' => $registry_voivodeship_id,
                'correspond_post'	=> Input::get('correspond_post'),
                'correspond_city'	=> Input::get('correspond_city'),
                'correspond_street'	=> Input::get('correspond_street'),
                'correspond_voivodeship_id' => $correspond_voivodeship_id,
                'phone'				=> Input::get('phone'),
                'email'				=> Input::get('email')
            ));
            $injury->client_id = $client->id;

            $old_client->update(['active' => 1]);
            Histories::dos_history($id, 121, Auth::user()->id);
            if( $injury->save() ) echo 0;

        }else{
            echo 0;
        }

    }

    public function postEditInjuryInfo($id)
    {
        if( Input::get('info') != ''){
            $insert = Text_contents::create(array(
                'content' => Input::get('info')
            ));

            $info_id = $insert->id;
        }else{
            $info_id = '0';
        }
        $injury = DosOtherInjury::find($id);
        $injury->info = $info_id;
        $injury->save();

        Histories::dos_history($id, 129, Auth::user()->id);
        echo 0;
    }

    public function setEditInjuryMap($id)
    {
        $injury = DosOtherInjury::find($id);

        if( Input::get('if_map') ) $if_map = 1; else $if_map = 0;
        if( Input::get('if_map_correct') ) $if_map_correct = 1; else $if_map_correct = 0;

        $injury->if_map = $if_map;
        $injury->if_map_correct = $if_map_correct;
        $injury->lat = Input::get('lat');
        $injury->lng = Input::get('lng');

        $injury->event_city = Input::get('event_city');
        $injury->event_street = Input::get('event_street');

        $injury -> touch();
        Histories::dos_history($id, 122, Auth::user()->id, Input::get('event_city').' '.Input::get('event_street'));

        if( $injury->save() ) echo 0;
    }

    public function setInvoice($id){
        $invoice = DosOtherInjuryInvoices::find($id);
        if($invoice->injury_files()->first()->category == 4){
            $invoice->parent_id 			= Input::get('parent_id');
        }
        $invoice->invoicereceives_id 	= Input::get('invoicereceives_id');
        $invoice->invoice_nr			= Input::get('invoice_nr');
        $invoice->invoice_date			= Input::get('invoice_date');
        $invoice->payment_date			= Input::get('payment_date');
        $invoice->netto 				= Input::get('netto');
        $invoice->vat 					= Input::get('vat');
        $invoice->commission			= Input::get('commission');
        $invoice->base_netto			= Input::get('base_netto');
        $invoice->touch();
        if( $invoice->save() ) echo 0;
    }

    public function createHistory($id)
    {
        Histories::dos_history($id, 128, Auth::user()->id, '<b>'.Input::get('content').'</b>');
        echo 0;
    }

    public function sendSms($id)
    {
        $phone_nb = trim(Input::get('phone_number'));
        $phone_nb = str_replace(' ', '', $phone_nb);

        $msg = Input::get('bodySMS').' Pozdrawiam '.Auth::user()->name;

        $result = send_sms($phone_nb, $msg);

        $result = iconv( "iso-8859-2", "utf-8", $result );

        Histories::dos_history($id, 141, Auth::user()->id, '-1', $msg);

        Flash::message($result);
        return Redirect::back();
    }

    public function setAlert($name, $id, $model, $desc)
    {
        $wreck = $model::find($id);
        $input = Input::all();
        $rules = array(
            'alert' => 'date',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        {
            return Response::json(array('alert' => 'response-alert-danger', 'message' => 'Niepoprawny format daty.'));
        }
        $wreck->$name = Input::get('alert');
        $col_user = $name.'_user_id';
        $wreck->$col_user = Auth::user()->id;
        $wreck->save();

        $result = array();
        $result['alert'] = 'response-alert-info';
        $result['message'] = 'Data parametru została zmieniona.';

        Histories::dos_history($wreck->injury->id, 143, Auth::user()->id, $desc.' - '.Input::get('alert') );

        return json_encode($result);
    }

    public function setEditInjuryOffender($id)
    {
        $injury = DosOtherInjury::find($id);

        $post_data = Input::all();
        $offender = Offenders::find($injury->offender_id);
        $offender->fill($post_data);
        if( $offender->save() ){
            Histories::dos_history($id, 135, Auth::user()->id);
            echo 0;
        }
    }

    public function previewDoc($id)
    {
        $file = DosOtherInjuryFiles::find($id);

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/" . $file->file;

        $response = Response::make(File::get($path), 200);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $response->header('Content-Type', finfo_file($finfo, $path));
        finfo_close($finfo);

        return $response;
    }

    public function updateCompensation($id)
    {
        $compensation = DosOtherInjuryCompensation::find($id);
        $compensation->update(Input::all());
        $compensation->save();

        Histories::dos_history($compensation->injury_id, 155, Auth::user()->id);
        Flash::success("Dane odszkodowania zostały zaktualizowane");

        $result['code'] = 0;
        return json_encode($result);
    }

    public function deleteCompensation($id)
    {
        $compensation = DosOtherInjuryCompensation::with('injury_file')->find($id);
        if($compensation->injury_file  )
            $compensation->injury_file->update(['active' => 1]);

        Histories::dos_history($compensation->injury_id, 171 , Auth::user()->id, ( $compensation->injury_file )?'<a target="_blank" href="'. URL::route('dos.other.injuries.downloadDoc', array($compensation->injury_file->id)).'">pobierz</a>' : '');

        $compensation->delete();

        $result['code'] = 0;
        return json_encode($result);
    }
}
