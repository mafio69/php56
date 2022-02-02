<?php
/**
 * Created by PhpStorm.
 * User: webwizards
 * Date: 2018-11-07
 * Time: 13:06
 */

namespace Idea\Searcher;


use Auth;
use Clients;
use Idea\Structures\GETVEHICLEDTAInput;
use Input;
use Owners;
use Vehicles;
use VipClient;
use VmanageCompanyCsm;
use VmanageVehicle;
use Webservice;

class Searcher
{
    private $registration;
    private $nr_contract;
    private $vin;

    /**
     * Searcher constructor.
     * @param $registration
     * @param $nr_contract
     */
    public function __construct($registration = '', $nr_contract = '', $vin = '')
    {
        $this->registration = $registration;
        $this->nr_contract = $nr_contract;
        $this->vin = $vin;
    }

    public function searchVmanageVehicle()
    {
        $result = array();

        $vehicle = VmanageVehicle::where('outdated', 0)->where(function($query) {
            if($this->registration != '' && $this->registration)
                $query->where('registration', 'like', $this->registration);
            if($this->nr_contract!='' && $this->nr_contract)
                $query->orWhere('nr_contract','like',$this->nr_contract);
        })->whereHas('company', function($query){
            $query->whereHas('guardians', function($query){
                $query->where('users.id', Auth::user()->id);
            });
        })->first();

        if($vehicle)
        {
            $csm_contents = VmanageCompanyCsm::where('vmanage_company_id', $vehicle->vmanage_company_id)->whereNotNull('content')->where('content', '!=','')
                ->with('csmType')->get();
            if(! $csm_contents->isEmpty())
            {
                $csm_title = '';
                foreach($csm_contents as $content)
                {
                    $csm_title .= '<b>'.$content->csmType->name.': </b>'.$content->content.'<br/>';
                }
                $csm = ' <a tabindex="0" class="btn-popover " role="button" data-toggle="popover" data-trigger="focus" title="Info Flota"
                        data-content="'.$csm_title.'"><i class="fa fa-info-circle"></i></a>';

            }else{
                $csm = '';
            }
            $result[$vehicle->owner_id] = [
                'status' => 0,
                'id' => $vehicle->id,
                'owner_id' => $vehicle->owner_id,
                'owner' => Owners::find($vehicle->owner_id)->name,
                'client_id' => $vehicle->client_id,
                'client' => ($vehicle->client) ? $vehicle->client->name : '',
                'registration' => $vehicle->registration,
                'vin' => $vehicle->vin,
                'brand' => checkObjectIfNotNull($vehicle->brand, 'name'),
                'model' => checkObjectIfNotNull($vehicle->{'model'}, 'name'),
                'year_production' => $vehicle->year_production,
                'insurance_company_name' => '',
                'insurance_company_id' => null,
                'expire' => $vehicle->insurance_expire_date,
                'nr_policy' => '',
                'assistance' => (is_null($vehicle->assistance) || $vehicle->assistance == '')? null : 1,
                'assistance_name' => $vehicle->assistance,
                'end_leasing' => '',
                'contract_status' => '',
                'mileage' => $vehicle->declare_mileage,
                'engine' => checkObjectIfNotNull($vehicle->car_engine, 'name'),
                'insurance' => $vehicle->insurance,
                'contribution' => '',
                'cfm' => $vehicle->cfm,
                'nr_contract' => $vehicle->nr_contract,
                'first_registration' => $vehicle->first_registration,
                'vehicle_type' => 'VmanageVehicle',
                'vmanage_company_name' => $vehicle->company->owner->name.$csm.'<br/><small><i>('.$vehicle->company->name.')</i></small>',
                'register_as'   => 0,
                'if_vip'    => $vehicle->if_vip
            ];
        }else{
            if($this->registration!='' && $this->registration) {
                $vehicle = VmanageVehicle::where('outdated', 0)->where('registration', 'like', $this->registration)->first();
            }else{
                $vehicle = null;
            }

            if($vehicle) {
                $result[1] = array(
                    'status' => 1,
                    'des' => 'Pojazd istnieje w systemie, lecz nie posiadasz uprawnień do zarządzania flotą w której się znajduje ('.$vehicle->company->name.')'
                );
            }
        }

        return $result;
    }

    public function searchVmanageVehicles()
    {
        $result = array();

        $vehicles = VmanageVehicle::where('outdated', 0)->where(function($query) {
            if($this->registration != '' && $this->registration)
                $query->where('registration', 'like', $this->registration);
            if($this->nr_contract!='' && $this->nr_contract)
                $query->orWhere('nr_contract','like',$this->nr_contract);
            if($this->vin!='' && $this->vin)
                $query->orWhere('vin','like',$this->vin);
        })->whereHas('company', function($query){
            $query->whereHas('guardians', function($query){
                $query->where('users.id', Auth::user()->id);
            });
        })->limit(100)->withTrashed()->lists('id');

        $vehicles = VmanageVehicle::whereIn('id', $vehicles)->orderBy('registration')->orderBy('deleted_at')->orderBy('id', 'desc')->withTrashed()->get();

        $prev_vehicle = null;
        if($vehicles->count() > 0)
        {
            foreach($vehicles as $vehicle) {
                if(!$prev_vehicle || $vehicle->vin != $prev_vehicle->vin) {
                    $csm_contents = VmanageCompanyCsm::where('vmanage_company_id', $vehicle->vmanage_company_id)->whereNotNull('content')->where('content', '!=', '')
                        ->with('csmType', 'seller')->get();
                    if (!$csm_contents->isEmpty()) {
                        $csm_title = '';
                        foreach ($csm_contents as $content) {
                            $csm_title .= '<b>' . $content->csmType->name . ': </b>' . $content->content . '<br/>';
                        }
                        $csm = ' <a tabindex="0" class="btn-popover " role="button" data-toggle="popover" data-trigger="focus" title="Info Flota"
                        data-content="' . $csm_title . '"><i class="fa fa-info-circle"></i></a>';

                    } else {
                        $csm = '';
                    }
                    $result[] = [
                        'status' => 0,
                        'id' => $vehicle->id,
                        'owner_id' => $vehicle->owner_id,
                        'owner' => Owners::find($vehicle->owner_id)->name,
                        'client_id' => $vehicle->client_id,
                        'client' => ($vehicle->client) ? $vehicle->client->name : '',
                        'registration' => $vehicle->registration,
                        'vin' => $vehicle->vin,
                        'brand' => checkObjectIfNotNull($vehicle->brand, 'name'),
                        'model' => checkObjectIfNotNull($vehicle->{'model'}, 'name'),
                        'year_production' => $vehicle->year_production,
                        'insurance_company_name' => $vehicle->insurance_company()->first() ? $vehicle->insurance_company()->first()->name : '',
                        'insurance_company_id' => $vehicle->insurance_company_id,
                        'expire' => $vehicle->insurance_expire_date,
                        'nr_policy' => '',
                        'assistance' => (is_null($vehicle->assistance) || $vehicle->assistance == '') ? null : 1,
                        'assistance_name' => $vehicle->assistance,
                        'end_leasing' => $vehicle->end_leasing,
                        'contract_status' => $vehicle->contract_status,
                        'mileage' => $vehicle->declare_mileage,
                        'engine' => checkObjectIfNotNull($vehicle->car_engine, 'name'),
                        'insurance' => $vehicle->insurance,
                        'contribution' => '',
                        'cfm' => $vehicle->cfm,
                        'nr_contract' => $vehicle->nr_contract,
                        'first_registration' => $vehicle->first_registration,
                        'vehicle_type' => 'VmanageVehicle',
                        'vmanage_company_name' => $vehicle->company->owner->name . $csm . '<br/><small><i>(' . $vehicle->company->name . ')</i></small>',
                        'register_as' => 0,
                        'if_vip' => $vehicle->if_vip,
                        'is_trashed' => $vehicle->trashed(),
                        'seller' => $vehicle->seller ? $vehicle->seller->name : '',
                        'sales_program' => $vehicle->sales_program
                    ];
                }
                $prev_vehicle = $vehicle;
            }
        }else{
            if($this->registration!='' && $this->registration) {
                $vehicle = VmanageVehicle::where('outdated', 0)->where('registration', 'like', $this->registration)->first();
            }else{
                $vehicle = null;
            }

            if($vehicle) {
                $result = array(
                    'status' => 1,
                    'des' => 'Pojazd istnieje w systemie, lecz nie posiadasz uprawnień do zarządzania flotą w której się znajduje ('.$vehicle->company->name.')'
                );
            }
        }

        return $result;
    }

    public function searchNonAsVehicle()
    {
        $result = array();

        $vehicle = Vehicles::where(function($query) {
            if(!is_null($this->registration) && $this->registration != '') {
                $query->where('registration', 'like', $this->registration);
            }else {
                $query->where('nr_contract', 'like', $this->nr_contract);
            }
        })->where('register_as', 0)->latest()->first();

        if($vehicle)
        {
            $vip_client = VipClient::where('registration',$vehicle->registration)->first();
            $result[$vehicle->owner_id] = [
                'status' => 0,
                'id' => $vehicle->id,
                'owner_id' => $vehicle->owner_id,
                'owner' => ($vehicle->owner->old_name) ? $vehicle->owner->name.' ('.$vehicle->owner->old_name.')' : $vehicle->owner->name,
                'client_id' => $vehicle->client_id,
                'client' => ($vehicle->client) ? $vehicle->client->name : '',
                'registration' => $vehicle->registration,
                'vin' => $vehicle->VIN,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'engine' => $vehicle->engine,
                'year_production' => $vehicle->year_production,
                'insurance_company_name' => '',
                'insurance_company_id' => $vehicle->insurance_company_id,
                'insurance' => $vehicle->insurance,
                'insurance_expire' => $vehicle->insurance_expire_date,
                'nr_policy' => '',
                'assistance' => (is_null($vehicle->assistance) || $vehicle->assistance == '')? null : 1,
                'assistance_name' => $vehicle->assistance,
                'nr_contract' => $vehicle->nr_contract,
                'end_leasing' => $vehicle->end_leasing,
                'expire' => $vehicle->end_leasing,
                'contract_status' => $vehicle->contract_status,
                'mileage' => $vehicle->mileage,
                'insurance' => $vehicle->insurance,
                'contribution' => $vehicle->contribution,
                'cfm' => $vehicle->cfm,
                'first_registration' => $vehicle->first_registration,
                'vehicle_type' => 'Vehicles',
                'register_as'   => $vehicle->register_as,
                'if_vip' => ($vip_client) ? 1 : 0,
            ];
        }

        return $result;
    }

    public function searchNonAsVehicles()
    {
        $result = array();

        $vehicles = Vehicles::where(function($query) {
            if($this->registration != '' && $this->registration)
                $query->where('registration', 'like', $this->registration);
            if($this->nr_contract!='' && $this->nr_contract)
                $query->orWhere('nr_contract','like',$this->nr_contract);
            if($this->vin!='' && $this->vin)
                $query->orWhere('VIN','like',$this->vin);
        })->where('register_as', 0)
        ->whereIn('id',
            \DB::table('vehicles')
                ->select(\DB::raw('MAX(id) as id'))
                ->where(function($query) {
                    if($this->registration != '' && $this->registration)
                        $query->where('registration', 'like', $this->registration);
                    if($this->nr_contract!='' && $this->nr_contract)
                        $query->orWhere('nr_contract','like',$this->nr_contract);
                    if($this->vin!='' && $this->vin)
                        $query->orWhere('VIN','like',$this->vin);
                })
                ->where('register_as', 0)
                ->groupBy('nr_contract')
                ->lists('id')
        )->limit(100)->get();

        if($vehicles->count() > 0)
        {
            foreach($vehicles as $vehicle) {
                $vip_client = VipClient::where('registration', $vehicle->registration)->first();
                $result[] = [
                    'status' => 0,
                    'id' => $vehicle->id,
                    'owner_id' => $vehicle->owner_id,
                    'owner' => ($vehicle->owner->old_name) ? $vehicle->owner->name . ' (' . $vehicle->owner->old_name . ')' : $vehicle->owner->name,
                    'client_id' => $vehicle->client_id,
                    'client' => ($vehicle->client) ? $vehicle->client->name : '',
                    'registration' => $vehicle->registration,
                    'vin' => $vehicle->VIN,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'engine' => $vehicle->engine,
                    'year_production' => $vehicle->year_production,
                    'insurance_company_name' => '',
                    'insurance_company_id' => $vehicle->insurance_company_id,
                    'insurance' => $vehicle->insurance,
                    'insurance_expire' => $vehicle->insurance_expire_date,
                    'nr_policy' => '',
                    'assistance' => (is_null($vehicle->assistance) || $vehicle->assistance == '') ? null : 1,
                    'assistance_name' => $vehicle->assistance,
                    'nr_contract' => $vehicle->nr_contract,
                    'end_leasing' => $vehicle->end_leasing,
                    'expire' => $vehicle->end_leasing,
                    'contract_status' => $vehicle->contract_status,
                    'mileage' => $vehicle->mileage,
                    'contribution' => $vehicle->contribution,
                    'cfm' => $vehicle->cfm,
                    'first_registration' => $vehicle->first_registration,
                    'vehicle_type' => 'Vehicles',
                    'register_as' => $vehicle->register_as,
                    'if_vip' => ($vip_client) ? 1 : 0,
                ];
            }
        }

        return $result;
    }

    public function searchASSVehicle()
    {
        $username = substr( Auth::user()->login, 0, 10);
        $data = new GETVEHICLEDTAInput($this->nr_contract, $this->registration, $username);

        $owners = Owners::whereActive('0')->where('wsdl', '!=', '')->get();

        $result = array();

        foreach($owners as $owner) {
            if( !isset($owner_id) || (isset($owner_id) && $result[$owner_id]['status'] != 0) ) {
                $owner_id = $owner->id;

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('getvehicledta_XML');

                $xml = $webservice->getResponseXML();

                $errorCode = $xml->ANSWER->getVehicleDataReturn->Error->ErrorCde;

                if ($errorCode == 'ERR0000') {
                    $xml = $xml->ANSWER->getVehicleDataReturn->getVehicle;

                    $client = Clients::where('NIP', '=', trim(str_replace('-', '', $xml->customer->NIP->__toString())))->where('REGON', '=', trim($xml->customer->REGON->__toString()))
                        ->where('active', '=', '0')->orderBy('parent_id', 'desc')->first();
                    if (!$client) {
                        //brak właściciela w bazie
                        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

                        $registry_post = $xml->customer->address->postalCode->__toString();
                        if (strlen($registry_post) == 6) {
                            $registry_voivodeship_id = $matcher->match($registry_post);
                        } else {
                            $registry_voivodeship_id = null;
                        }

                        $correspond_post = $xml->customer->mailAddress->postalCode->__toString();
                        if (strlen($correspond_post) == 6) {
                            $correspond_voivodeship_id = $matcher->match($correspond_post);
                        } else {
                            $correspond_voivodeship_id = null;
                        }


                        $client = Clients::create(array(
                            'firmID' => $xml->customer->firmID->__toString(),
                            'name' => $xml->customer->name->__toString(),
                            'NIP' => trim(str_replace('-', '', $xml->customer->NIP->__toString())),
                            'REGON' => trim($xml->customer->REGON->__toString()),
                            'registry_post' => $xml->customer->address->postalCode->__toString(),
                            'registry_city' => $xml->customer->address->city->__toString(),
                            'registry_street' => $xml->customer->address->street->__toString(),
                            'registry_voivodeship_id' => $registry_voivodeship_id,
                            'correspond_post' => $xml->customer->mailAddress->postalCode->__toString(),
                            'correspond_city' => $xml->customer->mailAddress->city->__toString(),
                            'correspond_street' => $xml->customer->mailAddress->street->__toString(),
                            'correspond_voivodeship_id' => $correspond_voivodeship_id,
                            'phone' => $xml->customer->phone->__toString(),
                            'email' => $xml->customer->email->__toString()
                        ));
                        $client_id = $client->id;

                    } else {
                        $client_id = $this->compareClients($client, $xml);
                    }

                    $insurance_company_name = trim($xml->policy->insCompany->__toString());


                    if ($xml->policy->assistance->__toString() == 'N') $assistance = 0;
                    else if ($xml->policy->assistance->__toString() == 'T') $assistance = 1;

                    $vehicle = Vehicles::where('nr_contract', '=', trim($xml->contract->number->__toString()))->orderBy('parent_id', 'desc')->get();
                    $exist_in_base = 0;
                    $pos = strpos($xml->vehicleData->description->__toString(), ' ');
                    $brand = trim(substr($xml->vehicleData->description->__toString(), 0, $pos));
                    $model = trim(substr($xml->vehicleData->description->__toString(), $pos));

                    $lessor = $xml->contract->lessor->__toString();
                    if($lessor == 'SKA')
                    {
                        $owner_id = 2;
                    }else{
                        $owner_id = 1;
                    }

                    if (count($vehicle) == 0) {
                        //samochód nie istnieje w bazie

                        $vehicle = Vehicles::create(array(
                            'owner_id' => $owner_id,
                            'client_id' => $client_id,
                            'registration' => $xml->vehicleData->regNumber->__toString(),
                            'VIN' => $xml->vehicleData->VIN->__toString(),
                            'brand' => $brand,
                            'model' => $model,
                            'year_production' => $xml->vehicleData->year->__toString(),
                            'insurance_company_name' => $insurance_company_name,
                            'expire' => $xml->policy->expDate->__toString(),
                            'nr_policy' => $xml->policy->policyNumber->__toString(),
                            'assistance' => $assistance,
                            'assistance_name' => $xml->policy->version->__toString(),
                            'nr_contract' => $xml->contract->number->__toString(),
                            'end_leasing' => $xml->contract->endDate->__toString(),
                            'contract_status' => $xml->contract->status->__toString(),
                            'cfm' => 0,
                            'register_as' => 1
                        ));

                        $id_vehicle = $vehicle->id;
                        $vehicle = Vehicles::find($id_vehicle);

                    } else {
                        $vehicle_base = $vehicle->first();
                        $exist_in_base = 1;

                        if ($vehicle_base->registration != $xml->vehicleData->regNumber->__toString() || $vehicle_base->nr_contract != $xml->contract->number->__toString()) {
                            $updated_vehicle = $vehicle_base->toArray();
                            $updated_vehicle['registration'] = $xml->vehicleData->regNumber->__toString();
                            $updated_vehicle['nr_contract'] = $xml->contract->number->__toString();
                            $updated_vehicle['parent_id'] = $vehicle_base->id;
                            $vehicle_base = Vehicles::create($updated_vehicle);
                        }

                        $vehicle = array();
                        $vehicle['id'] = $vehicle_base->id;
                        $vehicle['owner_id'] = $owner_id;
                        $vehicle['client_id'] = $client_id;
                        $vehicle['registration'] = $xml->vehicleData->regNumber->__toString();
                        $vehicle['VIN'] = $xml->vehicleData->VIN->__toString();
                        $vehicle['brand'] = $brand;
                        $vehicle['model'] = $model;
                        $vehicle['year_production'] = $xml->vehicleData->year->__toString();
                        $vehicle['insurance_company_name'] = $insurance_company_name;
                        $vehicle['insurance_company_id'] = $vehicle_base->insurance_company_id;
                        $vehicle['policy_insurance_company_id'] = $vehicle_base->policy_insurance_company_id;
                        $vehicle['expire'] = $xml->policy->expDate->__toString();
                        $vehicle['nr_policy'] = $xml->policy->policyNumber->__toString();
                        $vehicle['assistance'] = $assistance;
                        $vehicle['assistance_name'] = $xml->policy->version->__toString();
                        $vehicle['nr_contract'] = $xml->contract->number->__toString();
                        $vehicle['end_leasing'] = $xml->contract->endDate->__toString();
                        $vehicle['contract_status'] = $xml->contract->status->__toString();
                        $vehicle['cfm'] = $vehicle_base->cfm;

                    }
                    //$result = json_decode(json_encode((array) $xml), 1);

                    if ($exist_in_base == 1) {
                        $vip_client = VipClient::where('registration',$vehicle['registration'])->first();

                        $result[$owner_id] = array(
                            'status' => 0,
                            'id' => $vehicle['id'],
                            'owner_id' => $vehicle['owner_id'],
                            'owner' => (Owners::find($vehicle['owner_id'])->old_name) ? Owners::find($vehicle['owner_id'])->name.' ('.Owners::find($vehicle['owner_id'])->old_name.')' : Owners::find($vehicle['owner_id'])->name,
                            'client_id' => $vehicle['client_id'],
                            'client' => Clients::find($vehicle['client_id'])->name,
                            'registration' => $vehicle['registration'],
                            'vin' => $vehicle['VIN'],
                            'brand' => $vehicle['brand'],
                            'model' => $vehicle['model'],
                            'year_production' => $vehicle['year_production'],
                            'insurance_company_name' => $vehicle['insurance_company_name'],
                            'insurance_company_id' => $vehicle['insurance_company_id'],
                            'insurance_expire' => $vehicle['expire'],
                            'nr_policy' => $vehicle['nr_policy'],
                            'assistance' => $vehicle['assistance'],
                            'assistance_name' => $vehicle['assistance_name'],
                            'nr_contract' => $vehicle['nr_contract'],
                            'end_leasing' => $vehicle['end_leasing'],
                            'expire' => $vehicle['end_leasing'],
                            'contract_status' => $vehicle['contract_status'],
                            'mileage' => $vehicle_base['mileage'],
                            'engine' => $vehicle_base['engine'],
                            'insurance' => $vehicle_base['insurance'],
                            'contribution' => $vehicle_base['contribution'],
                            'cfm' => $vehicle_base['cfm'],
                            'vehicle_type' => 'Vehicles',
                            'if_vip' => ($vip_client) ? 1 : 0,
                        );
                        if ($vehicle_base['first_registration'] != '0000-00-00' && $vehicle_base['first_registration'] != NULL)
                            $result[$owner_id]['first_registration'] = $vehicle_base['first_registration'];


                        if ($vehicle['owner_id'] != $vehicle_base['owner_id'] && $vehicle_base['owner_id'] != 0) {
                            $result[$owner_id]['owner_system'] = (Owners::find($vehicle_base['owner_id'])->old_name) ? Owners::find($vehicle_base['owner_id'])->name.' ('.Owners::find($vehicle_base['owner_id'])->old_name.')' : Owners::find($vehicle_base['owner_id'])->name;
                        }
                        if ($vehicle['client_id'] != $vehicle_base['client_id'] && $vehicle_base['client_id'] != 0) {
                            $result[$owner_id]['client_system'] = Clients::find($vehicle_base['client_id'])->name;
                        }
                        if (mb_strtoupper($vehicle['VIN'], 'UTF-8') != mb_strtoupper($vehicle_base['VIN'], 'UTF-8'))
                            $result[$owner_id]['vin_system'] = $vehicle_base['VIN'];
                        if (mb_strtoupper($vehicle['brand'], 'UTF-8') != mb_strtoupper($vehicle_base['brand'], 'UTF-8'))
                            $result[$owner_id]['brand_system'] = $vehicle_base['brand'];
                        if (mb_strtoupper($vehicle['model'], 'UTF-8') != mb_strtoupper($vehicle_base['model'], 'UTF-8'))
                            $result[$owner_id]['model_system'] = $vehicle_base['model'];
                        if (mb_strtoupper($vehicle['year_production'], 'UTF-8') != mb_strtoupper($vehicle_base['year_production'], 'UTF-8'))
                            $result[$owner_id]['year_production_system'] = $vehicle_base['year_production'];
                        if (mb_strtoupper($vehicle['insurance_company_name'], 'UTF-8') != mb_strtoupper($vehicle_base['insurance_company_name'], 'UTF-8') && $vehicle_base['insurance_company_name'] != NULL) {
                            $result[$owner_id]['insurance_company_name_system'] = mb_strtoupper($vehicle_base['insurance_company_name'], 'UTF-8');
                        }
                        if ($vehicle['expire'] != $vehicle_base['expire'])
                            $result[$owner_id]['expire_system'] = $vehicle_base['expire'];
                        if ($vehicle['nr_policy'] != $vehicle_base['nr_policy'])
                            $result[$owner_id]['nr_policy_system'] = $vehicle_base['nr_policy'];
                        if (mb_strtoupper($vehicle['assistance'], 'UTF-8') != mb_strtoupper($vehicle_base['assistance'], 'UTF-8'))
                            $result[$owner_id]['assistance_system'] = $vehicle_base['assistance'];
                        if (mb_strtoupper($vehicle['assistance_name'], 'UTF-8') != mb_strtoupper($vehicle_base['assistance_name'], 'UTF-8'))
                            $result[$owner_id]['assistance_name_system'] = $vehicle_base['assistance_name'];
                        if (mb_strtoupper($vehicle['nr_contract'], 'UTF-8') != mb_strtoupper($vehicle_base['nr_contract'], 'UTF-8'))
                            $result[$owner_id]['nr_contract_system'] = $vehicle_base['nr_contract'];


                    } else {
                        $vip_client = VipClient::where('registration',$vehicle->registration)->first();

                        $result[$owner_id] = array(
                            'status' => 0,
                            "id" => $vehicle->id,
                            "label" => $vehicle->registration . ' ' . $vehicle->brand . ' - ' . $vehicle->model,
                            "value" => $vehicle->registration,
                            "registration" => $vehicle->registration,
                            'vin' => $vehicle->VIN,
                            'nr_contract' => $vehicle->nr_contract,
                            'brand' => $vehicle->brand,
                            'model' => $vehicle->model,
                            'engine' => $vehicle->engine,
                            'expire' => $vehicle->expire,
                            'client_id' => $vehicle->client_id,
                            'client' => $vehicle->client()->first()->name,
                            'year_production' => $vehicle->year_production,
                            'first_registration' => $vehicle->first_registration,
                            'mileage' => $vehicle->mileage,
                            'owner_id' => $vehicle->owner_id,
                            'owner' => ($vehicle->owner()->first()->old_name) ? $vehicle->owner()->first()->name.' ('.$vehicle->owner()->first()->old_name.')' : $vehicle->owner()->first()->name,
                            'end_leasing' => $vehicle->end_leasing,
                            'insurance_company_name' => $vehicle->insurance_company_name,
                            'insurance_company_id' => '0',
                            'assistance' => $vehicle->assistance,
                            'assistance_name' => $vehicle->assistance_name,
                            'nr_policy' => $vehicle->nr_policy,
                            'contract_status' => $xml->contract->status->__toString(),
                            'cfm' => $vehicle->cfm,
                            'vehicle_type' => 'Vehicles',
                            'if_vip' => ($vip_client) ? 1 : 0,

                        );
                    }
                    $result[$owner_id]['register_as'] = 1;

                } else if ($errorCode == 'ERR0003') {
                    //brak umowy o zadanych parametrach
                    $result[$owner_id] = array(
                        'status' => 1,
                        'des' => $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString()
                    );
                } else {
                    //pojawił się błąd
                    $result[$owner_id] = array(
                        'status' => 2,
                        'des' => $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString()
                    );
                }
            }
        }

        return $result;
    }

    public function searchAsVehicles()
    {
        $username = substr( Auth::user()->login, 0, 10);
        $data = new GETVEHICLEDTAInput($this->nr_contract, $this->registration, $username);

        $owners = Owners::whereActive('0')->where('wsdl', '!=', '')->get();

        $result = array();

        foreach($owners as $owner) {
            $owner_id = $owner->id;

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('getvehicledta_XML');

            $xml = $webservice->getResponseXML();

            $errorCode = $xml->ANSWER->getVehicleDataReturn->Error->ErrorCde;

            if ($errorCode == 'ERR0000') {
                $xml = $xml->ANSWER->getVehicleDataReturn->getVehicle;

                $client = Clients::where('NIP', '=', trim(str_replace('-', '', $xml->customer->NIP->__toString())))->where('REGON', '=', trim($xml->customer->REGON->__toString()))
                    ->where('active', '=', '0')->orderBy('parent_id', 'desc')->first();
                if (!$client) {
                    //brak właściciela w bazie
                    $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

                    $registry_post = $xml->customer->address->postalCode->__toString();
                    if (strlen($registry_post) == 6) {
                        $registry_voivodeship_id = $matcher->match($registry_post);
                    } else {
                        $registry_voivodeship_id = null;
                    }

                    $correspond_post = $xml->customer->mailAddress->postalCode->__toString();
                    if (strlen($correspond_post) == 6) {
                        $correspond_voivodeship_id = $matcher->match($correspond_post);
                    } else {
                        $correspond_voivodeship_id = null;
                    }


                    $client = Clients::create(array(
                        'firmID' => $xml->customer->firmID->__toString(),
                        'name' => $xml->customer->name->__toString(),
                        'NIP' => trim(str_replace('-', '', $xml->customer->NIP->__toString())),
                        'REGON' => trim($xml->customer->REGON->__toString()),
                        'registry_post' => $xml->customer->address->postalCode->__toString(),
                        'registry_city' => $xml->customer->address->city->__toString(),
                        'registry_street' => $xml->customer->address->street->__toString(),
                        'registry_voivodeship_id' => $registry_voivodeship_id,
                        'correspond_post' => $xml->customer->mailAddress->postalCode->__toString(),
                        'correspond_city' => $xml->customer->mailAddress->city->__toString(),
                        'correspond_street' => $xml->customer->mailAddress->street->__toString(),
                        'correspond_voivodeship_id' => $correspond_voivodeship_id,
                        'phone' => $xml->customer->phone->__toString(),
                        'email' => $xml->customer->email->__toString()
                    ));
                    $client_id = $client->id;

                } else {
                    $client_id = $this->compareClients($client, $xml);
                }

                $insurance_company_name = trim($xml->policy->insCompany->__toString());


                if ($xml->policy->assistance->__toString() == 'N') $assistance = 0;
                else if ($xml->policy->assistance->__toString() == 'T') $assistance = 1;

                $vehicle = Vehicles::where('nr_contract', '=', trim($xml->contract->number->__toString()))->orderBy('parent_id', 'desc')->get();
                $exist_in_base = 0;
                $pos = strpos($xml->vehicleData->description->__toString(), ' ');
                $brand = trim(substr($xml->vehicleData->description->__toString(), 0, $pos));
                $model = trim(substr($xml->vehicleData->description->__toString(), $pos));

                $lessor = $xml->contract->lessor->__toString();
                if($lessor == 'SKA')
                {
                    $owner_id = 2;
                }else{
                    $owner_id = 1;
                }

                if (count($vehicle) == 0) {
                    //samochód nie istnieje w bazie

                    $vehicle = Vehicles::create(array(
                        'owner_id' => $owner_id,
                        'client_id' => $client_id,
                        'registration' => $xml->vehicleData->regNumber->__toString(),
                        'VIN' => $xml->vehicleData->VIN->__toString(),
                        'brand' => $brand,
                        'model' => $model,
                        'year_production' => $xml->vehicleData->year->__toString(),
                        'insurance_company_name' => $insurance_company_name,
                        'expire' => $xml->policy->expDate->__toString(),
                        'nr_policy' => $xml->policy->policyNumber->__toString(),
                        'assistance' => $assistance,
                        'assistance_name' => $xml->policy->version->__toString(),
                        'nr_contract' => $xml->contract->number->__toString(),
                        'end_leasing' => $xml->contract->endDate->__toString(),
                        'contract_status' => $xml->contract->status->__toString(),
                        'cfm' => 0,
                        'register_as' => 1
                    ));

                    $id_vehicle = $vehicle->id;
                    $vehicle = Vehicles::find($id_vehicle);

                } else {
                    $vehicle_base = $vehicle->first();
                    $exist_in_base = 1;

                    if ($vehicle_base->registration != $xml->vehicleData->regNumber->__toString() || $vehicle_base->nr_contract != $xml->contract->number->__toString()) {
                        $updated_vehicle = $vehicle_base->toArray();
                        $updated_vehicle['registration'] = $xml->vehicleData->regNumber->__toString();
                        $updated_vehicle['nr_contract'] = $xml->contract->number->__toString();
                        $updated_vehicle['parent_id'] = $vehicle_base->id;
                        $vehicle_base = Vehicles::create($updated_vehicle);
                    }

                    $vehicle = array();
                    $vehicle['id'] = $vehicle_base->id;
                    $vehicle['owner_id'] = $owner_id;
                    $vehicle['client_id'] = $client_id;
                    $vehicle['registration'] = $xml->vehicleData->regNumber->__toString();
                    $vehicle['VIN'] = $xml->vehicleData->VIN->__toString();
                    $vehicle['brand'] = $brand;
                    $vehicle['model'] = $model;
                    $vehicle['year_production'] = $xml->vehicleData->year->__toString();
                    $vehicle['insurance_company_name'] = $insurance_company_name;
                    $vehicle['insurance_company_id'] = $vehicle_base->insurance_company_id;
                    $vehicle['policy_insurance_company_id'] = $vehicle_base->policy_insurance_company_id;
                    $vehicle['expire'] = $xml->policy->expDate->__toString();
                    $vehicle['nr_policy'] = $xml->policy->policyNumber->__toString();
                    $vehicle['assistance'] = $assistance;
                    $vehicle['assistance_name'] = $xml->policy->version->__toString();
                    $vehicle['nr_contract'] = $xml->contract->number->__toString();
                    $vehicle['end_leasing'] = $xml->contract->endDate->__toString();
                    $vehicle['contract_status'] = $xml->contract->status->__toString();
                    $vehicle['cfm'] = $vehicle_base->cfm;

                }
                //$result = json_decode(json_encode((array) $xml), 1);

                if ($exist_in_base == 1) {
                    $vip_client = VipClient::where('registration',$vehicle['registration'])->first();

                    $result[$owner_id] = array(
                        'status' => 0,
                        'id' => $vehicle['id'],
                        'owner_id' => $vehicle['owner_id'],
                        'owner' => (Owners::find($vehicle['owner_id'])->old_name) ? Owners::find($vehicle['owner_id'])->name.' ('.Owners::find($vehicle['owner_id'])->old_name.')' : Owners::find($vehicle['owner_id'])->name,
                        'client_id' => $vehicle['client_id'],
                        'client' => Clients::find($vehicle['client_id'])->name,
                        'registration' => $vehicle['registration'],
                        'vin' => $vehicle['VIN'],
                        'brand' => $vehicle['brand'],
                        'model' => $vehicle['model'],
                        'year_production' => $vehicle['year_production'],
                        'insurance_company_name' => $vehicle['insurance_company_name'],
                        'insurance_company_id' => $vehicle['insurance_company_id'],
                        'insurance_expire' => $vehicle['expire'],
                        'nr_policy' => $vehicle['nr_policy'],
                        'assistance' => $vehicle['assistance'],
                        'assistance_name' => $vehicle['assistance_name'],
                        'nr_contract' => $vehicle['nr_contract'],
                        'end_leasing' => $vehicle['end_leasing'],
                        'expire' => $vehicle['end_leasing'],
                        'contract_status' => $vehicle['contract_status'],
                        'mileage' => $vehicle_base['mileage'],
                        'engine' => $vehicle_base['engine'],
                        'insurance' => $vehicle_base['insurance'],
                        'contribution' => $vehicle_base['contribution'],
                        'cfm' => $vehicle_base['cfm'],
                        'vehicle_type' => 'Vehicles',
                        'if_vip' => ($vip_client) ? 1 : 0,
                    );
                    if ($vehicle_base['first_registration'] != '0000-00-00' && $vehicle_base['first_registration'] != NULL)
                        $result[$owner_id]['first_registration'] = $vehicle_base['first_registration'];


                    if ($vehicle['owner_id'] != $vehicle_base['owner_id'] && $vehicle_base['owner_id'] != 0) {
                        $result[$owner_id]['owner_system'] = (Owners::find($vehicle_base['owner_id'])->old_name) ? Owners::find($vehicle_base['owner_id'])->name.' ('.Owners::find($vehicle_base['owner_id'])->old_name.')' : Owners::find($vehicle_base['owner_id'])->name;
                    }
                    if ($vehicle['client_id'] != $vehicle_base['client_id'] && $vehicle_base['client_id'] != 0) {
                        $result[$owner_id]['client_system'] = Clients::find($vehicle_base['client_id'])->name;
                    }
                    if (mb_strtoupper($vehicle['VIN'], 'UTF-8') != mb_strtoupper($vehicle_base['VIN'], 'UTF-8'))
                        $result[$owner_id]['vin_system'] = $vehicle_base['VIN'];
                    if (mb_strtoupper($vehicle['brand'], 'UTF-8') != mb_strtoupper($vehicle_base['brand'], 'UTF-8'))
                        $result[$owner_id]['brand_system'] = $vehicle_base['brand'];
                    if (mb_strtoupper($vehicle['model'], 'UTF-8') != mb_strtoupper($vehicle_base['model'], 'UTF-8'))
                        $result[$owner_id]['model_system'] = $vehicle_base['model'];
                    if (mb_strtoupper($vehicle['year_production'], 'UTF-8') != mb_strtoupper($vehicle_base['year_production'], 'UTF-8'))
                        $result[$owner_id]['year_production_system'] = $vehicle_base['year_production'];
                    if (mb_strtoupper($vehicle['insurance_company_name'], 'UTF-8') != mb_strtoupper($vehicle_base['insurance_company_name'], 'UTF-8') && $vehicle_base['insurance_company_name'] != NULL) {
                        $result[$owner_id]['insurance_company_name_system'] = mb_strtoupper($vehicle_base['insurance_company_name'], 'UTF-8');
                    }
                    if ($vehicle['expire'] != $vehicle_base['expire'])
                        $result[$owner_id]['expire_system'] = $vehicle_base['expire'];
                    if ($vehicle['nr_policy'] != $vehicle_base['nr_policy'])
                        $result[$owner_id]['nr_policy_system'] = $vehicle_base['nr_policy'];
                    if (mb_strtoupper($vehicle['assistance'], 'UTF-8') != mb_strtoupper($vehicle_base['assistance'], 'UTF-8'))
                        $result[$owner_id]['assistance_system'] = $vehicle_base['assistance'];
                    if (mb_strtoupper($vehicle['assistance_name'], 'UTF-8') != mb_strtoupper($vehicle_base['assistance_name'], 'UTF-8'))
                        $result[$owner_id]['assistance_name_system'] = $vehicle_base['assistance_name'];
                    if (mb_strtoupper($vehicle['nr_contract'], 'UTF-8') != mb_strtoupper($vehicle_base['nr_contract'], 'UTF-8'))
                        $result[$owner_id]['nr_contract_system'] = $vehicle_base['nr_contract'];


                } else {
                    $vip_client = VipClient::where('registration',$vehicle->registration)->first();

                    $result[$owner_id] = array(
                        'status' => 0,
                        "id" => $vehicle->id,
                        "label" => $vehicle->registration . ' ' . $vehicle->brand . ' - ' . $vehicle->model,
                        "value" => $vehicle->registration,
                        "registration" => $vehicle->registration,
                        'vin' => $vehicle->VIN,
                        'nr_contract' => $vehicle->nr_contract,
                        'brand' => $vehicle->brand,
                        'model' => $vehicle->model,
                        'engine' => $vehicle->engine,
                        'expire' => $vehicle->expire,
                        'client_id' => $vehicle->client_id,
                        'client' => $vehicle->client()->first()->name,
                        'year_production' => $vehicle->year_production,
                        'first_registration' => $vehicle->first_registration,
                        'mileage' => $vehicle->mileage,
                        'owner_id' => $vehicle->owner_id,
                        'owner' => ($vehicle->owner()->first()->old_name) ? $vehicle->owner()->first()->name.' ('.$vehicle->owner()->first()->old_name.')' : $vehicle->owner()->first()->name,
                        'end_leasing' => $vehicle->end_leasing,
                        'insurance_company_name' => $vehicle->insurance_company_name,
                        'insurance_company_id' => '0',
                        'assistance' => $vehicle->assistance,
                        'assistance_name' => $vehicle->assistance_name,
                        'nr_policy' => $vehicle->nr_policy,
                        'contract_status' => $xml->contract->status->__toString(),
                        'cfm' => $vehicle->cfm,
                        'vehicle_type' => 'Vehicles',
                        'if_vip' => ($vip_client) ? 1 : 0,

                    );
                }
                $result[$owner_id]['register_as'] = 1;

            } else if ($errorCode == 'ERR0003') {
                //brak umowy o zadanych parametrach
//                    $result[$owner_id] = array(
//                        'status' => 1,
//                        'des' => $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString()
//                    );
            } else {
                //pojawił się błąd
//                    $result[$owner_id] = array(
//                        'status' => 2,
//                        'des' => $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString()
//                    );
            }
        }

        return $result;
    }

    public function searchObjects()
    {
        $result = array();

        $objects = \Objects::where('nr_contract', 'like', $this->nr_contract)
            ->whereIn('id',
                \DB::table('objects')
                    ->select(\DB::raw('MAX(id) as id'))
                    ->where('nr_contract', 'like', $this->nr_contract)
                    ->groupBy('nr_contract')
                    ->lists('id')
            )->limit(100)->get();

        if ($objects->count() > 0) {
            foreach ($objects as $object) {
                $result[] = [
                    'status' => 0,
                    'id' => $object->id,
                    'owner_id' => $object->owner_id,
                    'owner' => ($object->owner->old_name) ? $object->owner->name . ' (' . $object->owner->old_name . ')' : $object->owner->name,
                    'client_id' => $object->client_id,
                    'client' => ($object->client) ? $object->client->name : '',
                    'description' => $object->description,
                    'factoryNbr' => $object->factoryNbr,
                    'assetType' => checkObjectIfNotNull($object->assetType, 'name'),
                    'year_production' => $object->year_production,
                    'insurance_company_name' => $object->insurance_company()->first() ? $object->insurance_company()->first()->name : '',
                    'insurance_company_id' => $object->insurance_company_id,
                    'insurance' => $object->insurance,
                    'expire' => $object->insurance_expire_date,
                    'nr_policy' => $object->nr_policy,
                    'nr_contract' => $object->nr_contract,
                    'end_leasing' => $object->end_leasing,
                    'contract_status' => $object->contract_status,
                    'contribution' => $object->contribution
                ];
            }
        }

        return $result;
    }


    private function compareClients($client, $xml)
    {
        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

        $registry_post = $xml->customer->address->postalCode->__toString();
        if(strlen($registry_post) == 6)
        {
            $registry_voivodeship_id = $matcher->match($registry_post);
        }else{
            $registry_voivodeship_id = null;
        }

        $correspond_post = $xml->customer->mailAddress->postalCode->__toString();
        if(strlen($correspond_post) == 6)
        {
            $correspond_voivodeship_id = $matcher->match($correspond_post);
        }else{
            $correspond_voivodeship_id = null;
        }

        if(
            $client->firmID != $xml->customer->firmID->__toString() ||
            $client->name != $xml->customer->name->__toString() ||
            $client->NIP != trim(str_replace('-', '', $xml->customer->NIP->__toString())) ||
            $client->REGON != trim($xml->customer->REGON->__toString()) ||
            $client->registry_post != $xml->customer->address->postalCode->__toString() ||
            $client->registry_city != $xml->customer->address->city->__toString() ||
            $client->registry_street != $xml->customer->address->street->__toString() ||
            $client->registry_voivodeship_id != $registry_voivodeship_id ||
            $client->correspond_post != $xml->customer->mailAddress->postalCode->__toString() ||
            $client->correspond_city != $xml->customer->mailAddress->city->__toString() ||
            $client->correspond_street != $xml->customer->mailAddress->street->__toString() ||
            $client->correspond_voivodeship_id != $correspond_voivodeship_id ||
            $client->phone != $xml->customer->phone->__toString() ||
            $client->email != $xml->customer->email->__toString()
        ){
            $client = Clients::create(array(
                'name' => $xml->customer->name->__toString(),
                'firmID' => $xml->customer->firmID->__toString(),
                'NIP' => trim(str_replace('-', '', $xml->customer->NIP->__toString())),
                'REGON' => trim($xml->customer->REGON->__toString()),
                'registry_post' => $xml->customer->address->postalCode->__toString(),
                'registry_city' => $xml->customer->address->city->__toString(),
                'registry_street' => $xml->customer->address->street->__toString(),
                'registry_voivodeship_id' => $registry_voivodeship_id,
                'correspond_post' => $xml->customer->mailAddress->postalCode->__toString(),
                'correspond_city' => $xml->customer->mailAddress->city->__toString(),
                'correspond_street' => $xml->customer->mailAddress->street->__toString(),
                'correspond_voivodeship_id' => $correspond_voivodeship_id,
                'phone' => $xml->customer->phone->__toString(),
                'email' => $xml->customer->email->__toString()
            ));
        }

        return $client->id;
    }

    public function searchApiVehicles()
    {
        $results = $this->searchInSyjon();
        if($results) return $results;

        $results = [];

        $vmanageVehicles = VmanageVehicle::where(function($query){
            if($this->registration && $this->registration != ''){
                $query->where('registration', $this->registration);
            }
            if($this->nr_contract && $this->nr_contract != ''){
                $query->where('nr_contract', $this->nr_contract);
            }
            if($this->vin && $this->vin != ''){
                $query->where('vin', $this->vin);
            }
        })->where('outdated', 0)->with('brand', 'model', 'owner', 'policyInsuranceCompany', 'salesProgram')->get();

        foreach($vmanageVehicles as $vehicle){
            $results[] = [
                'vehicle_id' => $vehicle->id,
                'vehicle_type' => '1',
                'registration' => $vehicle->registration,
                'brand' => $vehicle->brand ? $vehicle->brand->name : '',
                'model' => $vehicle->{'model'} ? $vehicle->{'model'}->name : '',
                'vin' => $vehicle->vin,
                'year_production' => $vehicle->year_production,
                'owner' => $vehicle->owner->name,
                'sales_program' => $vehicle->salesProgram ? $vehicle->salesProgram->name_key : '',
                'contract_number' => $vehicle->nr_contract,
                'end_leasing' => $vehicle->end_leasing,
                'insurance_company' => $vehicle->policyInsuranceCompany ? $vehicle->policyInsuranceCompany->name : '',
                'insurance_expire_date' => $vehicle->insurance_expire_date,
                'policy_number' => $vehicle->nr_policy
            ];
        }

        if(count($results) > 0) return $results;

        $results = [];

        $vehicles = Vehicles::where(function($query){
            if($this->registration && $this->registration != ''){
                $query->where('registration', $this->registration);
            }
            if($this->nr_contract && $this->nr_contract != ''){
                $query->where('nr_contract', $this->nr_contract);
            }
            if($this->vin && $this->vin != ''){
                $query->where('VIN', $this->vin);
            }
        })->where('active', 0)->with( 'owner', 'policyInsuranceCompany', 'salesProgram')->get();

        foreach($vehicles as $vehicle){
            $results[] = [
                'vehicle_id' => $vehicle->id,
                'vehicle_type' => '2',
                'registration' => $vehicle->registration,
                'brand' => $vehicle->brand,
                'model' => $vehicle->{'model'},
                'vin' => $vehicle->VIN,
                'year_production' => $vehicle->year_production,
                'owner' => $vehicle->owner->name,
                'sales_program' => $vehicle->salesProgram ? $vehicle->salesProgram->name_key : '',
                'contract_number' => $vehicle->nr_contract,
                'end_leasing' => $vehicle->end_leasing,
                'insurance_company' => $vehicle->policyInsuranceCompany ? $vehicle->policyInsuranceCompany->name : '',
                'insurance_expire_date' => $vehicle->insurance_expire_date,
                'policy_number' => $vehicle->nr_policy
            ];
        }

        return $results;
    }

    private function searchInSyjon()
    {
        $syjonService = new \Idea\SyjonService\SyjonService();
        $matcher = new \Idea\SyjonService\Matcher(Input::instance());


        $request = ['contract_internal_agreement_type_id' => 5];
        if ($this->registration && $this->registration != '') {
            $request['registration'] = $this->registration;
        }
        if ($this->nr_contract && $this->nr_contract != '') {
            $request['nr_contract'] = $this->nr_contract;
        }
        if ($this->vin && $this->vin != '') {
            $request['vin'] = $this->vin;
        }

        \Debugbar::addMessage($request);

        $results = $syjonService->searchContracts($request);
        $contracts = json_decode($results);

        if (!$contracts || $contracts->total == 0) {
            return false;
        }
        $contracts = $matcher->salesPrograms($contracts);
        $vehicles = [];
        foreach ($contracts->data as $contract)
        {
            foreach($contract->vehicles as $vehicle){
                $vehicles[] = [
                    'vehicle_id' => $vehicle->id,
                    'vehicle_type' => '3',
                    'registration' => $vehicle->registration,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'vin' => $vehicle->vin,
                    'year_production' => $vehicle->year_production,
                    'owner' => $contract->owner->contractor_name,
                    'sales_program' => $contract->verified_sales_program ? $contract->salesProgram->name_key : '',
                    'contract_number' => $contract->contract_number,
                    'end_leasing' => $contract->contract_planned_ending_date,
                    'insurance_company' => (isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0])) ? $vehicle->contract_internal_agreements[0]->policies[0]->policy_insurance_company : '',
                    'insurance_expire_date' => (isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0])) ? $vehicle->contract_internal_agreements[0]->policies[0]->policy_date_to : '',
                    'policy_number' => (isset($vehicle->contract_internal_agreements[0]) && isset($vehicle->contract_internal_agreements[0]->policies[0])) ? $vehicle->contract_internal_agreements[0]->policies[0]->policy_number : '',
                ];
            }
        }

        return $vehicles;

    }
}
