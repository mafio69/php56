<?php

class DosOtherInjuriesController extends BaseController {

    private $counts;

    public function __construct(){

        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia#wejscie');
        $this->beforeFilter('permitted:zlecenia#szkody_nieprzetworzone', ['only' => ['getUnprocessed', 'getDeleted']]);
        $this->beforeFilter('permitted:zlecenia#szkody_zarejestrowane', ['only' => ['getNew', 'getInprogress', 'getCompleted']]);
        $this->beforeFilter('permitted:zlecenia#szkody_calkowite#kradzieze', ['only' => ['getTotal', 'getTheft', 'getTotalFinished']]);
        $this->beforeFilter('permitted:zlecenia#szkody_anulowane', ['only' => ['getCanceled']]);

        $query = DosOtherInjury::where('active', '=', 0);

        if(Session::get('search.injury_type', '0') != 0)
            $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

        if(Session::get('search.user_id', '0') != 0)
            $query ->where('user_id', '=', Session::get('search.user_id') );


        if(Session::get('search.locked_status', '0') == 1)
            $query ->whereIn('locked_status', array(5, '-5'));

        if(Session::get('search.leader_id', '0') != 0)
            $query ->where('leader_id', '=', Session::get('search.leader_id') );

        $res = $query->groupBy('step')->get(array('step', DB::raw('count(*) as cnt')));

        $array = array();
        foreach ($res as $k => $row) {
            $array[$row->step] = $row->cnt;
        }

        $unprocessed = MobileInjury::whereActive(0)->whereNotIn('source', [0,3])
                                    ->whereIn('injuries_type', [4,5])->count();
        
        $deleted = MobileInjury::onlyTrashed()
        ->where('active', '=', '0')
        ->whereNotIn('source', [0,3])
        ->whereIn('injuries_type', [4,5])
        ->count();                     

        $array['-1'] = $unprocessed;
        $array['-2'] = $deleted;

        $this->counts = $array;

        $query = DosOtherInjury::where('active', '=', 0);

        if(Session::get('search.injury_type', '0') != 0)
            $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

        if(Session::get('search.user_id', '0') != 0)
            $query ->where('user_id', '=', Session::get('search.user_id') );


        if(Session::get('search.locked_status', '0') == 1)
            $query ->whereIn('locked_status', array(5, '-5'));

        $ppis = $query->where('injuries_type_id', 9)->count();
        $this->counts['ppi'] = $ppis;
    }

    public function getUnprocessed(){
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = MobileInjury::where('active', '=', '0')
            ->whereNotIn('source', [0,3])
            ->whereIn('injuries_type', [4,5])
            ->where(function($query)
            {
                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){

                    $query->where(function($query2){

                        if(Input::has('registration')){
                            $query2 -> orWhere('registration', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('leasing_nr')){
                            $query2 -> orWhere('nr_contract', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('address')){
                            $query2 -> orWhere('event_city', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('surname')){
                            $query2 -> orWhere('notifier_surname', 'like', '%'.Input::get('term').'%');
                        }

                    });
                }

            })
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.unprocessed', compact('injuries', 'users', 'counts', 'injuries_type'));
    }

    public function getNew(){
        $users = User::where('active','=',0)->get();
        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->where('step', '=', '0')
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.new', compact('injuries', 'users', 'counts', 'injuries_type'));
    }

    public function getInprogress()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->whereIn('step', ['10', '46'])
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.inprogress', compact('injuries', 'users', 'counts', 'injuries_type'));
    }

    public function getCompleted()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->whereIn('step', array('15', '17', '19', '20', '21', '41') )
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.completed', compact('injuries', 'counts', 'users', 'injuries_type'));
    }

    public function getTotal()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->whereIn('step', ['25', '26', '27', '44'])
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('date_end','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.total', compact('injuries', 'counts', 'users', 'injuries_type'));
    }

    public function getTheft()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->whereIn('step', ['30', '31', '32', '45'])
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('date_end','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.theft', compact('injuries', 'counts', 'users', 'injuries_type'));
    }

    public function getTotalFinished()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->whereIn('step', ['28', '29', '33', '34', '42', '43'])
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('date_end','desc')->paginate(Session::get('search.pagin', '10'));
        $counts = $this->counts;

        return View::make('dos.other_injuries.total-finished', compact('injuries', 'counts', 'users', 'injuries_type'));
    }

    public function getCanceled()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->where('step', '=', '-10')
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.canceled', compact('injuries', 'counts', 'users', 'injuries_type'));
    }

    public function getDeleted()
    {
        $users = User::where('active','=',0)->get();


        $injuries_type = DosInjuryType::all();

        $injuries = MobileInjury::onlyTrashed()
            ->where('active', '=', '0')
            ->whereNotIn('source', [0,3])
            ->whereIn('injuries_type', [4,5])
            ->where(function($query)
            {
                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){

                    $query->where(function($query2){

                        if(Input::has('registration')){
                            $query2 -> orWhere('registration', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('leasing_nr')){
                            $query2 -> orWhere('nr_contract', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('address')){
                            $query2 -> orWhere('event_city', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('surname')){
                            $query2 -> orWhere('notifier_surname', 'like', '%'.Input::get('term').'%');
                        }

                    });
                }

            })->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));
        
        $counts = $this->counts;

        return View::make('dos.other_injuries.deleted', compact('injuries', 'users', 'counts', 'injuries_type'));
    }

    public function getPpi()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->where('injuries_type_id', 9)
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'leader')->orderBy('date_end','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.ppi', compact('injuries', 'counts', 'users', 'injuries_type'));
    }

    public function getSearchGlobal(){

        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->filter(Input::instance())
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'status')
            ->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('dos.other_injuries.search-global', compact('injuries', 'users', 'injuries_type', 'counts'));
    }

    public function getTasksExpired()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->where(function($query)
            {
                if(Session::get('search.injury_type', '0') != 0)
                    $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.locked_status', '0') == 1)
                    $query ->whereIn('locked_status', array(5, '-5'));


                $query->where(function($query2){
                    $query2->orWhereHas('chat', function($q){
                        $q->where('deadline', '<', date('Y-m-d') );
                    });
                });

            })
            ->where('step' , '!=', '-10')
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = 0;

        return View::make('dos.other_injuries.tasks-expired', compact('injuries', 'users', 'injuries_type', 'counts', 'step'));
    }

    public function getTasksToday()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = DosInjuryType::all();

        $injuries = DosOtherInjury::where('active', '=', '0')
            ->where(function($query)
            {
                if(Session::get('search.injury_type', '0') != 0)
                    $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.locked_status', '0') == 1)
                    $query ->whereIn('locked_status', array(5, '-5'));


                $query->where(function($query2){

                    $query2->orWhereHas('chat', function($q){
                        $q->where('deadline', '=', date('Y-m-d') );
                    });
                });

            })
            ->where('step' , '!=', '-10')
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = 0;

        return View::make('dos.other_injuries.tasks-today', compact('injuries', 'users', 'injuries_type', 'counts', 'step'));
    }

    public function getSearch(){

        $last =  URL::previous();
        $url = strtok($last, '?');

        $gets = '';

        if(Input::has('search_term')){
            $gets = '?';

            if(Input::has('case_nr'))
                $gets .= 'case_nr=1&';

            if(Input::has('registration'))
                $gets .= 'registration=1&';

            if(Input::has('injury_nr'))
                $gets .= 'injury_nr=1&';

            if(Input::has('leasing_nr'))
                $gets .= 'leasing_nr=1&';

            if(Input::has('address'))
                $gets .= 'address=1&';

            if(Input::has('global'))
                $gets .= 'global=1&';

            if(Input::has('surname'))
                $gets .= 'surname=1&';

            if(Input::has('client'))
                $gets .= 'client=1&';

            if(Input::has('firmID'))
                $gets .= 'firmID=1&';

            if(Input::has('NIP'))
                $gets .= 'NIP=1&';

            $gets.='term='.Input::get('search_term');
        }

        if(Input::has('global')){
            echo URL::route('dos.other.injuries.search.getAll').$gets;
        }else{
            echo $url.$gets;
        }

    }

    public function setInprogress($id){
        $injury = DosOtherInjury::find($id);

        $injury->step = 10;
        $injury->touch();

        Histories::dos_history($id, 151, Auth::user()->id);

        if( $injury->save() ) {
            //echo 0;
            Session::put('last_injury', $id);
            Session::put('last_injury_case_nr', $injury->case_nr);

            $result['code'] = 1;
            $result['url'] = URL::route('dos.other.injuries.info', array($injury->id));
            return json_encode($result);
        }


    }

    public function setComplete($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '15';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 114, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setCompleteL($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '17';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 115, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setCompleteN($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '19';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 116, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }

    public function setCompletedPayment($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '20';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_normal = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 200, Auth::user()->id);

        $injury->save();

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setCompletedRefuse($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '21';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_normal = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 201, Auth::user()->id);

        $injury->save();

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setCompletedWithoutRepaired($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '19';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_normal = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 116, Auth::user()->id);

        $injury->save();

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setUnlock($id)
    {
        $injury = DosOtherInjury::find($id);
        $injury->locked_status = '-5';
        $injury->touch();

        Histories::dos_history($id, 112 , Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setLock($id)
    {
        $injury = DosOtherInjury::find($id);
        $injury->locked_status = '5';
        $injury->touch();

        Histories::dos_history($id, 113 , Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setCancel($id)
    {
        $injury = DosOtherInjury::find($id);
        $injury->prev_step = $injury->step;
        $injury->step = '-10';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 29, Auth::user()->id);

        //dodanie wątku rozmowy w kartotece
        $status = bindec('100');

        if (get_chat_group() == 1)
            $dos_read = date('Y-m-d H:i:s');
        else
            $dos_read = null;

        if (get_chat_group() == 3)
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        if (get_chat_group() == 2)
            $branch_read = date('Y-m-d H:i:s');
        else
            $branch_read = null;

        if($injury->canceled_chat_id == null) {
            $chat = DosOtherInjuryChat::create(array(
                    'injury_id' => $id,
                    'user_id' => Auth::user()->id,
                    'topic' => 'Anulowanie szkody',
                    'status' => $status
                )
            );

            $injury->canceled_chat_id = $chat->id;
        }else{
            $chat = DosOtherInjuryChat::find($injury->canceled_chat_id);
        }

        DosOtherInjuryChatMessages::create(array(
                'chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> Input::get('content'),
                'status'	=> $status,
                'dos_read'	=> $dos_read,
                'info_read'	=> $info_read,
                'branch_read' => $branch_read
            )
        );


        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setCancelMobile($id)
    {
        $injury = MobileInjury::find($id);
        $injury->active = 8;
        $injury->save();

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }
    
    public function setDelete($id)
    {
        $injury = MobileInjury::find($id);

        if( $injury->delete() ){    
            // Histories::dos_history($id, 140, Auth::user()->id, 'Usunięto szkodę');
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setRestoreCanceled($id)
    {
        $injury = DosOtherInjury::find($id);

        $prev_step = $injury->prev_step;

        if( is_null($prev_step) || $prev_step == '' )
        {
            if($injury->branch_id == 0)
                $prev_step = 0;
            else
                $prev_step = 10;
        }

        $injury->step = $prev_step;
        $injury->prev_step = '-10';
        $injury->date_end = null;
        $injury->touch();

        Histories::dos_history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

        //dodanie wątku rozmowy w kartotece
        $status = bindec('100');

        $chat = DosOtherInjuryChat::find($injury->canceled_chat_id);

        if (get_chat_group() == 1)
            $dos_read = date('Y-m-d H:i:s');
        else
            $dos_read = null;

        if (get_chat_group() == 3)
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        if (get_chat_group() == 2)
            $branch_read = date('Y-m-d H:i:s');
        else
            $branch_read = null;

        DosOtherInjuryChatMessages::create(array(
                'chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> Input::get('content'),
                'status'	=> $status,
                'dos_read'	=> $dos_read,
                'info_read'	=> $info_read,
                'branch_read' => $branch_read
            )
        );

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setRestoreCompleted($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->prev_step = $injury->step;
        $injury->step = 10;
        $injury->date_end = null;
        $injury->touch();

        Histories::dos_history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setRestoreTotalFinished($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->prev_step = $injury->step;
        $injury->step = 10;
        $injury->date_end = null;
        $injury->touch();

        Histories::dos_history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setRestoreTheftFinished($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->prev_step = $injury->step;
        $injury->step = 10;
        $injury->date_end = null;
        $injury->touch();

        Histories::dos_history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setRestore($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->prev_step = $injury->step;
        $injury->step = Input::get('step');

        $injury->date_end = null;
        $injury->touch();

        Histories::dos_history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }

    public function setRestoreDeleted($id)
    {
        $injury = MobileInjury::withTrashed()->find($id);

        $injury->restore();
        $injury->touch();

        Histories::dos_history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }

    public function setRefusal($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '20';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::dos_history($id, 117, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }

    public function setTotal($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '25';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 30, Auth::user()->id);

        return json_encode($result);
    }

    public function setTotalPayment($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '26';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 202, Auth::user()->id);

        return json_encode($result);
    }

    public function setTotalRefuse($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '27';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 203, Auth::user()->id);

        return json_encode($result);
    }

    public function setTheft($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '30';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 118, Auth::user()->id);

        return json_encode($result);
    }

    public function setTheftPayment($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '31';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 204, Auth::user()->id);

        return json_encode($result);
    }

    public function setTheftRefuse($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '32';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 205, Auth::user()->id);

        return json_encode($result);
    }

    public function setTheftFinishedPayment($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '33';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_theft = date("Y-m-d H:i:s");
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 179, Auth::user()->id);

        return json_encode($result);
    }

    public function setTheftFinishedRefuse($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '34';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_theft = date("Y-m-d H:i:s");
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 178, Auth::user()->id);

        return json_encode($result);
    }

    public function setTotalFinishedPayment($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '28';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_total = date("Y-m-d H:i:s");
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 180, Auth::user()->id);

        return json_encode($result);
    }

    public function setTotalFinishedRefuse($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '29';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_total = date("Y-m-d H:i:s");
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 181, Auth::user()->id);

        return json_encode($result);
    }

    public function setClaimsResignation($id)
    {
        $injury = DosOtherInjury::find($id);

        if (in_array($injury->step, [0, 10])) $injury->step = 41;
        elseif (in_array($injury->step, [25, 26, 27, 28, 29])) $injury->step = 42; 
        elseif (in_array($injury->step, [30, 31, 32, 33, 34])) $injury->step = 43;
        $injury->save();

        $result['code'] = 0;
        $step = DosOtherInjurySteps::find($injury->step);
        Histories::dos_history($id, 219, Auth::user()->id, $step? $step->name : 'rezygnacja z roszczeń');

        return json_encode($result);
    }

    public function updateObject($id)
    {
        $injury = DosOtherInjury::find($id);
        $object =$injury->object->toArray();
        $object['parent_id'] = $object['id'];

        $newObject = Objects::create($object);
        $newObject->update(Input::all());
        $newObject->save();

        $injury->object_id = $newObject->id;
        $injury->save();

        Histories::dos_history($id, 156, Auth::user()->id);
        echo 0;
    }

    public function updateNotifier($id){
        $injury = DosOtherInjury::find($id);

        $injury->update(Input::all());

        Histories::dos_history($id, 91, Auth::user()->id);
        echo 0;
    }

    public function setAssignLeader($injury_id)
    {
        $injury = DosOtherInjury::find($injury_id);
        $injury->leader_id = Input::get('leader_id');
        $injury->leader_assign_date = date('Y-m-d H:i:s');
        $injury->save();

        Histories::dos_history($injury_id, 167, Auth::user()->id, $injury->leader->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setRemoveLeader($injury_id)
    {
        $injury = DosOtherInjury::find($injury_id);

        $leaderName = $injury->leader->name;

        $injury->leader_id = null;
        $injury->leader_assign_date = null;
        $injury->save();

        Histories::dos_history($injury_id, 199, Auth::user()->id, $leaderName);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setMarkAsLeader($injury_id)
    {
        $injury = DosOtherInjury::find($injury_id);
        $injury->leader_id = Auth::user()->id;
        $injury->leader_assign_date = date('Y-m-d H:i:s');
        $injury->save();

        Histories::dos_history($injury_id, 167, Auth::user()->id, $injury->leader->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function sendDocs($injury_id)
    {
        $injury = DosOtherInjury::find($injury_id);

        $lastHistoryRecord = DosOtherInjuryHistory::where('injury_id', $injury_id)->where('user_id', Auth::user()->id)->where('history_type_id', '168')->orderBy('id', 'desc')->first();
        if($lastHistoryRecord)
        {
            $now = \Carbon\Carbon::now();

            if( $now->diffInSeconds($lastHistoryRecord->created_at) < 5){
                Log::info('mało '.Auth::user()->id);
                return json_encode([
                    'code' => 3
                ]);
            }
        }

        $docsToSend = DosOtherInjuryFiles::whereIn('id', Input::get('doc_ids') )->with('document_type')->get();

        $emails = [];
        $unmachedEmails = [];

        if(Input::has('addressees')) {
            foreach (Input::get('addressees') as $addresse_type => $email) {
                $email = trim($email);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $emails[$email] = $email;
                } else {
                    $unmachedEmails[] = $email;
                }
            }
        }

        if(Input::has('special_emails')) {
            foreach (Input::get('special_emails') as $special_email) {
                $special_email = trim($special_email);

                if (!filter_var($special_email, FILTER_VALIDATE_EMAIL) === false) {
                    $emails[$special_email] = $special_email;
                } else {
                    $unmachedEmails[] = $special_email;
                }
            }
        }

        if(Input::has('custom_emails') && Input::get('custom_emails') != '')
        {
            $custom_emails = explode(',', Input::get('custom_emails'));

            foreach ($custom_emails as $custom_email)
            {
                $custom_email = trim($custom_email);

                if( !filter_var($custom_email, FILTER_VALIDATE_EMAIL) === false ) {
                    $emails[$custom_email] = $custom_email;
                }else{
                    $unmachedEmails[] = $custom_email;
                }
            }
        }

        if(Input::has('clients'))
        {
            foreach(Input::get('clients') as $client)
            {
                if( !filter_var($client, FILTER_VALIDATE_EMAIL) === false ) {
                    $emails[$client] = $client;
                }else{
                    $unmachedEmails[] = $client;
                }
            }
        }

        if(Input::has('insuranceCompanies'))
        {
            foreach(Input::get('insuranceCompanies') as $insuranceCompany)
            {
                if( !filter_var($insuranceCompany, FILTER_VALIDATE_EMAIL) === false ) {
                    $emails[$insuranceCompany] = $insuranceCompany;
                }else{
                    $unmachedEmails[] = $insuranceCompany;
                }
            }
        }
        $email_comment = Input::get('email_comment');

        if(count($emails) > 0 ) {
            Queue::push('Idea\Mail\MailDosQueue', array(
                'injury' => $injury,
                'docsToSend' => $docsToSend->toArray(),
                'email_comment' => $email_comment,
                'emails'=>$emails,
                'injury_id' => $injury_id,
                'doc_ids'=> \Input::get('doc_ids'),
                'user_id'=> Auth::user()->id,
                'url'=> url(),
            ));
        }

        return json_encode([
            'code' => 2,
            'error' => 'Wysłano wiadomość na adresy: '.implode(',', $emails)
        ]);
    }


    public function setSettled($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '46';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 221, Auth::user()->id);

        return json_encode($result);
    }

    public function setTotalSettled($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '44';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 183, Auth::user()->id);

        return json_encode($result);
    }

    public function setTheftSettled($id)
    {
        $injury = DosOtherInjury::find($id);

        $injury->step = '45';
        $injury->touch();
        $injury->save();

        $result['code'] = 0;

        Histories::dos_history($id, 194, Auth::user()->id);

        return json_encode($result);
    }
}


