<?php

class InjuriesManageController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
		$this->beforeFilter('permitted:zlecenia_(szkody)#zarzadzaj', ['only' => ['getClaimsResignation', 'postClaimsResignation', 'getCompleteWithoutAssistance', 'postCompleteWithoutAssistance', 'getBackFromToSettleToInprogress', 'postBackFromToSettleToInprogress', 'getClaimsResignation', 'postClaimsResignation', 'getCompleteTotalWithoutAssistance', 'postCompleteTotalWithoutAssistance']]);
		$this->beforeFilter('permitted:kartoteka_szkody#komunikator', ['only' => ['getAssignLeader', 'postAssignLeader', 'getRemoveLeader', 'postRemoveLeader', 'getMarkAsLeader', 'postMarkAsLeader', 'getAssignSettlementsLeader', 'postAssignSettlementsLeader', 'getMarkAsSettlementsLeader', 'postMarkAsSettlementsLeader']]);
		$this->beforeFilter('permitted:kartoteka_szkody#komunikator#przypisz_prowadzacego', ['only' => ['getAssignLeader', 'postAssignLeader', 'getAssignSettlementsLeader', 'postAssignSettlementsLeader']]);
		$this->beforeFilter('permitted:kartoteka_szkody#komunikator#usun_prowadzacego', ['only' => ['getRemoveLeader', 'postRemoveLeader']]);
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator#wez_sprawe', ['only' => ['getMarkAsLeader', 'postMarkAsLeader']]);
	}

	public function getToSettle($injury_id)
	{
		return View::make('injuries.dialog.to-settle', compact('injury_id'));
	}

	public function postToSettle($injury_id)
	{
		$injury = Injury::with('branch.company.groups')->find($injury_id);

		$injury->prev_step = $injury->step;

        if(
            $injury->branch_id != '-1' &&  $injury->branch_id != '0' &&
            (
                $injury->branch->company->groups->contains(1) ||
                ( $injury->branch->company->groups->contains(5) && $injury->vehicle->cfm == 1 )
            )
        )
        {
            $injury->step = 14;
        }else{
            $injury->step = 13;
        }


		$injury->save();

		Histories::history($injury_id, 162);

		$result['code'] = 0;
		return json_encode($result);
	}

	public function getSettled($injury_id)
	{
		return View::make('injuries.dialog.settled', compact('injury_id'));
	}

	public function postSettled($injury_id)
	{
		$injury = Injury::find($injury_id);

		$contract = $injury->vehicle->nr_contract;
		$issuedate = $injury->date_event;
		$issuenumber = $injury->case_nr;
		$issuetype = 'B';
		$username = substr(Auth::user()->login, 0, 10);
		$owner_id = $injury->vehicle->owner_id;

		if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
			$data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

			$webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

			$xml = $webservice->getResponseXML();
		}
		if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

			if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){
				$data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

				$webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

				$xml = $webservice->getResponseXML();

				if( $xml->Error->ErrorCde != 'ERR0000'){
					$result['code'] = 2;
					$result['error'] = $xml->Error->ErrorDes->__toString();
					return json_encode($result);
				}else{
					$data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

					$webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

					$xml = $webservice->getResponseXML();

					if( $xml->Error->ErrorCde != 'ERR0000'){
						$result['code'] = 2;
						$result['error'] = $xml->Error->ErrorDes->__toString();
						return json_encode($result);
					}
				}
			}

			$injury->prev_step = $injury->step;
			if($injury->step == 13){
                $injury->step = 16;
            }else{
			    $injury->step = 21;
            }
			$injury->date_end = date("Y-m-d H:i:s");
            $step = InjurySteps::findOrFail($injury->step);
            switch ($step->injury_group_id){
                case 1:
                    $injury->date_end_normal = date("Y-m-d H:i:s");
                    break;
                case 2:
                    $injury->date_end_total = date("Y-m-d H:i:s");
                    break;
                case 3:
                    $injury->date_end_theft = date("Y-m-d H:i:s");
                    break;
            }
			$injury->save();

			Histories::history($injury_id, 163);

			$result['code'] = 0;
			return json_encode($result);
		}else{
			$result['code'] = 2;
			$result['error'] = $xml->Error->ErrorDes->__toString();
			return json_encode($result);
		}
	}

	public function getClaimsResignation($injury_id)
	{
		return View::make('injuries.dialog.claims-resignation', compact('injury_id'));
	}

	public function postClaimsResignation($injury_id)
	{
		$injury = Injury::find($injury_id);

		$contract = $injury->vehicle->nr_contract;
		$issuedate = $injury->date_event;
		$issuenumber = $injury->case_nr;
		$issuetype = 'B';
		$username = substr(Auth::user()->login, 0, 10);
		$owner_id = $injury->vehicle->owner_id;

		if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
			$data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

			$webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

			$xml = $webservice->getResponseXML();
		}
		if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

			if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){
				$data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

				$webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

				$xml = $webservice->getResponseXML();

				if( $xml->Error->ErrorCde != 'ERR0000'){
					$result['code'] = 2;
					$result['error'] = $xml->Error->ErrorDes->__toString();
					return json_encode($result);
				}else{
					$data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

					$webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

					$xml = $webservice->getResponseXML();

					if( $xml->Error->ErrorCde != 'ERR0000'){
						$result['code'] = 2;
						$result['error'] = $xml->Error->ErrorDes->__toString();
						return json_encode($result);
					}
				}
			}

			$injury->prev_step = $injury->step;
			$injury->step = '18';
			$injury->date_end = date("Y-m-d H:i:s");
			$injury->date_end_normal = date('Y-m-d H:i:s');
			$injury->save();

			Histories::history($injury_id, 164);

			$result['code'] = 0;
			return json_encode($result);
		}else{
			$result['code'] = 2;
			$result['error'] = $xml->Error->ErrorDes->__toString();
			return json_encode($result);
		}
	}

	public function getBackFromToSettleToInprogress($injury_id){
		return View::make('injuries.dialog.back-from-to-settle-to-inprogress', compact('injury_id'));
	}

	public function postBackFromToSettleToInprogress($injury_id){
		$injury = Injury::find($injury_id);

		$injury->prev_step = null;
		$injury->step = '10';
		$injury->save();

		Histories::history($injury_id, 165);

		$result['code'] = 0;
		return json_encode($result);
	}

	public function getAssignLeader($injury_id)
	{
		$users = User::where('login', '!=', 'default')
			            ->orderBy('name')->lists('name', 'id');
		return View::make('injuries.dialog.assign-leader', compact('injury_id', 'users'));
	}

	public function postAssignLeader($injury_id)
	{
		$injury = Injury::find($injury_id);
		$injury->leader_id = Input::get('leader_id');
		$injury->leader_assign_date = date('Y-m-d H:i:s');
		$injury->save();

		Histories::history($injury_id, 167, Auth::user()->id, $injury->leader->name);

		$result['code'] = 0;
		return json_encode($result);
	}

	public function getRemoveLeader($injury_id)
	{
		return View::make('injuries.dialog.remove-leader', compact('injury_id'));
	}

	public function postRemoveLeader($injury_id)
	{
		$injury = Injury::find($injury_id);

		$leaderName = $injury->leader->name;

		$injury->leader_id = null;
		$injury->leader_assign_date = null;
		$injury->save();

		Histories::history($injury_id, 199, Auth::user()->id, $leaderName);

		$result['code'] = 0;
		return json_encode($result);
	}

	public function getMarkAsLeader($injury_id)
	{
		return View::make('injuries.dialog.mark-as-leader', compact('injury_id'));
	}

	public function postMarkAsLeader($injury_id)
	{
		$injury = Injury::find($injury_id);
		$injury->leader_id = Auth::user()->id;
		$injury->leader_assign_date = date('Y-m-d H:i:s');
		$injury->save();

		Histories::history($injury_id, 167, Auth::user()->id, $injury->leader->name);

		$result['code'] = 0;
		return json_encode($result);
	}

    public function getAssignSettlementsLeader($injury_id)
    {
        $users = User::where('login', '!=', 'default')
	            ->where(function($query){ $query->where('user_group_id', '!=' , 1)->orWhereNull('user_group_id'); })
	            ->orderBy('name')->lists('name', 'id');
        return View::make('injuries.dialog.assign-settlements-leader', compact('injury_id', 'users'));
    }

    public function postAssignSettlementsLeader($injury_id)
    {
        $injury = Injury::find($injury_id);
        $injury->settlements_leader_id = Input::get('leader_id');
        $injury->settlements_leader_assign_date = date('Y-m-d H:i:s');
        $injury->save();

        Histories::history($injury_id, 172, Auth::user()->id, $injury->settlementsLeader->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getMarkAsSettlementsLeader($injury_id)
    {
        return View::make('injuries.dialog.mark-as-settlements-leader', compact('injury_id'));
    }

    public function postMarkAsSettlementsLeader($injury_id)
    {
        $injury = Injury::find($injury_id);
        $injury->settlements_leader_id = Auth::user()->id;
        $injury->settlements_leader_assign_date = date('Y-m-d H:i:s');
        $injury->save();

        Histories::history($injury_id, 172, Auth::user()->id, $injury->settlementsLeader->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function postToggleRepairStage()
    {
        $injury_id = Input::get('injury_id');
        $stage_id = Input::get('stage_id');

        $injury = Injury::findOrFail($injury_id);
        $injury_stage = $injury->repairStages()->where('t_injury_repair_stage_id', $stage_id)->first();

        if($injury_stage->value == 1){
            $injury_stage->update(['value' => 0]);
        }else {
            $injury_stage->update(['value' => 1]);
        }

        $currentStage = $injury->repairStages()->where('value', 1)->latest('t_injury_repair_stage_id')->first();
        $injury->update(['current_injury_repair_stage_id' => (! $currentStage) ? null : $currentStage->id]);

        Histories::history($injury->id, 175, Auth::user()->id, $injury_stage->stage->name.(($injury_stage->value == 1) ? ' - tak' : ' - nie'));

        $result['status'] = 'success';

        if($currentStage) {
            $result['current'] = $currentStage->t_injury_repair_stage_id;
            $result['current_name'] = $currentStage->stage->name;
            $result['current_checked_description'] = $currentStage->stage->checked_description;
        }

        $result['checked_description'] = $injury_stage->stage->checked_description;
        $result['unchecked_description'] = $injury_stage->stage->unchecked_description;
        return json_encode($result);
    }

    public function postUpdateRepairStageDate()
    {
        $injury_id = Input::get('injury_id');
        $stage_id = Input::get('stage_id');
        $injury = Injury::findOrFail($injury_id);
        $injury_stage = $injury->repairStages()->where('t_injury_repair_stage_id', $stage_id)->first();

        $date_value = Input::get('date_value');
        if($date_value == '' || $date_value == '-0001-11-30' || $date_value == '0000-00-00')
        {
            $date_value = null;
        }

        $injury_stage->update(['date_value' => $date_value]);

        $result['status'] = 'success';
        return json_encode($result);
    }

    public function postUpdateRepairStageComment()
    {
        $stage_id = Input::get('stage_id');
        $injury_id = Input::get('injury_id');

        $injury = Injury::findOrFail($injury_id);
        $injury_stage = $injury->repairStages()->where('t_injury_repair_stage_id', $stage_id)->first();

        $comment = Input::get('comment');
        $injury_stage->update(['comment' => $comment]);

        $result['status'] = 'success';
        return json_encode($result);
    }

    public function getCompleteWithoutAssistance($injury_id)
    {
        return View::make('injuries.dialog.complete-without-assistance', compact('injury_id'));
    }

    public function postCompleteWithoutAssistance($injury_id)
    {
        $injury = Injury::find($injury_id);

        $contract = $injury->vehicle->nr_contract;
        $issuedate = $injury->date_event;
        $issuenumber = $injury->case_nr;
        $issuetype = 'B';
        $username = substr(Auth::user()->login, 0, 10);
        $owner_id = $injury->vehicle->owner_id;

        if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

            $xml = $webservice->getResponseXML();
        }
        if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

            if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){
                $data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if( $xml->Error->ErrorCde != 'ERR0000'){
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    return json_encode($result);
                }else{
                    $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $xml = $webservice->getResponseXML();

                    if( $xml->Error->ErrorCde != 'ERR0000'){
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        return json_encode($result);
                    }
                }
            }

            $injury->prev_step = $injury->step;
            $injury->step = '26';
            $injury->date_end = date("Y-m-d H:i:s");
            $injury->date_end_normal = date('Y-m-d H:i:s');
            $injury->save();

            Histories::history($injury_id, 206);

            $result['code'] = 0;
            return json_encode($result);
        }else{
            $result['code'] = 2;
            $result['error'] = $xml->Error->ErrorDes->__toString();
            return json_encode($result);
        }
    }

    public function getCompleteTotalWithoutAssistance($injury_id)
    {
        return View::make('injuries.dialog.complete-total-without-assistance', compact('injury_id'));
    }

    public function postCompleteTotalWithoutAssistance($injury_id)
    {
        $injury = Injury::find($injury_id);

        $contract = $injury->vehicle->nr_contract;
        $issuedate = $injury->date_event;
        $issuenumber = $injury->case_nr;
        $issuetype = 'B';
        $username = substr(Auth::user()->login, 0, 10);
        $owner_id = $injury->vehicle->owner_id;

        if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

            $xml = $webservice->getResponseXML();
        }
        if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

            if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){
                $data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if( $xml->Error->ErrorCde != 'ERR0000'){
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    return json_encode($result);
                }else{
                    $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $xml = $webservice->getResponseXML();

                    if( $xml->Error->ErrorCde != 'ERR0000'){
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        return json_encode($result);
                    }
                }
            }

            $injury->prev_step = $injury->step;
            $injury->step = '38';
            $injury->date_end = date("Y-m-d H:i:s");
            $injury->date_end_normal = date('Y-m-d H:i:s');
            $injury->save();

            Histories::history($injury_id, 211);

            $result['code'] = 0;
            return json_encode($result);
        }else{
            $result['code'] = 2;
            $result['error'] = $xml->Error->ErrorDes->__toString();
            return json_encode($result);
        }
    }
}
