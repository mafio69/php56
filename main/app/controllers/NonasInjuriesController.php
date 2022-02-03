<?php


class NonasInjuriesController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

    public function setWithoutCompany($id)
    {
        $injury = Injury::find($id);

        $injury->branch_id = '-1';
        $injury->step = 10;
        $injury->touch();

        Histories::history($id, 131, Auth::user()->id);

        if( $injury->save() ) {

            Session::put('last_injury', $id);
            Session::put('last_injury_case_nr', $injury->case_nr);

            $result['code'] = 1;
            $result['url'] = URL::route('injuries-info', array($injury->id));
            return json_encode($result);
        }

    }

    public function setAssignBranch($id){
        $injury = Injury::find($id);

        $injury->branch_id = Input::get('id_warsztat');
        $injury->step = 10;
        $injury->touch();

        Histories::history($id, 31, Auth::user()->id);

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
                $vehicle = Vehicles::find($injury->vehicle_id);

                $msg = "Informujemy, że likwidację szkody w pojeździe " . $vehicle->registration . " wykona serwis: " . $branch->short_name . ", ulica " . $branch->street . ", " . $branch->city . ", tel. " . $branch->phone . ". CAS w imieniu ".$vehicle->owner->name;

                send_sms($phone_nb, $msg);

                Histories::history($injury->id, 138, Auth::user()->id, $phone_nb);
            }
        }

        $injury->save();
        if($injury->sap)
        {
            $sap = new Idea\SapService\Sap();
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($injury->id, 217, Auth::user()->id);
            }
        }

        //echo 0;
        Session::put('last_injury', $id);
        Session::put('last_injury_case_nr', $injury->case_nr);

        $result['code'] = 1;
        $result['url'] = URL::route('injuries-info', array($injury->id));
        return json_encode($result);
    }

    public function setTotal($id)
    {
        $injury = Injury::find($id);
        $injury->prev_step = $injury->step;
        $injury->step = 30;
        $injury->total_status_id = 11;
        $injury->touch();

        Histories::history($id, 30, Auth::user()->id);

        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 11,
            'status_type' => 'InjuryTotalStatuses'
        ]);


        InjuryWreck::create(array(
            'injury_id'  =>  $id
        ));

        if( $injury->save() ){
            Session::put('last_injury', $id);
            Session::put('last_injury_case_nr', $injury->case_nr);

            $result['code'] = 1;
            $result['url'] = URL::route('injuries-total');
            return json_encode($result);
        }

    }

    public function setTheft($id)
    {
        $injury = Injury::find($id);

        $injury->step = '-3';
        $injury->touch();

        Histories::history($id, 118, Auth::user()->id);

        if( $injury->save() ){
            Session::put('last_injury', $id);
            Session::put('last_injury_case_nr', $injury->case_nr);

            $result['code'] = 1;
            $result['url'] = URL::route('injuries-theft');
            return json_encode($result);
        }

    }

    public function setComplete($id)
    {
        $injury = Injury::find($id);

        $injury->step = '15';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_normal = date('Y-m-d H:i:s');
        $injury->touch();

        Histories::history($id, 114, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }

    public function setCompleteRefused($id)
    {
        $injury = Injury::find($id);

        $injury->step = '24';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_normal = date('Y-m-d H:i:s');
        $injury->touch();

        Histories::history($id, 173, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setCompleteL($id)
    {
        $injury = Injury::find($id);

        $injury->step = '17';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_normal = date('Y-m-d H:i:s');
        $injury->touch();

        Histories::history($id, 115, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }



    public function setCompleteN($id)
    {
        $injury = Injury::find($id);

        $injury->step = '19';
        $injury->touch();

        Histories::history($id, 116, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }

    public function setRefusal($id)
    {
        $injury = Injury::find($id);

        $injury->step = '20';
        $injury->touch();

        Histories::history($id, 117, Auth::user()->id);

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }

    }

}
