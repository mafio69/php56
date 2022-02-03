<?php

class InsurancesInfoInsurancesController extends \BaseController {

    /*
     * Edycja danych polisy
     */

    /**
     * InsurancesInfoInsurancesController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:kartoteka_polisy#zarzadzaj');
    }

    public function getEdit($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::find($insurance_id);
        $leasingAgreement = $insurance->leasingAgreement;

        $firstInsurance = $leasingAgreement->insurances()->orderBy('id','desc')->first();
        if($firstInsurance->id == $insurance_id) {
            $isFirstInsurance = true;
            $intervals = $this->calculateDatesIntervals($leasingAgreement, $insurance);
        }else {
            $isFirstInsurance = false;
            $intervals = null;
        }
        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
                                                    ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);

        $paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
        $insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');

        $insurance_company = Insurance_companies::find($insurance->insurance_company_id);
        $if_rounding = ($insurance_company) ? $insurance_company->if_rounding : null;

        return View::make('insurances.manage.card_file.manage.edit.insurance', compact('insurance','leasingAgreement',
                                'insuranceCompanies', 'paymentWays', 'insuranceTypes', 'isFirstInsurance', 'intervals', 'if_rounding'));
    }


    public function postUpdate($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::find($insurance_id);
        $previousInsurance = $insurance->toArray();
        $insurance->update(Input::all());

        $insurance->packages()->detach();
        if(Input::has('package_percentage'))
        {
            foreach(Input::get('package_percentage') as $package)
            {
                $insurance->packages()->attach($package);
            }
        }

        if(Input::has('package_amount'))
        {
            foreach(Input::get('package_amount') as $package)
            {
                $insurance->packages()->attach($package);
            }
        }


        $leasingAgreement = $insurance->leasingAgreement;
        $previousAgreement = $leasingAgreement->toArray();

        if($insurance->active == 1) {
            $leasingAgreement->update(Input::all());
        }

        $historyType = 6;
        $history_id = Histories::leasingAgreementHistory($insurance->leasing_agreement_id, $historyType, Auth::user()->id);
        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $leasingAgreement->toArray()
                ],
                'insurance' => [
                    'previous' => $previousInsurance,
                    'current' => $insurance->toArray()
                ]
            ], $history_id, $leasingAgreement->id);

        Flash::success("Dane polisy zostały zaktualizowane");
        return Redirect::to(URL::to('insurances/info/show', [$insurance->leasing_agreement_id]).'#insurances-data');
    }

    public function getEditYacht($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::with('coverages', 'coverages.type', 'payments')->find($insurance_id);
        $leasingAgreement = $insurance->leasingAgreement;

        $coverages = array();
        foreach($insurance->coverages as $coverage)
        {
            $coverages[$coverage->leasing_agreement_insurance_coverage_type_id] = $coverage;
        }

        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
            ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);

        $paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
        $insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');

        $installments = LeasingAgreementInsuranceInstallment::lists('installments', 'id');

        return View::make('insurances.manage.card_file-yacht.manage.edit.insurance', compact('insurance','leasingAgreement',
            'insuranceCompanies', 'paymentWays', 'insuranceTypes', 'installments', 'coverages'));
    }

    public function postUpdateYacht($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::with('payments', 'coverages')->find($insurance_id);
        $previousInsurance = $insurance->toArray();

        $inputs = Input::all();
        $validator = Validator::make($inputs ,
            array(
                'insurance_company_id' => 'required|numeric|min:1'
            )
        );

        if($validator -> fails()){
            Flash::error('Wystąpił błąd w trakcie wprowadzania zmian w polisie. Skontaktuj się z administratorem.');
            Log::error('błąd przy wprowadzaniu polisy', $inputs);

            return Redirect::back()->withInput();
        }

        $inputs = Input::all();
        if($inputs['insurance_date'] == '') $inputs['insurance_date'] = null;

        $leasingAgreement = $insurance->leasingAgreement;
        $previousAgreement = $leasingAgreement->toArray();
        $leasingAgreement->update($inputs);
        $insurance->update($inputs);

        $insurance->payments()->delete();
        $insurance->coverages()->delete();

        if(isset($inputs['date_payment_deadline'])){
            foreach($inputs['date_payment_deadline'] as $k => $date_payment_deadline)
                {
                    LeasingAgreementInsurancePayment::create([
                        'leasing_agreement_insurance_id' => $insurance->id,
                        'deadline' => $date_payment_deadline,
                        'amount'	=> $inputs['date_payment_amount'][$k],
                        'date_of_payment' => (isset($inputs['paid']) && isset($inputs['paid'][$k]) && $inputs['date_of_payment'][$k] != '') ? $inputs['date_of_payment'][$k] : null
                ]);
            }
        }

        if(isset($inputs['coverages']))
        {
            foreach($inputs['coverages'] as $k => $coverage_id)
            {
                switch ($coverage_id) {
                    case 1:
                        $amount = $inputs['oc_insurance'];
                        $currency_id = $inputs['oc_currency'];
                        $net_gross = $inputs['oc_net_gross'];
                        break;
                    case 2:
                        $amount = $inputs['ac_insurance'];
                        $currency_id = $inputs['ac_currency'];
                        $net_gross = $inputs['ac_net_gross'];
                        break;
                    case 3:
                        $amount = $inputs['nnw_insurance'];
                        $currency_id = $inputs['nnw_currency'];
                        $net_gross = $inputs['nnw_net_gross'];
                        break;
                    case 4:
                        $amount = $inputs['crew_insurance'];
                        $currency_id = $inputs['crew_currency'];
                        $net_gross = $inputs['crew_net_gross'];
                        break;
                    default :
                        $amount = null;
                        $currency_id = null;
                        $net_gross = null;
                        break;
                }
                LeasingAgreementInsuranceCoverage::create([
                    'leasing_agreement_insurance_id' => $insurance->id,
                    'leasing_agreement_insurance_coverage_type_id' => $coverage_id,
                    'amount' => $amount,
                    'currency_id' => $currency_id,
                    'net_gross' => $net_gross
                ]);
            }
        }

        $historyType = 6;
        $history_id = Histories::leasingAgreementHistory($insurance->leasing_agreement_id, $historyType, Auth::user()->id);
        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $leasingAgreement->toArray()
                ],
                'insurance' => [
                    'previous' => $previousInsurance,
                    'current' => $insurance->toArray()
                ]
            ], $history_id, $leasingAgreement->id);


        Flash::success("Dane polisy zostały zaktualizowane");
        return Redirect::to(URL::to('insurances/info/show', [$insurance->leasing_agreement_id]).'#insurances-data');
    }

    /*
     * Dodawanie polisy do umowy
     */

    public function getCreate($agreement_id)
    {
        $leasingAgreement = LeasingAgreement::find($agreement_id);

        $insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');
        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
            ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);
        $paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');

        $lastInsurance = $leasingAgreement->insurances()->orderBy('created_at', 'desc')->first();

        return View::make('insurances.manage.card_file.manage.create.insurance', compact('lastInsurance', 'leasingAgreement', 'insuranceTypes', 'insuranceCompanies', 'paymentWays'));
    }

    public function postStore($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);
        $last_insurance = $agreement->insurances->last();

        $last_insurance->active = 0;
        $last_insurance->save();

        $insurance = LeasingAgreementInsurance::create(Input::all());

        if(Input::has('client_id')){
            $last_client = $agreement->client;
            $agreement->cessions()->save($last_client, array('leasing_agreement_insurance_id' => $insurance->id));

            $agreement->client_id = Input::get('client_id');
            $agreement->save();
        }

        Histories::leasingAgreementHistory($insurance->leasing_agreement_id, 5, Auth::user()->id);

        Flash::success("Dodano polisę do umowy leasingowej");
        return Redirect::to(URL::to('insurances/info/show', [$agreement_id]).'#insurances-data');
    }

    /*
     * Wyszukiwanie klientów do cesji
     */

    public function postSearchClient()
    {
        \Debugbar::disable();
        $column = Input::get('col_name');
        $column = substr($column, 7);
        $term = Input::get('term');


        $result = array();

        if($term != '') {
            $clients = DB::select(DB::raw('
                SELECT * FROM clients c WHERE
                    c.'.$column.' LIKE "%' . $term . '%" AND
                    (
                        (SELECT count(*) from clients c_2 WHERE c_2.parent_id = c.id) = 0
                        OR
                        (SELECT count(*) from clients c_3 WHERE c_3.parent_id = c.id AND '.$column.' NOT LIKE "%' . $term . '%") >= 1
                    )
            '));

            foreach ($clients as $k => $client) {
                $result[] = array(
                    "id" => $client->id,
                    "label" => $client->$column,
                    "value" => $client->$column
                );
            }
        }



        return json_encode($result);
    }

    /*
     * Pobranie informacji o kliencie
     */

    public function getClientInfo()
    {
        \Debugbar::disable();
        $client_id = Input::get('client_id');
        $client = Clients::find($client_id);

        return json_encode($client->toArray());
    }

    /*
     * Cesja
     */
    public function getCession($agreement_id)
    {
        $leasingAgreement = LeasingAgreement::find($agreement_id);

        $insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');
        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
            ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);
        $paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');

        $lastInsurance = $leasingAgreement->insurances()->orderBy('created_at', 'desc')->first();

        $contribution = $this->calculateContribution($agreement_id, 12);

        return View::make('insurances.manage.card_file.manage.create.cession', compact('lastInsurance', 'leasingAgreement', 'insuranceTypes', 'insuranceCompanies', 'paymentWays', 'contribution'));
    }

    public function postStoreCession($agreement_id)
    {
        $inputs = Input::all();

        $agreement = LeasingAgreement::find($agreement_id);
        $previousAgreement = $agreement->toArray();
        $last_insurance = $agreement->insurances->last();

        if($agreement->client_id == $inputs['client_id'])
        {
            Flash::error("Próba wykonania cesji na tym samym leasingobiorcu.");
            return Redirect::back()->withInput($inputs);
        }
        $newInsuranceData = $last_insurance->toArray();

        $last_insurance->active = 0;
        $last_insurance->save();

        $newInsuranceData['notification_number'] = Auth::user()->insurances_global_nr;
        $newInsuranceData['user_id'] = Auth::user()->id;
        $newInsuranceData['if_cession'] = 1;
        $newInsuranceData['active'] = 1;
        $insurance = LeasingAgreementInsurance::create($newInsuranceData);

        $last_client = $agreement->client;
        $agreement->cessions()->save($last_client, array('leasing_agreement_insurance_id' => $insurance->id, 'current_client_id' => $inputs['client_id']));

        $agreement->nr_contract = Input::get('nr_contract');
        $agreement->client_id = Input::get('client_id');
        $agreement->save();

        $historyType = 13;
        $history_id = Histories::leasingAgreementHistory($insurance->leasing_agreement_id, $historyType, Auth::user()->id);

        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $agreement->toArray()
                ]
            ], $history_id, $agreement->id);

        Flash::success("Dodano cesji do umowy leasingowej");
        return Redirect::to(URL::to('insurances/info/show', [$agreement_id]).'#insurances-data');
    }

    public function calculateContribution($leasingAgreement_id, $months)
    {
        $leasingAgreement = LeasingAgreement::find($leasingAgreement_id);
        $objects = $leasingAgreement->objects;

        if($leasingAgreement->leasing_agreement_type_id == 1)
            $gross_net = 'gross_value';
        else
            $gross_net = 'net_value';

        $contribution = 0;
        $rate = 0;

        foreach($objects as $object)
        {
            if(is_null($object->leasing_agreement_insurance_group_row_id))
                return array(
                    'error' => 'uzupełnij grupę ubezpieczenia dla przedmiotu umowy'
                );

            if(is_null($object->$gross_net))
                return array(
                    'error' => 'uzupełnij wartość przedmiotu umowy'
                );

            $local_rate = ($object->insurance_group_row()->first()->months_12 / 12) * $months;

            $contribution += ($local_rate/100)*$object->$gross_net;
            $rate += $local_rate;
        }

        $rate = $rate/count($objects);

        return array(
            'contribution' => number_format((float)$contribution, 2, '.', ''),
            'rate' => number_format((float)$rate, 2, '.', '')
        );
    }

    public function checkMonth()
    {
        $insuranceType = LeasingAgreementInsuranceType::find(Input::get('leasing_agreement_insurance_type_id'));

        return $insuranceType->months;
    }

    public function postRollbackCession($agreement_id)
    {
        $leasingAgreement = LeasingAgreement::find($agreement_id);
        $previousAgreement = $leasingAgreement->toArray();
        $cession = $leasingAgreement->cessions()->get()->last();

        $lastInsurance = $leasingAgreement->insurances()->get()->last();
        $lastInsurance_id = $lastInsurance->id;
        $lastInsurance->delete();

        $lastInsurance = $leasingAgreement->insurances()->get()->last();
        $lastInsurance->active = 1;
        $lastInsurance->save();

        $leasingAgreement->client_id = $cession->pivot->client_id;
        $leasingAgreement->save();

        DB::table('leasing_agreement_cessions')
            ->where('leasing_agreement_id', $agreement_id)
            ->where('leasing_agreement_insurance_id', $lastInsurance_id)
            ->update(array('deleted_at' => DB::raw('NOW()')));

        $historyType = 14;
        $history_id = Histories::leasingAgreementHistory($agreement_id, $historyType, Auth::user()->id);

        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $leasingAgreement->toArray()
                ]
            ], $history_id, $agreement_id);

        Flash::success("Cesja została wycofana.");

        $result['code'] = 0;
        return json_encode($result);
    }

    public function postRollbackInsurance($insurance_id)
    {
        $lastInsurance = LeasingAgreementInsurance::findOrFail($insurance_id);

        $leasingAgreement = LeasingAgreement::find($lastInsurance->leasing_agreement_id);
        $previousAgreement = $leasingAgreement->toArray();

        $lastInsurance->delete();

        $lastInsurance = $leasingAgreement->insurances()->get()->last();
        if($lastInsurance) {
            $lastInsurance->active = 1;
            $lastInsurance->save();
        }

        $historyType = 15;
        $history_id = Histories::leasingAgreementHistory($leasingAgreement->id, $historyType, Auth::user()->id);

        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $leasingAgreement->toArray()
                ]
            ], $history_id, $leasingAgreement->id);

        Flash::success("Polisa została wycofana.");

        $result['code'] = 0;
        return json_encode($result);
    }

    public function postRollbackInsuranceYacht($insurance_id)
    {
        $rollbackInsurance = LeasingAgreementInsurance::findOrFail($insurance_id);

        if($rollbackInsurance->resumedInsurance)
        {
            $resumedInsurance = $rollbackInsurance->resumedInsurance;
            $resumedInsurance->active = 1;
            $resumedInsurance->save();
        }

        $rollbackInsurance->delete();

        $leasingAgreement = LeasingAgreement::findOrFail($rollbackInsurance->leasing_agreement_id);
        $previousAgreement = $leasingAgreement->toArray();

        $historyType = 15;
        $history_id = Histories::leasingAgreementHistory($rollbackInsurance->leasing_agreement_id, $historyType, Auth::user()->id);

        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $leasingAgreement->toArray()
                ]
            ], $history_id, $rollbackInsurance->leasing_agreement_id);

        Flash::success("Polisa została wycofana.");

        $result['code'] = 0;
        return json_encode($result);
    }

    private function calculateDatesIntervals($leasingAgreement, $insurance)
    {
        if($leasingAgreement->insurance_from)
            $from = Date::createFromFormat('Y-m-d', $leasingAgreement->insurance_from);
        else
            $from = Date::createFromFormat('Y-m-d', $insurance->date_from);

        if($leasingAgreement->insurance_to)
            $to = Date::createFromFormat('Y-m-d',$leasingAgreement->insurance_to);
        else
            $to = Date::createFromFormat('Y-m-d', $insurance->date_to);


        $months_diff = $from->diffInMonths($to)+1;
        if($months_diff > 12)
            $insurance_type_id = LeasingAgreementInsuranceType::whereNull('months')->first();
        else
            $insurance_type_id = LeasingAgreementInsuranceType::where('months', $months_diff)->first();

        if(! $insurance_type_id )
        {
            $insurance_type_id = LeasingAgreementInsuranceType::create([
                'name' => $months_diff. 'msc',
                'months' => $months_diff
            ]);
        }

        $insurance_type_id = $insurance_type_id->id;

        return ['months' => $months_diff, 'insurance_type_id' => $insurance_type_id];
    }

    public function postSetPayment()
    {
        $payment_id = Input::get('payment_id');
        $rules = [
            'date' => 'required|date'
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return json_encode(['status' => 'error', 'msg' => 'proszę podać prawidłową datę napłynięcia zapłaty']);
        }

        $payment = LeasingAgreementInsurancePayment::find($payment_id);
        $payment->date_of_payment = Input::get('date');
        $payment->save();

        Histories::leasingAgreementHistory(Input::get('agreement_id'), 19);

        return json_encode(['status' => 'success', 'msg' => 'zmiany zostały zapisane']);

    }

    public function postAttachToResume($insurance_id)
    {
        if(! Input::has('insurance_to_attach'))
        {
            return json_encode(['code' => '3', 'error' => 'Proszę wybrać prawidłową polisę do podpięcia.']);
        }
        $insurance = LeasingAgreementInsurance::findOrFail($insurance_id);
        $insurance_to_attach_id = Input::get('insurance_to_attach');

        $insurance_to_attach = LeasingAgreementInsurance::findOrFail($insurance_to_attach_id);
        $insurance_to_attach->resumed_insurance_id = $insurance_id;
        $insurance_to_attach->save();

        $insurance->active = 0;

        return json_encode(['code' => 0]);
    }

    public function postActivateInsurance($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::findOrFail($insurance_id);
        $insurance->active = 1;
        $insurance->save();

        Histories::leasingAgreementHistory(Input::get('agreement_id'), 23);

        return json_encode(['code' => 0]);
    }

    public function getMarkContributionAsPaid($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::find($insurance_id);
        return View::make('insurances.manage.card_file.dialog.mark-contribution-as-paid', compact('insurance'));

    }

    public function postMarkContributionAsPaidInsurance($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::findOrFail($insurance_id);
        $insurance->if_contribution_paid = !$insurance->if_contribution_paid;
        $insurance->save();

        $result['code'] = 0;
        return json_encode($result);
    }
}
