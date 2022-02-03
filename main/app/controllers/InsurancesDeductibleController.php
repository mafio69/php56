<?php

class InsurancesDeductibleController extends \BaseController {

    /**
     * InsurancesDeductibleController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:wykaz_franszyz#wejscie');
        $this->beforeFilter('permitted:wykaz_franszyz#zarzadzaj', ['except' => ['getIndex']]);
    }

    public function getIndex($insurance_company_id = null, $id = null)
	{
        if(is_null($insurance_company_id)) {
            $insurancesCompanyInRates = LeasingAgreementInsuranceGroupRate::groupBy('insurance_company_id')->first(['insurance_company_id']);
            $insurance_company_id = $insurancesCompanyInRates->insurance_company_id;
            return Redirect::to(url('insurances/deductible/index', [$insurance_company_id]));
        }
        $rates = LeasingAgreementInsuranceGroupRate::where('insurance_company_id', $insurance_company_id)->orderBy('created_at', 'desc')->get();

        $existInsuranceCompaniesInInsurancesRates = LeasingAgreementInsuranceGroupRate::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesRates)->orderBy('name')->lists('name', 'id');

        return View::make('insurances.deductible.index', compact('rates', 'insuranceCompanies','insurance_company_id'));
	}


  public function getEdit($id)
  {
        $rate = LeasingAgreementInsuranceGroupRate::find($id);

        return View::make('insurances.deductible.edit', compact('rate'));
  }

	public function postUpdate($id)
	{
        $inputs = Input::all();

        $rules = array(
            'value' => 'numeric',
        );

        $validator = Validator::make($inputs, $rules);

        if ($validator->fails())
        {
            $result['code'] = 2;
            $result['error'] = 'Wystąpił błąd w trakcie edycji.';

            return json_encode($result);
        }

        if(isset($inputs['type'])){
          if($inputs['type']==1){
            $data['deductible_value'] =  $inputs['value'];
          }
          else{
            $data['deductible_percent'] =  $inputs['value'];
          }
        }
        else{
          $data['deductible_value'] =  $inputs['value'];
        }

        $rate = LeasingAgreementInsuranceGroupRate::find($id);

        $rate->update($data);

        $result['code'] = 0;
        return json_encode($result);
	}


}
