<?php

class InsurancesCreateYachtController extends \BaseController {

    /**
     * InsurancesCreateController constructor.
     */
    public function __construct()
    {
    }


    public function getIndex()
    {
        $leasingAgreementTypes = LeasingAgreementType::lists('name', 'id');
        $owners = Owners::orderBy('name')->lists('name', 'id');
        $owners[0] = '---wybierz finansującego---';
        ksort($owners);

        return View::make('insurances.manage.create-yacht.index', compact('leasingAgreementTypes', 'leasingAgreementPaymentWays', 'owners'));
    }

    public function getCreateYacht()
    {
        $yacht_assetTypes = ObjectAssetType::where('if_yacht', 1)->lists('name', 'id');
        return View::make('insurances.manage.create-yacht.partials.yacht', compact('yacht_assetTypes'));
    }

    public function postStore()
    {
        $inputs = Input::all();
        $inputs['user_id'] = Auth::id();
        $inputs['nr_contract'] = trim($inputs['nr_contract']);
        $inputs['creating_way'] = 1;

        $validator = Validator::make($inputs ,
            array(
                'nr_contract' => 'required|Unique:leasing_agreements,nr_contract,NULL,id,withdraw,NULL',
                'owner_id' => 'required|numeric|min:1'
            )
        );

        if($validator -> fails()){
            Flash::error('W systemie istnieje już umowa o podanym numerze.');
            return Redirect::back()->withInput(Input::except('client_id'));
        }

        $inputs['nr_agreement'] = $this->generateNr_agreement();
        $inputs['has_yacht'] = 1;

        $leasingAgreement = LeasingAgreement::create($inputs);
        if(Input::has('yacht-name')){
            foreach($inputs['yacht-name'] as $k => $name){
                LeasingAgreementObject::create([
                    'name' => $name,
                    'user_id' => $inputs['user_id'],
                    'leasing_agreement_id' => $leasingAgreement->id,
                    'object_assetType_id' => $inputs['yacht-yacht_assetType_id'][$k],
                    'net_value' => $inputs['yacht-net_value'][$k],
                    'gross_value' => $inputs['yacht-gross_value'][$k],
                    'fabric_number' => $inputs['fabric_number'][$k],
                    'registration_number' => $inputs['registration_number'][$k]
                ]);
            }
        }

        Histories::leasingAgreementHistory($leasingAgreement->id, 1);

        Flash::success('Dodano nową umowę do systemu.');
        return Redirect::to('insurances/manage/index');
    }

    private function generateNr_agreement()
    {
        $lastAgreement = LeasingAgreement::orderby('id', 'desc')->first();
        if(is_null($lastAgreement)){
            return '1/'.date('n/Y');
        }

        $nr = explode('/', $lastAgreement->nr_agreement);
        $year = date('Y');
        $month = date('n');
        if($year > $nr[2]){
            return '1/'.date('n/Y');
        }

        if($month > (int)$nr[1]){
            return '1/'.date('n').'/'.$nr[2];
        }
        $nr[0] +=1;
        return $nr[0].'/'.$nr[1].'/'.$nr[2];
    }
}
