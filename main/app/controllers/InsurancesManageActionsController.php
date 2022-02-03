<?php

class InsurancesManageActionsController extends \BaseController {

	/**
	 * InsurancesManageActionsController constructor.
	 */
	public function __construct()
	{
        $this->beforeFilter('permitted:wykaz_polis#zarzadzaj');
	}

	/*
	 * wznowienie umowy
	 */
	public function getResume($agreement_id, $leasing_agreement_insurance_type_id)
	{
		$leasingAgreement = LeasingAgreement::find($agreement_id);

		$insuranceType = LeasingAgreementInsuranceType::find($leasing_agreement_insurance_type_id);

		$existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
		$insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
		$insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
		ksort($insuranceCompanies);

		$lastInsurance = $leasingAgreement->insurances()->active()->first();
		if(!is_null($lastInsurance)){
			$insurance_company_id = $lastInsurance->insurance_company_id;

			$groups = $this->postMatchInsuranceGroup($leasingAgreement->id, $insurance_company_id);

      	  $insurance_company = Insurance_companies::find($insurance_company_id);
			if($insurance_company)
        		$if_rounding = $insurance_company->if_rounding;
			else
				$if_rounding = 0;

       		$paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
			$contribution = $this->calculateContribution($leasingAgreement, $insuranceType->months, $if_rounding);
			$dates = $this->calculateDates($lastInsurance, $insuranceType->months);
		}
		if($leasing_agreement_insurance_type_id == 14){
			$monthsRange = array_combine(range(1,120), range(1,120));
			$dates = $this->calculateDates($lastInsurance, 1);
		}
		return View::make('insurances.manage.actions.resume', compact('leasingAgreement', 'insuranceType', 'insuranceCompanies',
																	'paymentWays', 'lastInsurance', 'contribution', 'dates', 'groups', 'if_rounding', 'monthsRange'));
	}

	public function postStoreResume($agreement_id)
	{
		$inputs = Input::all();
		$validator = Validator::make($inputs ,
			array(
				'group_id' => (isset($inputs['if_foreign_policy'])) ? 'numeric' : 'required|numeric|min:1',
				'leasing_agreement_insurance_group_row_id' => (isset($inputs['if_foreign_policy'])) ? 'numeric' : 'required|numeric|min:1',
				'insurance_company_id' => 'required|numeric|min:1'
			)
		);

		if($validator -> fails()){
			Flash::error('Wystąpił błąd w trakcie wprowadzania wznowienia. Skontaktuj się z administratorem.');
			Log::error('błąd przy wznowieniu', $inputs);

			return Redirect::back()->withInput();
		}

		$agreement = LeasingAgreement::find($agreement_id);

		$last_insurance = $agreement->insurances->last();

		$last_insurance->active = 0;
		$last_insurance->save();

		$inputs = Input::all();
		$inputs['notification_number'] = Auth::user()->insurances_global_nr;
		$insurance = LeasingAgreementInsurance::create($inputs);

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

		$inputs['reported_to_resume'] = null;
		$agreement->update($inputs);
		$agreement->save();

		Histories::leasingAgreementHistory($agreement_id, 9);

		Flash::success("Dodano wznowienie do umowy nr ".$agreement->nr_contract);
		return Redirect::to('insurances/manage/resume');
	}

	public function getResumeYacht($leasing_agreement_id)
	{
		$leasingAgreement = LeasingAgreement::with('insurances', 'insurances.coverages', 'insurances.coverages.type')->findOrFail($leasing_agreement_id);
		$coveragesTypes = LeasingAgreementInsuranceCoverageType::lists('name', 'id');

		return View::make('insurances.manage.actions.resume-yacht', compact('leasingAgreement', 'coveragesTypes'));
	}

	public function getResumeYachtContent()
	{
		$insurance = LeasingAgreementInsurance::with('coverages')->findOrFail(Input::get('insurance_id'));
		$leasingAgreement = LeasingAgreement::with('insurances', 'insurances.coverages', 'insurances.coverages.type')->find($insurance->leasing_agreement_id);

        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
            ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);

		$paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
		$insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');

		$installments = LeasingAgreementInsuranceInstallment::lists('installments', 'id');

		$coveragesTypes = LeasingAgreementInsuranceCoverageType::lists('name', 'id');

		return View::make('insurances.manage.actions.resume-yacht-content', compact('leasingAgreement', 'insuranceCompanies', 'paymentWays', 'insuranceTypes', 'installments', 'coveragesTypes', 'insurance'));
	}

	public function postStoreResumeYacht()
	{
		$agreement_id = Input::get('leasing_agreement_id');
		$resuming_insurance_id = Input::get('leasing_agreement_insurance_id');

		$inputs = Input::all();
		$inputs['resumed_insurance_id'] = $resuming_insurance_id;

		$validator = Validator::make($inputs ,
			array(
				'insurance_company_id' => 'required|numeric|min:1'
			)
		);

        if ($validator->fails()) {
            Flash::error('Wystąpił błąd w trakcie wprowadzania polisy. Proszę wybrać ubezpieczyciela.');
            Log::error('błąd przy wprowadzaniu polisy', $inputs);

            return Redirect::back()->withInput();
        }

        $resuming_insurance = LeasingAgreementInsurance::findOrFail($resuming_insurance_id);
        $resuming_insurance->active = 0;
        $resuming_insurance->save();

        $agreement = LeasingAgreement::find($agreement_id);

        $agreement->update($inputs);

        $inputs['notification_number'] = Auth::user()->insurances_global_nr;
        if ($inputs['insurance_date'] == '') $inputs['insurance_date'] = null;
        $leasingAgreementInsurance = LeasingAgreementInsurance::create($inputs);


//		foreach($inputs['date_payment_deadline'] as $k => $date_payment_deadline)
//		{
//			LeasingAgreementInsurancePayment::create([
//					'leasing_agreement_insurance_id' => $leasingAgreementInsurance->id,
//					'deadline' => $date_payment_deadline,
//					'amount'	=> $inputs['date_payment_amount'][$k],
//					'date_of_payment' => (isset($inputs['paid']) && isset($inputs['paid'][$k]) && $inputs['date_of_payment'][$k] != '') ? $inputs['date_of_payment'][$k] : null
//			]);
//		}

        if (isset($inputs['coverages'])) {
            foreach ($inputs['coverages'] as $k => $coverage_id) {
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
						'leasing_agreement_insurance_id' => $leasingAgreementInsurance->id,
						'leasing_agreement_insurance_coverage_type_id' => $coverage_id,
						'amount' => $amount,
						'currency_id' => $currency_id,
						'net_gross' => $net_gross
				]);
			}
		}
		Histories::leasingAgreementHistory($agreement_id, 9);



		Flash::success("Dodano wznowienie do umowy nr ".$agreement->nr_contract);
		return Redirect::to(url('insurances/info/show', [$agreement->id]));
	}

	/*
	 * Obliczanie składki i stawki dla nowej umowy
	 */
	private function calculateContribution($leasingAgreement, $months, $if_rounding)
	{
		if($leasingAgreement->net_gross == 2)
			$gross_net = 'loan_gross_value';
		else
			$gross_net = 'loan_net_value';
		$leasingAgreement->load('insurance_group_row');
		if(is_null($leasingAgreement->leasing_agreement_insurance_group_row_id) || ! $leasingAgreement->insurance_group_row)
			return array(
				'error' => 'uzupełnij grupę ubezpieczenia dla umowy'
			);

		if(is_null($leasingAgreement->$gross_net))
			return array(
				'error' => 'uzupełnij wartość przedmiotów umowy'
			);

		$rate = $leasingAgreement->insurance_group_row()->first()->months_12 ;
		$rateToCalc = ($rate / 12) * $months;

		$contribution = ($rateToCalc/100)*$leasingAgreement->$gross_net ;
        if($if_rounding == 1)
            $contribution = round( $contribution );

		$rate_lessor = $rate;
		$contribution_lessor = $contribution;

		return array(
			'contribution' => number_format((float)$contribution, 2, '.', ''),
			'rate' => number_format((float)$rate, 2, '.', ''),
			'contribution_lessor' => number_format((float)$contribution_lessor, 2, '.', ''),
			'rate_lessor' => number_format((float)$rate_lessor, 2, '.', '')
		);
	}

	public function postCalculateNewContribution($leasing_agreement_id)
	{
		if(Input::get('leasing_agreement_insurance_group_row_id') == 0)
		{
			return json_encode(array(
				'error' => 'nie zdefiniowano obowiązujących grup stawek'
			));
		}

		$leasingAgreement = LeasingAgreement::find($leasing_agreement_id);

		if($leasingAgreement->net_gross == 2)
			$gross_net = 'loan_gross_value';
		else
			$gross_net = 'loan_net_value';

		if(is_null($leasingAgreement->$gross_net))
			return json_encode(array(
				'error' => 'uzupełnij wartość przedmiotów umowy'
			));
		$group_rate = LeasingAgreementInsuranceGroupRow::with('rate')->find(Input::get('leasing_agreement_insurance_group_row_id'));

		$months = Input::get('months');

		switch ($months){
			case ($months <= 12):
				$group_rate_column = 'months_12';
				$group_months = 12;
				break;
			case ($months <= 24):
				$group_rate_column = 'months_24';
                $group_months = 24;
				break;
			case ($months <= 36):
				$group_rate_column = 'months_36';
                $group_months = 36;
				break;
			case ($months <= 48):
				$group_rate_column = 'months_48';
                $group_months = 48;
				break;
			case ($months <= 60):
				$group_rate_column = 'months_60';
                $group_months = 60;
				break;
			case ($months <= 72):
				$group_rate_column = 'months_72';
				$group_months = 72;
				break;
			case ($months <= 84):
				$group_rate_column = 'months_84';
				$group_months = 84;
				break;
			case ($months <= 96):
				$group_rate_column = 'months_96';
				$group_months = 96;
				break;
			case ($months <= 108):
				$group_rate_column = 'months_108';
				$group_months = 108;
				break;
			case ($months <= 120):
				$group_rate_column = 'months_120';
				$group_months = 120;
				break;
			default:
				$group_rate_column = 'months_72';
				$group_months = 72;
				break;
		}
		if(Input::get('multi_month') == 1){ // "Stawka leasingodawcy" -  powinna być przeliczana wyłącznie na podstawie stawki na 12 m-cy (wielomiesięczne)
			$group_rate_column = 'months_12';
			$group_months = 12;
		}

		if(Input::get('if_full_year') == 'true')
			$if_full_year = true;
		else
			$if_full_year = false;

		$rate = $group_rate->$group_rate_column ;
		if(! $if_full_year)
			$rate = ($rate / $group_months) * $months;

        $contribution = ($rate / 100) * $leasingAgreement->$gross_net;

        if(Input::get('if_rounding') == 'true')
            $contribution = round($contribution);

		if(Input::has('leasing_agreement_payment_way_id') && Input::get('leasing_agreement_payment_way_id') == 2)
		{
			$rate_lessor = $rate;
			$contribution_lessor = $contribution;
		}else {
			if($months >= 12)
				$rate_lessor = $group_rate->months_12;
			else
				$rate_lessor = $rate;

			$contribution_lessor = ($rate_lessor / 100) * $leasingAgreement->$gross_net;
            if(Input::get('if_rounding') == 'true')
                $contribution_lessor = round($contribution_lessor);
		}
		if(Input::get('multi_month') == 1){
			$rate_lessor = $rate;
			$contribution_lessor = $contribution;
		}

		if(!$if_full_year && $months != $group_months && $months > 12)
		{
			$months_diff = 12-($group_months-$months);
			$last_year_lessor_contribution = ($contribution_lessor/12) * $months_diff ;
            if(Input::get('if_rounding') == 'true')
                $last_year_lessor_contribution = round($last_year_lessor_contribution);
		}else {
			$last_year_lessor_contribution = 0;
		}

		$packagesView = '';
		if($group_rate->packages->count() > 0)
		{
			if(Input::has('insurance_id'))
			{
				$insurance = LeasingAgreementInsurance::with('packages')->find(Input::get('insurance_id'));
			}else{
				$insurance = null;
			}
			$percentage_col = 'months_'.$months.'_percentage';
			$amount_col = 'months_'.$months.'_amount';

			$packagesView = View::make('insurances.manage.actions.packages', compact('percentage_col', 'amount_col', 'group_rate', 'insurance'))->render();
		}

		$if_minimal = [];
		if($group_rate -> if_minimal == 1) {
			$minimal_col = 'minimal_' . $group_months;
			$minimal_amount = $group_rate->$minimal_col;

			if ($contribution < $minimal_amount) {
				$contribution = $minimal_amount;
				$if_minimal['contribution'] = 1;
			}

			if ($contribution_lessor < $minimal_amount) {
				$contribution_lessor = $minimal_amount;
				$if_minimal['contribution_lessor'] = 1;
			}
		}

		$deductible_info = false;

		if($group_rate->rate){
			if($group_rate->rate->deductible_value){
				if($leasingAgreement->$gross_net<$group_rate->rate->deductible_value)
					$deductible_info = true;
			}
			elseif($group_rate->rate->deductible_percent){
				//warunek symboliczny dla wartości <100%
				if($leasingAgreement->$gross_net<($group_rate->rate->deductible_percent*$leasingAgreement->$gross_net)/100)
					$deductible_info = true;
			}
		}

		return json_encode(array(
			'contribution' => number_format((float)$contribution, 2, '.', ''),
			'rate' => number_format((float)$rate, 2, '.', ''),
			'contribution_lessor' => number_format((float)$contribution_lessor, 2, '.', ''),
			'rate_lessor' => number_format((float)$rate_lessor, 2, '.', ''),
			'last_year_lessor_contribution' => number_format((float)$last_year_lessor_contribution, 2, '.', ''),
			'packages'	=> $packagesView,
			'if_minimal' => $if_minimal,
			'deductible_info' => $deductible_info,
			'commission' => $group_rate->commission
		));
	}

	/*
	 * aktualizacja globalnego numeru
	 */

	public function postUpdateGlobalNr()
	{
		if(Input::has('value') && Input::get('value') != '') {

			/*
			$new_nr = Date::createFromFormat('m/Y', Input::get('value'));
			$last_nr = Date::createFromFormat('m/Y', Settings::get('insurances_global_nr'));
			if($new_nr->diffInMonths($last_nr, false) > 0)
			{
				$result['success'] = false;
				$result['msg'] = 'Numery zgłoszeń muszą narastać.';
			}else {
				Settings::set('insurances_global_nr', Input::get('value'));
			*/
			Auth::user()->insurances_global_nr = Input::get('value');
			Auth::user()->save();

			$result['success'] = true;
			$result['notification'] = "Globalny numer zgłoszenia został zaktualizowany";
			//}
		}else{
			$result['success'] = false;
			$result['msg'] = 'Pole wymagane.';
		}
		return json_encode($result);
	}

	/*
	 * Obliczanie dat obowiązywania nowej polisy
	 */
	private function calculateDates($lastInsurance, $months)
	{
		if( is_null($lastInsurance->date_to) || $lastInsurance->date_to == '0000-00-00' ) {
			$dates['from'] = date('Y-m-d');
			$dates['to'] = Date::now()->addMonths($months)->toDateString();
		}else {
			$dates['from'] = Date::createFromFormat('Y-m-d', $lastInsurance->date_to)->addDay()->toDateString();
			$dates['to'] = Date::createFromFormat('Y-m-d', $lastInsurance->date_to)->addMonths($months)->toDateString();
		}

		return $dates;
	}

	/*
	 * Obliczanie daty obowiązywania nowej polisy
	 */
	public function postCalculateDateTo()
	{
		$date_to = Date::createFromFormat('Y-m-d', Input::get('date_from'))->addMonths(Input::get('months'))->subDay()->toDateString();

		return json_encode(['date_to' => $date_to]);
	}

	/*
     * Dopasuj grupę stawek
     */
	public function postMatchInsuranceGroup($agreement_id, $insurance_company_id)
	{
		$insurance_company = Insurance_companies::find($insurance_company_id);
		$agreement = LeasingAgreement::find($agreement_id);
		$currentRate = $agreement->insurance_group_row()->first();
		if($currentRate)
			$currentRate = $agreement->insurance_group_row()->first()->leasing_agreement_insurance_group_rate_id;
		else
			$currentRate = null;

		$matchedGroup = LeasingAgreementInsuranceGroup::where('insurance_company_id',$insurance_company_id)->whereNotNull('valid_from')->whereNull('valid_to')->has('rows', '>', 0)->get()->last();
		if($matchedGroup)
		{
			$returnGroup['status'] = 'success';
			$returnGroup['group'] = $matchedGroup->id;
			$returnGroup['rates'] = $matchedGroup->rows()->get()->lists('rate_name', 'id');
			$returnGroup['rates'][0] = '---wybierz stawkę---';
			ksort($returnGroup['rates']);
			if($currentRate && $matchedGroup->rows()->where('leasing_agreement_insurance_group_rate_id', $currentRate)->first()){
				$returnGroup['currentRate'] = $matchedGroup->rows()->where('leasing_agreement_insurance_group_rate_id', $currentRate)->first()->id;
			}
		}else{
			$returnGroup['status'] = 'error';
			$returnGroup['msg'] = 'nie zdefiniowano obowiązujących grup stawek';
			$returnGroup['group'] = 0;
			$returnGroup['rates'] = ['---brak zdefiniowanych stawek---'];
		}

		$groups = LeasingAgreementInsuranceGroup::where('insurance_company_id',$insurance_company_id)->whereNotNull('valid_from')->has('rows', '>', 0)->orderBy('id', 'desc')->get();
		if(!$groups->isEmpty())
			$returnGroup['groups'] = $groups->lists('group_name', 'id');
		else
			$returnGroup['groups'] = ['---brak zdefiniowanych stawek---'];


		if(!$currentRate){
			$returnGroup['currentRate'] = null;
		}

		$returnGroup['if_rounding'] = ($insurance_company) ? $insurance_company->if_rounding : null;
		$returnGroup['if_full_year'] = ($insurance_company) ? $insurance_company->if_full_year : null;
		return $returnGroup;
	}

	public function postChangeInsurancesGroup()
	{
		$currentRate = LeasingAgreementInsuranceGroupRow::find(Input::get('current_rate_id'));

		$group = LeasingAgreementInsuranceGroup::find(Input::get('group_id'));


		$rates = $group->rows()->get()->toArray();
		if($group->rows) {
			$current_id = $group->rows()->where('leasing_agreement_insurance_group_rate_id', $currentRate->leasing_agreement_insurance_group_rate_id)->first();
			if ($current_id)
				$current_id = $current_id->id;
			else
				$current_id = '-1';
		}else{
			$current_id = '-1';
		}

		return json_encode(['current_id' => $current_id, 'rates' => $rates]);
	}

	public function getAssign($agreement_id)
	{
		$leasingAgreement = LeasingAgreement::find($agreement_id);

		$intervals = $this->calculateDatesIntervals($leasingAgreement);

		$existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
		$insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
		$insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
		ksort($insuranceCompanies);

		$paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
		$insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');

		$mismatchingReasons = LeasingAgreementMismatchingReason::lists('name', 'id');
		$mismatchingReasons = array_merge(['0' => '--- wskaż przyczynę ---'], $mismatchingReasons);

		return View::make('insurances.manage.actions.assign', compact('leasingAgreement', 'insuranceCompanies', 'paymentWays', 'insuranceTypes', 'intervals', 'mismatchingReasons'));
	}

	public function postStoreAssign($agreement_id)
	{
		$inputs = Input::all();

		$validator = Validator::make($inputs ,
			array(
				'group_id' => (isset($inputs['if_foreign_policy'])) ? 'numeric' : 'required|numeric|min:1',
				'leasing_agreement_insurance_group_row_id' => (isset($inputs['if_foreign_policy'])) ? 'numeric' : 'required|numeric|min:1',
				'insurance_company_id' => 'required|numeric|min:1'
			)
		);
		if($validator -> fails()){
			Flash::error('Wystąpił błąd w trakcie wprowadzania polisy. Skontaktuj się z administratorem.');
			Log::error('błąd przy wprowadzaniu polisy', $inputs);

			return Redirect::back()->withInput();
		}

		$agreement = LeasingAgreement::find($agreement_id);

		$inputs = Input::all();

		$agreement->update($inputs);
		$agreement->save();

		$inputs['notification_number'] = Auth::user()->insurances_global_nr;

		if(Input::has('leasing_agreement_mismatching_reason_id') && $inputs['leasing_agreement_mismatching_reason_id'] == 0) $inputs['leasing_agreement_mismatching_reason_id'] = null;

		$insurance = LeasingAgreementInsurance::create($inputs);

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

		Histories::leasingAgreementHistory($agreement_id, 5);

		Flash::success("Dodano polisę do umowy nr ".$agreement->nr_contract);
		return Redirect::to('insurances/manage/index');
	}

	public function getAssignToYacht($agreement_id)
	{
		$leasingAgreement = LeasingAgreement::find($agreement_id);

        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
            ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);

		$paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
		$insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');

		$installments = LeasingAgreementInsuranceInstallment::lists('installments', 'id');

		return View::make('insurances.manage.actions.assign-to-yacht', compact('leasingAgreement', 'insuranceCompanies', 'paymentWays', 'insuranceTypes', 'installments'));
	}

	public function postStoreAssignToYacht($agreement_id)
	{
		$inputs = Input::all();
		$validator = Validator::make($inputs ,
			array(
				'insurance_company_id' => 'required|numeric|min:1'
			)
		);

		if($validator -> fails()){
			Flash::error('Wystąpił błąd w trakcie wprowadzania polisy. Skontaktuj się z administratorem.');
			Log::error('błąd przy wprowadzaniu polisy', $inputs);

			return Redirect::back()->withInput();
		}

		$agreement = LeasingAgreement::find($agreement_id);

		$inputs = Input::all();

		$agreement->update($inputs);
		$agreement->save();

		$inputs['notification_number'] = Auth::user()->insurances_global_nr;
		if($inputs['insurance_date'] == '') $inputs['insurance_date'] = null;
		$leasingAgreementInsurance = LeasingAgreementInsurance::create($inputs);

		foreach($inputs['date_payment_deadline'] as $k => $date_payment_deadline)
		{
			LeasingAgreementInsurancePayment::create([
				'leasing_agreement_insurance_id' => $leasingAgreementInsurance->id,
				'deadline' => $date_payment_deadline,
				'amount'	=> $inputs['date_payment_amount'][$k],
				'date_of_payment' => (isset($inputs['paid']) && isset($inputs['paid'][$k]) && $inputs['date_of_payment'][$k] != '') ? $inputs['date_of_payment'][$k] : null
			]);
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
					'leasing_agreement_insurance_id' => $leasingAgreementInsurance->id,
					'leasing_agreement_insurance_coverage_type_id' => $coverage_id,
					'amount' => $amount,
					'currency_id' => $currency_id,
					'net_gross' => $net_gross
				]);
			}
		}

		Histories::leasingAgreementHistory($agreement_id, 5);

		Flash::success("Dodano polisę do umowy nr ".$agreement->nr_contract);
		return Redirect::to(url('insurances/info/show', [$agreement->id]));
	}

	public function postPaymentDeadline()
	{
		$leasing_agreement_payment_way_id = Input::get('leasing_agreement_payment_way_id');
		$leasing_agreement_installment_id = Input::get('leasing_agreement_installment_id');

		if($leasing_agreement_payment_way_id == 2)
			$installments = 1;
		else{
			$installment = LeasingAgreementInsuranceInstallment::find($leasing_agreement_installment_id);
			$installments = $installment->installments;
		}

		$result = null;
		for($i = 1; $i <= $installments; $i++)
		{
			$result .= View::make('insurances.manage.actions.payment-deadline', compact('i'));
		}

		return $result;
	}

	private function calculateDatesIntervals($leasingAgreement)
	{
		if(!is_null($leasingAgreement->insurance_from) && !is_null($leasingAgreement->insurance_to) ){
			$from = Date::createFromFormat('Y-m-d', $leasingAgreement->insurance_from);
			$to = Date::createFromFormat('Y-m-d', $leasingAgreement->insurance_to);

			$months_diff = $from->diffInMonths($to) + 1;

			if ($months_diff > 12)
				$insurance_type = LeasingAgreementInsuranceType::whereNull('months')->first();
			else {
				$insurance_type = LeasingAgreementInsuranceType::where('months', $months_diff)->first();
				if (!$insurance_type) {
					$insurance_type = LeasingAgreementInsuranceType::create(['name' => $months_diff . ' msc', 'months' => $months_diff]);
				}
			}

			$insurance_type_id = $insurance_type->id;

			return ['months' => $months_diff, 'insurance_type_id' => $insurance_type_id];
		}else{
			return ['months' => null, 'insurance_type_id' => null];
		}
	}

	public function getInsuranceTypeMonths(){
		$insurance_type_id = Input::get('insurance_type_id');

		$months = LeasingAgreementInsuranceType::find($insurance_type_id);
		return $months->months;
	}

	public function getInsuranceType(){
		$months = Input::get('months');

		if($months > 12)
			$insurance_type_id = LeasingAgreementInsuranceType::whereNull('months')->first();
		else
			$insurance_type_id = LeasingAgreementInsuranceType::where('months', '<=', $months)->orderBy('months', 'desc')->first();

		return $insurance_type_id->id;
	}

	public function postRecalculateRates($leasing_agreement_id)
	{
		$element = Input::get('element_name');
		$value = Input::get('element_value');

		$currentRate = LeasingAgreementInsuranceGroupRow::find(Input::get('leasing_agreement_insurance_group_row_id'));

		$leasingAgreement = LeasingAgreement::find($leasing_agreement_id);

		if($leasingAgreement->net_gross == 2)
			$gross_net = 'loan_gross_value';
		else
			$gross_net = 'loan_net_value';

		if(is_null($leasingAgreement->$gross_net))
			return json_encode([
				'error' => 'uzupełnij wartość przedmiotów umowy'
			]);

		$gross_net = $leasingAgreement->$gross_net;

		$months = Input::get('months');
		$rate_months = ceil($months/12) * 12;

        $months_diff = 12-($rate_months-$months);

		switch($element){
			case 'rate':
				if($months > 12) {
					$rateToCalc = ($value / $rate_months) * $months;
					$new_value = ($rateToCalc / 100) * $gross_net;
				}else{
					$new_value = $value*$gross_net/100;
				}
                if(Input::get('if_rounding') == 'true')
                    $new_value = round($new_value);

				$new_element = 'contribution';
                $new_element2 = $new_value2 = '';
				break;
			case 'contribution':
				if($months > 12) {
                	$new_value = 100 * ( ($value * $rate_months ) / ($months * $gross_net) );
				}else{
					$new_value = $value*100/$gross_net;
				}
				$new_element = 'rate';
                $new_element2 = $new_value2 = '';
				break;
			case 'rate_lessor':
				$new_value = $value*$gross_net/100;
                if(Input::get('if_rounding') == 'true')
                    $new_value = round($new_value);

				$new_element = 'contribution_lessor';

                $new_element2 = 'last_year_lessor_contribution';
                $new_value2 =  ($new_value/12) * $months_diff;
                if(Input::get('if_rounding') == 'true')
                    $new_value2 = round($new_value2);

                $new_value2 = number_format((float)$new_value2, 2, '.', '');
				break;
			case 'contribution_lessor':
				$new_value = $value*100/$gross_net;
				$new_element = 'rate_lessor';

                $new_element2 = 'last_year_lessor_contribution';
                $new_value2 =  ($value/12) * $months_diff ;
                if(Input::get('if_rounding') == 'true')
                    $new_value2 = round($new_value2);

                $new_value2 = number_format((float)$new_value2, 2, '.', '');
				break;
		}
        $new_value = number_format((float)$new_value, 2, '.', '');

        return json_encode([
			'element' 	=> $new_element,
			'value'		=> $new_value,
            'element2'  => $new_element2,
            'value2'    => $new_value2
		]);
	}

	public function postCheckOwner()
	{
		$leasing_agreement_id = Input::get('leasing_agreement_id');
		$leasingAgreement = LeasingAgreement::findOrFail($leasing_agreement_id);

		$owner_status = $leasingAgreement->owner->active;

		return json_encode(['status' => $owner_status]);
	}

    public function postChangeOwner($leasing_agreement_id)
    {
		if(Input::get('owner_id') == 0)
		{
			return json_encode(array(
				'code' => 3,
				'error' => 'Proszę poprawnie wybrać nowego właściciela'
			));
		}
        $previousAgreement = LeasingAgreement::findOrFail($leasing_agreement_id);

        $leasingAgreement = LeasingAgreement::findOrFail($leasing_agreement_id);
        $leasingAgreement->owner_id = Input::get('owner_id');
        $leasingAgreement->save();

        $history_id = Histories::leasingAgreementHistory($leasing_agreement_id, 18);

        new \Idea\Logging\LeasingAgreements\Logger(18,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $leasingAgreement->toArray()
                ]
            ], $history_id, $leasing_agreement_id);

        Flash::success('Zmieniono pomyślnie finansującego.');

        return json_encode(array(
            'code' => 0
        ));
    }

	public function getRefundYacht($leasing_agreement_id)
	{
		$leasingAgreement = LeasingAgreement::with('insurances')->findOrFail($leasing_agreement_id);
		$coveragesTypes = LeasingAgreementInsuranceCoverageType::lists('name', 'id');

		return View::make('insurances.manage.actions.refund-yacht', compact('leasingAgreement', 'coveragesTypes'));
	}

	public function getRefundYachtContent()
	{
		$insurance = LeasingAgreementInsurance::findOrFail(Input::get('insurance_id'));
		$date_to = $insurance->date_to;

		if(!is_null($date_to) &&  new DateTime($date_to) > new DateTime(date('Y-m-d')) )
			$refund = $this->calculateYachtRefund($insurance->id, date('Y-m-d'));
		else
			$refund = array();

		return View::make('insurances.manage.actions.refund-yacht-content', compact('insurance', 'refund', 'date_to'));
	}

	public function postCalculateYachtRefund($insurance_id, $date_count_from)
	{
		$insurance = LeasingAgreementInsurance::find($insurance_id);

		if(is_null($insurance->date_to))
			return ['error' => 'uzupełnij datę zakończenia ostatniej polisy'];

		$endInsuranceDate = Date::createFromFormat('Y-m-d', $insurance->date_to);
		$daysToRefund = Date::createFromFormat('Y-m-d', $date_count_from)->diffInDays($endInsuranceDate);

		if(is_null($insurance->date_from))
			return ['error' => 'uzupełnij datę rozpoczęcia ostatniej polisy'];

		$insuranceDays = Date::createFromFormat('Y-m-d', $insurance->date_from)->diffInDays($endInsuranceDate);

		if(is_null($insurance->contribution_lessor))
			return ['error' => 'uzupełnij składkę leasingobiorcy'];

		$date_to = $insurance->date_to;
		if(!is_null($date_to) &&  new DateTime($date_to) >= new DateTime($date_count_from) )
		{
			$contribution = $insurance->contribution_lessor;
			$contributionPerDay = ($contribution/$insuranceDays);
			$valueToRefund = $contributionPerDay*$daysToRefund;
		}
		else
			return ['error' => 'data zwrotu składki musi być przed datą końca polisy'];

		return [ 'value' => number_format((float)$valueToRefund, 2, '.', '')];
	}

	public function postStoreRefundYacht($agreement_id)
	{
		$agreement = LeasingAgreement::findOrFail($agreement_id);
		$insurance = LeasingAgreementInsurance::findOrFail(Input::get('insurance_id'));

		$insurance->active = 0;
		$insurance->save();

		$new_insurance = $insurance;
		$new_insurance->active = 1;
		$new_insurance->date_from = Input::get('date_to');
		$new_insurance->refund = Input::get('refund');
		$new_insurance->if_refund_contribution = 1;
		$new_insurance->user_id = Auth::user()->id;

		$new_insurance->notification_number = Auth::user()->insurances_global_nr;
		$new_insurance->refunded_insurance_id = $insurance->id;

		$new_insurance->commission_refund_value = Input::get('commission_refund_value');

		$new_insurance = LeasingAgreementInsurance::create($new_insurance->toArray());



		foreach($insurance->payments as $payment)
		{
			LeasingAgreementInsurancePayment::create([
				'leasing_agreement_insurance_id' => $new_insurance->id,
				'deadline' => $payment->deadline,
				'amount'	=> $payment->amount,
				'date_of_payment' => $payment->date_of_payment
			]);
		}

		foreach($insurance->coverages as $coverage)
		{
			LeasingAgreementInsuranceCoverage::create([
				'leasing_agreement_insurance_id' => $new_insurance->id,
				'leasing_agreement_insurance_coverage_type_id' => $coverage->leasing_agreement_insurance_coverage_type_id,
				'amount' => $coverage->amount,
				'currency_id' => $coverage->currency_id,
				'net_gross' => $coverage->net_gross
			]);
		}

		$non_refund_insurances = LeasingAgreementInsurance::where('leasing_agreement_id', $agreement_id)->active()->where('if_refund_contribution', 0)->first();
		if(! $non_refund_insurances) {
			$agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
			$agreement->save();
		}

		Histories::leasingAgreementHistory($agreement_id, 10);

		Flash::success("Wykonano zwrot składki do umowy nr ".$agreement->nr_contract);


		return Redirect::to('insurances/manage/inprogress');
	}

	public function getDownloadDocument($file_id)
	{
		ob_start();
		$file = LeasingAgreementFile::find($file_id);
		$path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$file->file;

		$finfo = finfo_open(FILEINFO_MIME_TYPE);

		$pathParts = pathinfo($path);

		$name = rand('10000','99999');
		// Prepare the headers
		$headers = array(
			'Content-Description' => 'File Transfer',
			'Content-Type' => finfo_file($finfo, $path),
			'Content-Transfer-Encoding' => 'binary',
			'Expires' => 0,
			'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
			'Pragma' => 'public',
			'Content-Length' => File::size($path),
			'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
		);
		finfo_close($finfo);

		$response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

		// If there's a session we should save it now
		if (Config::get('session.driver') !== '') {
			Session::save();
		}

		// Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
		session_write_close();
		if (ob_get_contents()) ob_end_clean();
		$response->sendHeaders();
		if ($file = fopen($path, 'rb')) {
			while (!feof($file) and (connection_status() == 0)) {
				print(fread($file, 1024 * 8));
				flush();
			}
			fclose($file);
		}

		// Finish off, like Laravel would
		Event::fire('laravel.done', array($response));
		//$response->foundation->finish();

		exit;
	}

	/*
	public function archiveYacht($agreement_id)
	{
		$leasingAgreement = LeasingAgreement::with('insurances')->findOrFail($agreement_id);
		$coveragesTypes = LeasingAgreementInsuranceCoverageType::lists('name', 'id');

		return View::make('insurances.manage.actions.archive-yacht', compact('leasingAgreement', 'coveragesTypes'));
	}

	public function moveArchiveYacht($agreement_id)
	{
		$agreement = LeasingAgreement::find($agreement_id);
		$agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
		$agreement->save();

		$non_refund_insurances = LeasingAgreementInsurance::where('leasing_agreement_id', $agreement_id)->active()->where('if_refund_contribution', 0)->first();
		if(! $non_refund_insurances) {
			$agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
			$agreement->save();
		}

		Histories::leasingAgreementHistory($agreement->id, 8);
		Flash::success("Umowa nr <i>".$agreement->nr_contract."</i> została przeniesiona do archiwum.");

		return json_encode(array(
			'code' => 0
		));
	}
	*/


    public function getAssignAfterRefund($agreement_id)
    {
        $leasingAgreement = LeasingAgreement::find($agreement_id);
        $insurance = $leasingAgreement->activeInsurance()->refundedInsurance;

        $isFirstInsurance = true;
        $intervals = $this->calculateDatesIntervals($leasingAgreement, $insurance);

        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
            ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);

        $paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
        $insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');

        $insurance_company = Insurance_companies::find($insurance->insurance_company_id);
        $if_rounding = ($insurance_company) ? $insurance_company->if_rounding : null;

        return View::make('insurances.manage.actions.assign-after-refund', compact('insurance','leasingAgreement',
            'insuranceCompanies', 'paymentWays', 'insuranceTypes', 'isFirstInsurance', 'intervals', 'if_rounding'));
    }

    public function postStoreAfterRefund($agreement_id)
    {
        $inputs = Input::all();

        $agreement = LeasingAgreement::find($agreement_id);

        $last_insurance = $agreement->insurances->last();
        $last_insurance->active = 0;
        $last_insurance->save();

        $inputs['archive'] = null;

        $agreement->update($inputs);
        $agreement->save();

        $inputs['notification_number'] = Auth::user()->insurances_global_nr;

        $insurance = LeasingAgreementInsurance::create($inputs);

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

        Histories::leasingAgreementHistory($agreement_id, 5);

        Flash::success("Dodano polisę do umowy nr ".$agreement->nr_contract);
        return Redirect::to('insurances/manage/index');
    }

    public function getAssignAfterRefundYacht($agreement_id)
    {
        $leasingAgreement = LeasingAgreement::find($agreement_id);
        $insurance = $leasingAgreement->activeInsurance()->refundedInsurance;

        $coverages = array();


        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')
            ->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $insuranceCompanies[0] = '---wybierz ubezpieczyciela---';
        ksort($insuranceCompanies);

        $paymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
        $insuranceTypes = LeasingAgreementInsuranceType::lists('name', 'id');

        $installments = LeasingAgreementInsuranceInstallment::lists('installments', 'id');

        return View::make('insurances.manage.actions.assign-after-refund-yacht', compact('insurance','leasingAgreement',
            'insuranceCompanies', 'paymentWays', 'insuranceTypes', 'installments', 'coverages'));
    }

    public function postStoreAfterRefundYacht($agreement_id)
    {
        $inputs = Input::all();

        $leasingAgreement = LeasingAgreement::find($agreement_id);

        $last_insurance = $leasingAgreement->insurances->last();
        $last_insurance->active = 0;
        $last_insurance->save();

        $inputs['archive'] = null;

        $leasingAgreement->update($inputs);
        $leasingAgreement->save();

        $inputs['notification_number'] = Auth::user()->insurances_global_nr;
        $inputs['active'] = 1;

        if($inputs['insurance_date'] == '') $inputs['insurance_date'] = null;

        $insurance = LeasingAgreementInsurance::create($inputs);

        foreach($inputs['date_payment_deadline'] as $k => $date_payment_deadline)
        {
            LeasingAgreementInsurancePayment::create([
                'leasing_agreement_insurance_id' => $insurance->id,
                'deadline' => $date_payment_deadline,
                'amount'	=> $inputs['date_payment_amount'][$k],
                'date_of_payment' => (isset($inputs['paid']) && isset($inputs['paid'][$k]) && $inputs['date_of_payment'][$k] != '') ? $inputs['date_of_payment'][$k] : null
            ]);
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


        Histories::leasingAgreementHistory($agreement_id, 5);

        Flash::success("Dodano polisę do umowy nr ".$leasingAgreement->nr_contract);
        return Redirect::to('insurances/manage/index');
    }


}
