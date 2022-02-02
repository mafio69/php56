<?php

class InsurancesCreateController extends \BaseController {

    /**
     * InsurancesCreateController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:wykaz_polis#wprowadzenie_umowy');
    }


	public function getIndex()
	{
        $leasingAgreementTypes = LeasingAgreementType::lists('name', 'id');
        $leasingAgreementPaymentWays = LeasingAgreementPaymentWay::lists('name', 'id');

        $owners = Owners::orderBy('name')->lists('name', 'id');
        $owners[0] = '---wybierz finansującego---';
        ksort($owners);


        return View::make('insurances.manage.create.index', compact('leasingAgreementTypes', 'leasingAgreementPaymentWays', 'owners'));
	}

    public function getCreateClient()
    {
        return View::make('insurances.manage.create.create-client');
    }

    public function postCheckClientNip()
    {
        \Debugbar::disable();

        $nip = trim(str_replace('-', '', Input::get('NIP')));
        $client = Clients::where('NIP', '=', $nip)->get();
        if ($client->isEmpty())
            return '0';

        return '1';
    }

    public function postStoreClient(){
        $input = Input::all();
        $input['NIP'] = trim(str_replace('-', '', $input['NIP']));

        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
        $registry_post = $input['registry_post'];
        if(strlen($registry_post) == 6)
        {
            $voivodeship_id = $matcher->match($registry_post);
            $input['registry_voivodeship_id'] = $voivodeship_id;
        }
        $correspond_post = $input['correspond_post'];
        if(strlen($correspond_post) == 6)
        {
            $voivodeship_id = $matcher->match($correspond_post);
            $input['correspond_voivodeship_id'] = $voivodeship_id;
        }

        $client = Clients::create($input);

        if($client){
            $result['status'] = 'success';
            $result['client'] = $client->toArray();
        }else{
            $result['status'] = 'error';
            $result['msg'] = 'Wystąpił błąd w trakcie dodawania klienta. Skontaktuj się z administratorem.';
        }
        return json_encode($result);
    }

    public function getCreateObject()
    {
        $object_assetTypes = ObjectAssetType::lists('name', 'id');
        return View::make('insurances.manage.create.partials.object', compact('object_assetTypes'));
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

        $leasingAgreement = LeasingAgreement::create($inputs);
        if(Input::has('object-name')){
            foreach($inputs['object-name'] as $k => $name){
                LeasingAgreementObject::create([
                    'name' => $name,
                    'user_id' => $inputs['user_id'],
                    'leasing_agreement_id' => $leasingAgreement->id,
                    'object_assetType_id' => $inputs['object-object_assetType_id'][$k],
                    'net_value' => $inputs['object-net_value'][$k],
                    'gross_value' => $inputs['object-gross_value'][$k]
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
