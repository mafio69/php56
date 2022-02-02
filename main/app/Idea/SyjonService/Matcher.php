<?php

namespace Idea\SyjonService;


use Auth;
use Clients;
use Injury;
use DosOtherInjury;
use InjuryLetter;
use LiquidationCards;
use MobileInjury;
use SyjonProgram;
use Vehicles;
use VmanageVehicle;

class Matcher
{
    private $client_id;
    private $owner_id;

    /**
     * Matcher constructor.
     */
    public function __construct($input)
    {
        $this->client_id = $input->get('client_id');
        $this->owner_id = $input->get('owner_id');
    }

    public function letters($contracts){
        foreach($contracts->data as $contract)
        {
            foreach($contract->vehicles as $vehicle)
            {
                $vehicle->letters = $this->searchLetters($vehicle->registration, $contract->contract_number);
            }
        }

        return $contracts;
    }

    public function injuries($contracts)
    {
        foreach($contracts->data as $contract) {
            foreach ($contract->vehicles as $vehicle) {
                $vehicle->injuries = $this->searchInjuries($vehicle->registration, $contract->contract_number, $contract->object_user->contractor_nip);
            }
        }

        return $contracts;
    }

    public function dosInjuries($contracts)
    {
        foreach($contracts->data as $contract) {
            foreach ($contract->vehicles as $vehicle) {
                $vehicle->injuries = $this->searchDosInjuries($contract->contract_number, $contract->object_user->contractor_nip);
            }
        }

        return $contracts;
    }

    public function unprocessed($contracts, $type = 1)
    {
        foreach($contracts->data as $contract) {
            foreach ($contract->vehicles as $vehicle) {
                $vehicle->unprocessed = $this->searchUnprocessed($vehicle, $contract, $type);
            }
        }

        return $contracts;
    }

    public function searchLetters($registration, $nr_contract)
    {
        return  InjuryLetter::whereNull('injury_file_id')->where(function($query) use ($registration, $nr_contract){
            if($nr_contract != '') {
                $query->orWhere(function ($subquery) use ($nr_contract) {
                    $subquery->whereNotNull('nr_contract')->where('nr_contract',  'like', $nr_contract);
                });
            }
            if($registration != '') {
                $query->orWhere(function ($subquery) use ($registration) {
                    $subquery->whereNotNull('registration')->where('registration', 'like', $registration);
                });
            }
        })->get();
    }

    public function searchInjuries($registration, $nr_contract, $nip = null)
    {
        $query = Injury::where(function($query) use ($registration, $nr_contract, $nip){
            if($registration && $registration != '') {
                $query->where(function ($query) use ($registration) {
                    $query->vehicleExists('registration', $registration);
                });
            }

            if($nr_contract && $nr_contract != '') {
                $nr_contract = explode('/', $nr_contract);
                $nr_contract = array_slice($nr_contract, 0, 2);
                $nr_contract = implode('/', $nr_contract);
                $query->orWhere(function ($query) use ($nr_contract) {
                    $query->vehicleExistsLikeStart('nr_contract', $nr_contract);
                });
            }
        })->where('active', '=', 0)
            ->with('getInfo', 'vehicle', 'injuries_type', 'user', 'chat', 'chat.messages', 'status')
            ->orderBy('created_at','desc');


        return $query->get();
    }

    public function searchDosInjuries( $nr_contract, $nip = null)
    {
        return DosOtherInjury::where(function($query) use ($nr_contract, $nip){
            if($nr_contract && $nr_contract != '') {
                $nr_contract = explode('/', $nr_contract);
                $nr_contract = array_slice($nr_contract, 0, 2);
                $nr_contract = implode('/', $nr_contract);

                $query->whereHas('object', function ($query) use ($nr_contract) {
                    $query->where('nr_contract', 'like', $nr_contract.'%');
                });
            }

            if($nip && $nip != '') {
                $query->whereHas('client', function($query) use($nip)
                {
                    $query -> where('NIP', 'like', $nip);
                });
            }
        })->where('active', '=', 0)
            ->with('object', 'object.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'type_incident', 'status')
            ->orderBy('created_at','desc')
            ->get();
    }


    public function searchUnprocessed($vehicle, $contract, $type = 1)
    {
        return MobileInjury::where('active', '=', '0')
            ->where(function($query) use($vehicle, $contract, $type)
            {
                if($vehicle->registration != '') {
                    $query -> where('registration', 'like', $vehicle->registration);
                }

                if($contract->contract_number != '') {
                    $query -> orWhere('nr_contract', 'like', $contract->contract_number.'%');
                }

                if($type == 1) { //pojazdy
                    $query->where(function($query){
                        $query->where('source', 0);
                        $query->orWhereIn('injuries_type', [2,1,3]);
                    });
                    $query->where('source', '!=', 3);
                }else{ // majątek
                    $query->whereNotIn('source', [0,3]);
                    $query->whereIn('injuries_type', [4,5]);
                }
            })
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->get();
    }

    public function searchLiquidationCard($registration, $vin, $nr_contract)
    {
        $vehiclesA = Vehicles::where(function ($query) use($registration, $vin, $nr_contract){
            if($registration != '') {
                $query->whereRegistration($registration);
            }
            if($vin != '') {
                $query->where('vin',  'like', $vin);
            }
            if($nr_contract != '') {
                $nr_contract = explode('/', $nr_contract);
                $nr_contract = array_slice($nr_contract, 0, 2);
                $nr_contract = implode('/', $nr_contract);
                
                $query->where('nr_contract', 'like', $nr_contract.'%');
            }
        })->whereActive(0)->lists('id');

        $cardsA = LiquidationCards::whereIn('vehicle_id', $vehiclesA)->orderBy('id', 'desc')->get();
        if (!$cardsA->isEmpty()) {
            return $cardsA->first();
        }

        return false;
    }

    public function searchVehicle($input)
    {
        $syjonService = new \Idea\SyjonService\SyjonService();
        $syjon_vehicle = json_decode( $syjonService->loadVehicle($input->get('vehicle_id'), $input->get('contract_id')) )->data;
        $syjon_contract = json_decode( $syjonService->loadContract($input->get('contract_id')) )->data;
        $syjon_policy = $input->get('policy_id') ? json_decode( $syjonService->loadPolicy($input->get('policy_id')))->data : null;
        $this->parseClient($syjon_contract->object_user);
        $this->parseOwner($syjon_contract->owner);
        $vehicleDb = Vehicles::where('nr_contract', 'like', $syjon_contract->contract_number ? $syjon_contract->contract_number :'')
                                ->where('vin', 'like', $syjon_vehicle->vin ? $syjon_vehicle->vin : '')
                                ->orderBy('parent_id', 'desc')->first();

        if($vehicleDb)
        {
            $vehicle = Vehicles::create($vehicleDb->toArray());
            $vehicle->update([
                'registration' => mb_strtoupper( $syjon_vehicle->registration ),
                'VIN' => mb_strtoupper( $syjon_vehicle->vin ),
                'brand' => mb_strtoupper( $syjon_vehicle->brand ),
                'model' => mb_strtoupper( $syjon_vehicle->model ),
                'engine' => $syjon_vehicle->engine_capacity,
                'nr_contract' => mb_strtoupper( $syjon_contract->contract_number ),
                'year_production' => $syjon_vehicle->year_production,
                'first_registration' => $syjon_vehicle->first_registration,
                'mileage' => $syjon_vehicle->mileage,
                'expire' => $syjon_policy ? $syjon_policy->policy_date_to : $vehicle->expire,
                'netto_brutto' => $syjon_policy ? ($this->parseNettoBrutto($syjon_policy)) : $vehicle->netto_brutto,
                'assistance' => $syjon_policy ? ($syjon_policy->policy_assistance == 'Tak' ? 1 : 0) : $vehicle->assistance,
                'insurance' => $syjon_policy ? $syjon_policy->policy_insurance_amount : $vehicle->insurance,
                'risks' => $syjon_policy ? $syjon_policy->policy_risks : $vehicle->risks,
                'nr_policy' => $syjon_policy ? mb_strtoupper( $syjon_policy->policy_number ) : $vehicle->nr_policy,
                'object_type' => $syjon_vehicle ?  $syjon_vehicle->object_type_origin : '',
                'contract_status' => mb_strtoupper(  $syjon_contract->contract_status ),
                'gap' => $syjon_policy ? ($syjon_policy->policy_gap == 'Tak' ? 1 : 2) : $vehicle->gap,
                'legal_protection' => $syjon_policy ? ($syjon_policy->policy_protected == 'Tak' ? 1 : 2) : $vehicle->legal_protection,
                'insurance_company_name' => $syjon_policy ? mb_strtoupper( $syjon_policy->policy_insurance_company ) : $vehicle->insurance_company_name,
                'insurance_company_id' => $input->get('insurance_company_id'),
                'policy_insurance_company_id' => $input->get('policy_insurance_company_id'),
                'end_leasing' => $syjon_contract->contract_planned_ending_date,
                'parent_id' => $vehicleDb->id,
                'syjon_program_id' => $syjon_contract->program_id
            ]);
        }else {

            $vmanageVehicle = VmanageVehicle::where('outdated', 0)
                ->where('vin', 'like', $syjon_vehicle->vin ? $syjon_vehicle->vin : '')
                ->where('nr_contract', 'like', $syjon_contract->contract_number ? $syjon_contract->contract_number :'')
                ->first();

            if ($vmanageVehicle) {
                $vmanageVehicle->update(['outdated' => 1]);
            }

            $vehicle = Vehicles::create([
                'registration' => mb_strtoupper( $syjon_vehicle->registration ),
                'VIN' => mb_strtoupper( $syjon_vehicle->vin ),
                'brand' => mb_strtoupper( $syjon_vehicle->brand ),
                'model' => mb_strtoupper( $syjon_vehicle->model ),
                'engine' => $syjon_vehicle->engine_capacity,
                'nr_contract' => mb_strtoupper( $syjon_contract->contract_number ),
                'year_production' => $syjon_vehicle->year_production,
                'first_registration' => $syjon_vehicle->first_registration,
                'mileage' => $syjon_vehicle->mileage,
                'expire' => $syjon_policy ? $syjon_policy->policy_date_to : null,
                'netto_brutto' => $syjon_policy ? $this->parseNettoBrutto( $syjon_policy ) : 0,
                'assistance' => $syjon_policy ? ($syjon_policy->policy_assistance == 'Tak' ? 1 : 0) : 0,
                'insurance' => $syjon_policy ? $syjon_policy->policy_insurance_amount : null,
                'risks' => $syjon_policy ? $syjon_policy->policy_risks : null,
                'nr_policy' => $syjon_policy ? mb_strtoupper( $syjon_policy->policy_number ) : null,
                'object_type' => $syjon_vehicle ?  $syjon_vehicle->object_type_origin : '',
                'contract_status' =>  $syjon_contract->contract_status,
                'gap' => $syjon_policy ? ($syjon_policy->policy_gap == 'Tak' ? 1 : 2) : 0,
                'legal_protection' => $syjon_policy ? ($syjon_policy->policy_protected == 'Tak' ? 1 : 2) : 0,
                'insurance_company_name' => $syjon_policy ? mb_strtoupper( $syjon_policy->policy_insurance_company ) : null,
                'insurance_company_id' => $input->get('insurance_company_id'),
                'policy_insurance_company_id' => $input->get('policy_insurance_company_id'),
                'end_leasing' => $syjon_contract->contract_planned_ending_date,
                'cfm' => $vmanageVehicle ? $vmanageVehicle->cfm : null,
                'register_as' => $vmanageVehicle ? $vmanageVehicle->register_as : 0,
                'syjon_program_id' => $syjon_contract->program_id
            ]);
        }



        $vehicle->update([
            'client_id' => $this->client_id,
            'owner_id' => $this->owner_id
        ]);

        return $vehicle;
    }

    private function parseClient($object_user)
    {
        $nip = $object_user->contractor_nip;
        $regon = $object_user->contractor_regon;
        $name = $object_user->contractor_name;

        if($regon || $nip) {
            $clientDb = Clients::where(function($query) use($nip, $regon){
                            if($nip) {
                                $query->where('NIP', 'like', $nip);
                            }
                            if($regon) {
                                $query->where('REGON', 'like', $regon);
                            }
                        })
                        ->where('active', '=', '0')
                        ->orderBy('parent_id', 'desc')
                        ->first();
        }elseif($name){
            $clientDb = Clients::
                            where('name', 'like', $name)
                            ->where('active', '=', '0')
                            ->orderBy('parent_id', 'desc')
                            ->first();
        }else{
            $clientDb = false;
        }

        if(!$nip) $nip = '';
        if(!$regon) $regon = '';

        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

        $registry_post = $object_user->contractor_office_post_code;
        $registry_voivodeship_id = $matcher->match($registry_post);

        $correspond_post = $object_user->contractor_office_correspondence_post_code;
        $correspond_voivodeship_id = $matcher->match($correspond_post);

        $phones = implode(',', (array) $object_user->contractor_office_phone);

        if(! $regon) $regon = '';

        if(
            $clientDb &&
            (
                $clientDb->name != $object_user->contractor_name ||
                $clientDb->NIP != $nip ||
                $clientDb->REGON != $regon ||
                $clientDb->registry_post != $registry_post ||
                $clientDb->registry_city != $object_user->contractor_office_city ||
                $clientDb->registry_street != $object_user->contractor_office_street ||
                $clientDb->registry_voivodeship_id != $registry_voivodeship_id ||
                $clientDb->correspond_post != $correspond_post ||
                $clientDb->correspond_city != $object_user->contractor_office_correspondence_city ||
                $clientDb->correspond_street != $object_user->contractor_office_correspondence_street ||
                $clientDb->correspond_voivodeship_id != $correspond_voivodeship_id ||
                $clientDb->phone != $phones ||
                $clientDb->email != $object_user->contractor_office_email ||
                $clientDb->firmID != $object_user->contractor_code_client
            )
        ){
            $client = Clients::create(array(
                'name' => $object_user->contractor_name ,
                'firmID' => $object_user->contractor_code_client,
                'NIP' => $nip,
                'REGON' => $regon,
                'registry_post' => $registry_post,
                'registry_city' => $object_user->contractor_office_city,
                'registry_street' => $object_user->contractor_office_street,
                'registry_voivodeship_id' => $registry_voivodeship_id,
                'correspond_post' => $correspond_post,
                'correspond_city' => $object_user->contractor_office_correspondence_city,
                'correspond_street' => $object_user->contractor_office_correspondence_street,
                'correspond_voivodeship_id' => $correspond_voivodeship_id,
                'phone' => $phones,
                'email' => $object_user->contractor_office_email,
                'parent_id' => $clientDb->id
            ));
            $clientDb->update(['active' => 9]);
        }elseif(! $clientDb){
            $client = Clients::create(array(
                'name' => $object_user->contractor_name ,
                'firmID' => $object_user->contractor_code_client,
                'NIP' => $nip,
                'REGON' => $regon,
                'registry_post' => $registry_post,
                'registry_city' => $object_user->contractor_office_city,
                'registry_street' => $object_user->contractor_office_street,
                'registry_voivodeship_id' => $registry_voivodeship_id,
                'correspond_post' => $correspond_post,
                'correspond_city' => $object_user->contractor_office_correspondence_city,
                'correspond_street' => $object_user->contractor_office_correspondence_street,
                'correspond_voivodeship_id' => $correspond_voivodeship_id,
                'phone' => $phones,
                'email' => $object_user->contractor_office_email
            ));
        }else{
            $client = $clientDb;
        }

        $this->client_id = $client->id;
        return;
    }

    private function parseOwner($syjon_owner)
    {
        $nip = $syjon_owner->contractor_nip;
        $name = $syjon_owner->contractor_name ? $syjon_owner->contractor_name : '';
        $owner = \Owners::where(function($query) use($nip){
                            if($nip) {
                                $query->whereHas('nip', function ($query) use ($nip) {
                                    $query->where('value', 'like', $nip);
                                });
                            }
                        })
                        ->where('name', 'like', $name)->whereActive(0)->first();

        if(! $owner){
            $words = preg_split("/\s+/", $syjon_owner->contractor_name);
            $acronym = "";

            foreach ($words as $w) {
                $acronym .= mb_strtoupper( $w[0] );
            }

            $owner = \Owners::create([
                'syjon_contractor_id' => $syjon_owner->contractor_id,
                'name' => $syjon_owner->contractor_name,
                'short_name' => $acronym,
                'post' => $syjon_owner->contractor_office_post_code,
                'city' => $syjon_owner->contractor_office_city,
                'street' => $syjon_owner->contractor_office_street,
                'owners_group_id' => 8,
            ]);

            \Idea_data::insert([
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 8,
                    'value' => $syjon_owner->contractor_nip
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 1,
                    'value' => $syjon_owner->contractor_name
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 2,
                    'value' => $syjon_owner->contractor_office_street
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 3,
                    'value' => $syjon_owner->contractor_office_post_code
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 4,
                    'value' => $syjon_owner->contractor_office_email
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 5,
                    'value' => implode(',', (array) $syjon_owner->contractor_office_phone)
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 8,
                    'value' => $syjon_owner->contractor_nip
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 13,
                    'value' => $syjon_owner->contractor_office_city
                ],
                [
                    'owner_id' => $owner->id,
                    'parameter_id' => 15,
                    'value' => $syjon_owner->contractor_regon
                ],
            ]);
        }

        $this->owner_id = $owner->id;
        return;
    }

    public function parseInsuranceCompany($policy)
    {
        if(!$policy ) return;
        $insuranceCompanyDb = \Insurance_companies::where('name', 'like', $policy->policy_insurance_company)->first();

        if($policy->policy_insurance_company != ''  && $insuranceCompanyDb){
            if($insuranceCompanyDb->active != 0)
            {
                $insuranceCompanyDb->update(['active' => 0]);
            }
            return $insuranceCompanyDb->id;
        }

        if(!property_exists($policy, 'policy_insurance_company_data') || ! $policy->policy_insurance_company_data ){
            return null;
        }

        $insuranceCompanyDb = \Insurance_companies::create([
            'name' => $policy->policy_insurance_company_data->contractor_name,
            'street' => $policy->policy_insurance_company_data->contractor_office_street,
            'post' => $policy->policy_insurance_company_data->contractor_office_post_code,
            'city' => $policy->policy_insurance_company_data->contractor_office_city,
            'email' => $policy->policy_insurance_company_data->contractor_office_email,
            'phone'=> $policy->policy_insurance_company_data->contractor_office_phone,
            'if_rounding' => 1,
            'if_full_year' => 1,
        ]);

        return $insuranceCompanyDb->id;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getOwnerId()
    {
        return $this->owner_id;
    }

    public function searchObject($input)
    {
        $syjonService = new \Idea\SyjonService\SyjonService();
        $syjon_contract = json_decode( $syjonService->loadContract($input->get('contract_id')) )->data;
        $this->parseClient($syjon_contract->object_user);
        $this->parseOwner($syjon_contract->owner);
        $objectDb = \Objects::where('nr_contract', 'like', $syjon_contract->contract_number ? $syjon_contract->contract_number :'')
                                ->orderBy('parent_id', 'desc')->first();

        if($objectDb)
        {
            $object = \Objects::create($objectDb->toArray());
            $object->update(
                [
                    'contract_status' => $syjon_contract->contract_status,
                    'parent_id' => $objectDb->id,
                    'client_id' => $this->client_id,
                    'owner_id' => $this->owner_id
                ]
            );
        }else {
            $object = \Objects::create([
                'nr_contract' => $syjon_contract->contract_number,
                'contract_status' => $syjon_contract->contract_status,
                'client_id' => $this->client_id,
                'owner_id' => $this->owner_id
            ]);
        }

        return $object;
    }

    public function salesPrograms($contracts)
    {
        foreach($contracts->data as $contract) {
            if($contract->program_id && $contract->program_id != '')
            {
                $syjonProgram = SyjonProgram::find($contract->program_id);
                if($syjonProgram)
                {
                    $contract->salesProgram = $syjonProgram;
                    $plan_exist = \Plan::where('sales_program', $syjonProgram->name_key)->first();
                    if($plan_exist && count($plan_exist->groups) > 0 ) {
                        $contract->verified_sales_program = true;
                    }else{
                        $contract->verified_sales_program = false;
                    }
                }else{
                    $contract->salesProgram = null;
                    $contract->verified_sales_program = false;
                }
            }else{
                $contract->salesProgram = null;
                $contract->verified_sales_program = false;
            }
        }

        return $contracts;
    }

    private function parseNettoBrutto($syjon_policy)
    {
        switch ($syjon_policy->policy_type_price){
            case 'Netto':
                return 1;
            case 'Netto50':
                return 3;
            case 'Brutto':
                return 2;
            default:
                return 0;
        }
    }
}
