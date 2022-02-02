<?php

class DosOtherInjuriesInfoliniaController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function getSearch(){
        return View::make('dos.other_injuries-infolinia.search');
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

    public function getSearchContract(){
        $object = Objects::where('nr_contract', '=', Input::get('contract'))->where('active' ,'=', 0)->orderBy('parent_id', 'desc')->first();

        if( is_null($object)){
            return '-1';
        }

        $objects = DB::select( DB::raw('
				SELECT T2.id
				FROM (
				    SELECT
				        @r AS _id,
				        (SELECT @r := parent_id FROM objects WHERE id = _id) AS parent_id,
				        @l := @l + 1 AS lvl
				    FROM
				        (SELECT @r := '.$object->id.', @l := 0) vars,
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

        $injuries = DosOtherInjury::where('active', '=', '0')->whereIn('object_id', $objectsA)->with('object', 'injuries_type', 'user')->orderBy('created_at','desc')->get();

        return View::make('dos.other_injuries-infolinia.search-result', compact('injuries', 'users', 'counts'));
    }

    public function getSearchInjury_nr(){

        $injuries = DosOtherInjury::where('injury_nr', '=', Input::get('injury_nr'))->where('active', '=', '0')->with('object', 'injuries_type', 'user')->orderBy('created_at','desc')->get();

        if( is_null($injuries)){
            return '-1';
        }

        return View::make('dos.other_injuries-infolinia.search-result', compact('injuries', 'users', 'counts'));
    }

    public function getInfo($id)
    {
        $url = URL::previous();

        Session::put('prev', $url);

        Histories::dos_history($id, 133, Auth::user()->id);

        $injury = DosOtherInjury::find($id);

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
            $query->where('type', '=', 2)->orWhere('type', '=', 3);
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

        return View::make('dos.other_injuries-infolinia.info', compact('injury', 'info', 'remarks', 'imagesBefore',
            'imagesInprogress', 'imagesAfter', 'type_incident', 'history', 'documentsTypes', 'documents', 'genDocumentsA', 'remarks_damage',
            'chat', 'invoices', 'templates', 'phonesSMS' , 'theftAcceptation'));

    }

    public function getObjectContractList()
    {

        $term = Input::get('term');

        $objects = Objects::where('nr_contract', 'like', '%'.$term.'%')->groupBy('nr_contract')->orderBy('parent_id', 'desc')->get();

        $result = array();
        foreach($objects as $k => $v){
            $result[] = array(
                "id"=>$v->id,
                "label"=>$v->nr_contract.' - '.$v->factoryNbr,
                "value" => $v->nr_contract
            );
        }

        return json_encode($result);

    }

    public function getObjectInjury_nrList()
    {

        $term = Input::get('term');

        $injuries = DosOtherInjury::where('injury_nr', 'like', '%'.$term.'%')->where('active', '=', '0')->with('object')->orderBy('created_at', 'desc')->get();



        $result = array();
        foreach($injuries as $k => $v){


            $result[] = array(
                "id"=>$v->id,
                "label"=>$v->injury_nr.' - '.$v->object->factoryNbr,
                "value" => $v->injury_nr
            );
        }

        return json_encode($result);

    }
}
