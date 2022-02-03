<?php

class InsurancesGroupsController extends \BaseController {
    /**
     * InsurancesGroupsController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:wykaz_stawek#wejscie');
        $this->beforeFilter('permitted:wykaz_stawek#zarzadzaj', ['except' => 'getIndex']);
    }

    /**
     * Display a listing of the resource.
     * GET /insurancesgroups
     *
     * @param null $insurance_company_id
     * @param null $id
     * @return Response
     */
	public function getIndex($insurance_company_id = null, $id = null)
	{
        if(is_null($insurance_company_id)) {
            $insurancesCompanyInGroups = LeasingAgreementInsuranceGroup::groupBy('insurance_company_id')->first(['insurance_company_id']);
            $insurance_company_id = $insurancesCompanyInGroups->insurance_company_id;
            return Redirect::to(url('insurances/groups/index', [$insurance_company_id]));
        }
        $groups = LeasingAgreementInsuranceGroup::where('insurance_company_id', $insurance_company_id)->with('rows')->orderBy('created_at', 'desc')->get();

        if($groups->isEmpty())
        {
            LeasingAgreementInsuranceGroup::create(array(
                'create_user_id' => Auth::user()->id,
                'insurance_company_id' => $insurance_company_id
            ));
        }
        $groups = LeasingAgreementInsuranceGroup::where('insurance_company_id', $insurance_company_id)->with('rows', 'rows.rate')->orderBy('created_at', 'desc')->get();

        $groupsA = array();
        foreach($groups as $group)
        {
            if( is_null($group->valid_from) )
                $groupsA[$group->id] = "w trakcie definiowania - utworzona: ".substr($group->created_at, 0, -9);
            else if(is_null($group->valid_to))
                $groupsA[$group->id] = "aktualnie obowiązująca od ".$group->valid_from;
            else
                $groupsA[$group->id] = "archiwalne obowiązująca od ".$group->valid_to." do ".$group->valid_to;
        }

        $last_group = $groups->first();

        if(is_null($id))
        {
            $selected_group = $last_group;
        }else{
            $selected_group = $groups->find($id);
        }

        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');

        return View::make('insurances.groups.index', compact('groups', 'last_group','selected_group', 'groupsA', 'insuranceCompanies'));
	}


    public function getCreate($group_id)
    {
        $group = LeasingAgreementInsuranceGroup::find($group_id);

        $filledRates = $group->rows()->get()->lists('leasing_agreement_insurance_group_rate_id');

        $query = LeasingAgreementInsuranceGroupRate::where('insurance_company_id', $group->insurance_company_id);
        if(count($filledRates) > 0)
            $query->whereNotIn('id', $filledRates);

        $rates = $query->lists('name', 'id');

        return View::make('insurances.groups.create', compact('group_id', 'rates'));
    }

	/**
	 * Store a newly created resource in storage.
	 * POST /insurancesgroups
	 *
	 * @return Response
	 */
	public function postStore()
	{
        $inputs = Input::all();

        if(Input::has('rate_name')){
            $group = LeasingAgreementInsuranceGroup::find(Input::get('leasing_agreement_insurance_group_id'));
            $name = trim(Input::get('rate_name'));
            $ifRateExist = LeasingAgreementInsuranceGroupRate::whereName($name)->where('insurance_company_id', $group->insurance_company_id)->get();
            if($ifRateExist->isEmpty()) {
                $rate = LeasingAgreementInsuranceGroupRate::create([
                    'name' => $name,
                    'insurance_company_id' => $group->insurance_company_id
                ]);
            }else{
                $rate = $ifRateExist->first();
//                $result['code'] = 2;
//                $result['error'] = 'Istnieje już stawka o identycznej nazwie dla danego TU.';
//                return json_encode($result);
            }
            $inputs['leasing_agreement_insurance_group_rate_id'] = $rate->id;
        }

        $rules = array(
            'user_id' => 'required|min:1',
            'leasing_agreement_insurance_group_id'=> 'required|min:1',
            'leasing_agreement_insurance_group_rate_id'=> 'required|min:1',
            'months_12'=> 'required|min:1',
            'months_24'=> 'required|min:1',
            'months_36'=> 'required|min:1',
            'months_48'=> 'required|min:1',
            'months_60'=> 'required|min:1',
            'months_72'=> 'required|min:1',
            'months_84'=> 'min:1',
            'months_96'=> 'min:1',
            'months_108'=> 'min:1',
            'months_120'=> 'min:1'
        );

        $validator = Validator::make($inputs, $rules);

        if ($validator->fails())
        {
            $result['code'] = 2;
            $result['error'] = 'Wystąpił błąd w trakcie dodawania stawki.';
            CustomLog::error('insurances', $result['error'], $inputs);

            return json_encode($result);
        }

        LeasingAgreementInsuranceGroupRow::create($inputs);

        $result['code'] = 0;
        return json_encode($result);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /insurancesgroups/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postUpdate($id)
	{
        if(Input::has('value') && Input::get('value') != '') {
            $groupRow = LeasingAgreementInsuranceGroupRow::find($id);
            $column = Input::get('name');
            if($column == 'leasing_agreement_insurance_group_rate_id')
            {
                $group = $groupRow->insurance_group()->first();
                $rate = $groupRow->rate;
                $ifRateExist = LeasingAgreementInsuranceGroupRate::whereName(Input::get('value'))->where('insurance_company_id', $group->insurance_company_id)->get();
                if($ifRateExist->isEmpty()) {
                    $rate->name = Input::get('value');
                    $rate->save();

                    $result['success'] = true;
                    $result['notification'] = "Nazwa stawki została zaktualizowana globalnie dla TU.";
                }else{
                    $result['success'] = false;
                    $result['msg'] = 'Istnieje już zdefiniowana stawka o danej nazwie dla zadanego TU.';
                }
            }else {
                $groupRow->$column = Input::get('value');
                $groupRow->save();
                $result['success'] = true;
                $result['notification'] = "Stawka dla <strong>" . $groupRow->name . "</strong> została zaktualizowana.";
            }
        }else{
            $result['success'] = false;
            $result['msg'] = 'Pole wymagane.';
        }
        return json_encode($result);
	}

    public function getDelete($id)
    {
        $group = LeasingAgreementInsuranceGroupRow::find($id);

        return View::make('insurances.groups.delete', compact('group'));
    }

	/**
	 * Remove the specified resource from storage.
	 * DELETE /insurancesgroups/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postDestroy($id)
	{
        $group_row = LeasingAgreementInsuranceGroupRow::find($id);

        $group_row->delete();
        Log::info(Auth::user()->name.' Usunięto grupę stawek '.$group_row->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getClose($id)
    {
        return View::make('insurances.groups.close', compact('id'));
    }

    public function postConfirm($id)
    {
        $previousGroupID = LeasingAgreementInsuranceGroup::where('id', '<', $id)->max('id');
        if($previousGroupID)
        {
            $prevGroup = LeasingAgreementInsuranceGroup::find($previousGroupID);
            $prevGroup->valid_to = date('Y-m-d');
            $prevGroup->save();
        }

        $group = LeasingAgreementInsuranceGroup::find($id);
        $group->close_user_id = Auth::user()->id;
        $group->valid_from = date('Y-m-d');
        $group->save();

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getBase($id)
    {
        $base_group = LeasingAgreementInsuranceGroup::find($id);

        $new_group = LeasingAgreementInsuranceGroup::create(array(
            'create_user_id' => Auth::user()->id,
            'insurance_company_id' => $base_group->insurance_company_id
        ));

        foreach ($base_group->rows as $row) {
            $new_row = LeasingAgreementInsuranceGroupRow::create($row->toArray());
            $new_group->rows()->save($new_row);

            foreach($row->packages as $package)
            {
                $package = LeasingAgreementInsuranceGroupRowPackage::create($package->toArray());
                $package->leasing_agreement_insurance_group_row_id = $new_row->id;
                $package->save();
            }
        }

        return Redirect::to(url('insurances/groups/index',[$base_group->insurance_company_id]));
    }

    public function getFresh($id)
    {
        $base_group = LeasingAgreementInsuranceGroup::find($id);

        LeasingAgreementInsuranceGroup::create(array(
            'create_user_id' => Auth::user()->id,
            'insurance_company_id' => $base_group->insurance_company_id
        ));

        return Redirect::to(url('insurances/groups/index', [$base_group->insurance_company_id]));
    }

    public function getAssignInsuranceCompany()
    {
        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::where('active', '=', '0')->whereNotIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '--- wybierz ubezpieczyciela ---';

        return View::make('insurances.groups.assign', compact('insuranceCompanies'));
    }

    public function postAddInsuranceCompanyToGroups()
    {
        if(Input::get('insurance_company_id') == 0)
        {
            $rules = array(
                'name' => 'required'
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails())
            {
                $result['code'] = 2;
                $result['error'] = 'Nazwa ubezpieczyciela jest wymagana.';
                return json_encode($result);
            }
            $insuranceCompany = Insurance_companies::create(Input::all());
        }else
            $insuranceCompany =Insurance_companies::find(Input::get('insurance_company_id'));

        Flash::success('Dodane ubezpieczalnię '.$insuranceCompany->name.' do grup stawek.');
        $result['code'] = 1;
        $result['url'] = URL::to(url('insurances/groups/index', [$insuranceCompany->id]));
        return json_encode($result);
    }


    public function postGenerateMinRow($row_id)
    {
        $row = LeasingAgreementInsuranceGroupRow::find($row_id);
        $row->if_minimal = 1;
        $row->save();

        return Redirect::back();
    }

    public function postRemoveMinRow($row_id)
    {
        $row = LeasingAgreementInsuranceGroupRow::find($row_id);
        $row->if_minimal = 0;
        $row->save();

        return Redirect::back();
    }

    public function postAddPackage($row_id)
    {
        LeasingAgreementInsuranceGroupRowPackage::create(['leasing_agreement_insurance_group_row_id' => $row_id]);

        return Redirect::back();
    }

    public function postUpdatePackage($id)
    {
        if(Input::has('value') && Input::get('value') != '') {
            $groupRow = LeasingAgreementInsuranceGroupRowPackage::find($id);
            $column = Input::get('name');

            if($column != 'name') {
                $parts = explode('_', $column);
                if ($parts[2] == 'percentage') {
                    $parts[2] = 'amount';
                } else {
                    $parts[2] = 'percentage';
                }
                $update_col = implode('_', $parts);
                $groupRow->$update_col = null;
                $result['data_name'] = $update_col;
            }
            $groupRow->$column = Input::get('value');
            $groupRow->save();
            $result['success'] = true;
            $result['notification'] = "Stawka dla pakietu <strong>" . $groupRow->name . "</strong> została zaktualizowana.";
        }else{
            $result['success'] = false;
            $result['msg'] = 'Pole wymagane.';
        }
        return json_encode($result);
    }

    public function getDeletePackage($package_id)
    {
        $package = LeasingAgreementInsuranceGroupRowPackage::find($package_id);

        return View::make('insurances.groups.delete-package', compact('package'));
    }

    public function postDestroyPackage($package_id)
    {
        $package = LeasingAgreementInsuranceGroupRowPackage::find($package_id);

        $package->delete();
        Log::info(Auth::user()->name.' Usunięto pakiet.');

        $result['code'] = 0;
        return json_encode($result);
    }
}
