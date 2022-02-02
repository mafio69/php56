<?php

namespace Idea\LeasingAgreements\InprogressAgreement;


use Clients;
use DateTime;
use Histories;
use Idea\AddressParser\AddressParser;
use Idea\VoivodeshipMatcher\SingleMatching;
use LeasingAgreement;
use LeasingAgreementObject;

class InprogressParser {

    private $currentAgreement;
    private $currentAgreementObject;
    private $currentLeasingObject;

    public function parseSingle($agreement)
    {
        $parsedAgreement = $this->parse($agreement);

        $parsedClient = $this->parseClient($parsedAgreement['client_data']);
        $parsedAgreement['agreement_data']['client_id'] = $parsedClient['client_id'];
        if($parsedClient['status'] == 'error')
            $parsedAgreement['agreement_data']['detect_problem'] = 1;

        $agreement_id = $this->parseAgreement($parsedAgreement['agreement_data']);
        $this->parseObject($parsedAgreement['object_data'], $agreement_id);
        $this->parseInsurance($parsedAgreement['insurance_data'], $agreement_id);
        return $agreement_id;
    }

    public function parseMultiple($agreements)
    {
        $parsedAgreements = array();

        foreach($agreements as $agreement) {
            $parsedAgreements[] = $this->parse($agreement);
        }
        unset($agreements);
        unset($agreement);

        $sortedAgreements = $this->sortAgreements($parsedAgreements); //posortowanie wg numeru zgłoszenie|daty od|daty do

        unset($parsedAgreement);
        $first = true;
        $agreementInfo = array();

        ksort($sortedAgreements['sortedAgreements']);
        foreach($sortedAgreements['sortedAgreements'] as $agreementsInYear)
        {
            ksort($agreementsInYear);
            foreach($agreementsInYear as $parsedAgreement) {
                if ($first) {
                    $agreementInfo = $this->multipleGenerateAgreement($parsedAgreement);
                    $this->currentAgreement = $parsedAgreement;
                    $first = false;
                } else {
                    $this->compareAgreements($parsedAgreement['agreement_data']);
                    $this->compareObjects($parsedAgreement['object_data']);

                    if ($parsedAgreement['agreement_data']['if_data_change'] == 1)
                        $agreementInfo['client_id'] = $this->compareExistClientData($agreementInfo, $parsedAgreement['client_data']);
                }

                $this->parseInsurance($parsedAgreement['insurance_data'], $agreementInfo['agreement_id']);
            }
        }

        if(count($sortedAgreements['wrongAgreements']) > 0) {
            foreach ($sortedAgreements['wrongAgreements'] as $parsedAgreement) {
                if ($first) {
                    $agreementInfo = $this->multipleGenerateAgreement($parsedAgreement);
                    $this->currentAgreement = $parsedAgreement;
                    $first = false;
                } else {
                    $this->compareAgreements($parsedAgreement['agreement_data']);
                    $this->compareObjects($parsedAgreement['object_data']);

                    if ($parsedAgreement['agreement_data']['if_data_change'] == 1)
                        $agreementInfo['client_id'] = $this->compareExistClientData($agreementInfo, $parsedAgreement['client_data']);
                }

                $this->parseInsurance($parsedAgreement['insurance_data'], $agreementInfo['agreement_id']);
            }
            $agreement = LeasingAgreement::find($agreementInfo['agreement_id']);
            $agreement->detect_problem = 1;
            $agreement->save();
        }
    }

    public function parseSingleWithCession($agreement)
    {
        $parsedAgreement = $this->parse($agreement);

        $parsedClient = $this->parseClient($parsedAgreement['client_data']);
        $parsedAgreement['agreement_data']['client_id'] = $parsedClient['client_id'];

        $parsedAgreement['agreement_data']['detect_problem'] = 1;
        $parsedAgreement['agreement_data']['potential_cession'] = 1;
        $agreement_id = $this->parseAgreement($parsedAgreement['agreement_data']);
        $this->parseObject($parsedAgreement['object_data'], $agreement_id);

        $this->parseInsurance($parsedAgreement['insurance_data'], $agreement_id);
    }

    public function parseMultipleWithCession($agreements)
    {
        $parsedAgreements = array();

        foreach($agreements as $agreement) {
            $parsedAgreements[] = $this->parse($agreement);
        }
        unset($agreements);
        unset($agreement);

        $sortedAgreements = $this->sortAgreements($parsedAgreements); //posortowanie wg numeru zgłoszenie|daty od|daty do

        unset($parsedAgreement);
        $first = true;
        $agreementInfo = array();

        ksort($sortedAgreements['sortedAgreements']);
        foreach($sortedAgreements['sortedAgreements'] as $agreementsInYear)
        {
            ksort($agreementsInYear);
            foreach($agreementsInYear as $parsedAgreement) {
                if ($first) {
                    $agreementInfo = $this->multipleGenerateAgreement($parsedAgreement);
                    $this->currentAgreement = $parsedAgreement;
                    $first = false;
                } else {
                    $this->compareAgreements($parsedAgreement['agreement_data']);
                    $this->compareObjects($parsedAgreement['object_data']);
                }

                $lastInsurance = $this->parseInsurance($parsedAgreement['insurance_data'], $agreementInfo['agreement_id']);

                $agreementInfo['client_id'] = $this->checkIfCession($agreementInfo, $parsedAgreement['client_data'], $lastInsurance);
            }
        }

        if(count($sortedAgreements['wrongAgreements']) > 0) {
            foreach ($sortedAgreements['wrongAgreements'] as $parsedAgreement) {
                if ($first) {
                    $agreementInfo = $this->multipleGenerateAgreement($parsedAgreement);
                    $this->currentAgreement = $parsedAgreement;
                    $first = false;
                } else {
                    $this->compareAgreements($parsedAgreement['agreement_data']);
                    $this->compareObjects($parsedAgreement['object_data']);
                }

                $lastInsurance = $this->parseInsurance($parsedAgreement['insurance_data'], $agreementInfo['agreement_id']);

                $agreementInfo['client_id'] = $this->checkIfCession($agreementInfo, $parsedAgreement['client_data'], $lastInsurance);
            }
            $agreement = LeasingAgreement::find($agreementInfo['agreement_id']);
            $agreement->detect_problem = 1;
            $agreement->save();
        }

        $this->checkIfValidCession();
    }

    public function parse($agreement)
    {
        $parsed = array();

        $parsed['agreement_data'] =  array(
                "no" => $agreement['no'],
                "nr_contract" => $agreement['nr_contract'],
                "correction" => $agreement['correction'],
                "if_data_change" => $this->yes_no($agreement['if_data_change']),
                "agreement_type" => $agreement['agreement_type'],
                "owner" => $agreement['owner'],
                "agreement_payment_way" => $agreement['agreement_payment_way'],
                "loan_net_value" => $agreement['loan_net_value'],
                "net_gross" => $this->net_gross($agreement['net_gross']),
                "rate" => $agreement['rate'],
                "contribution" => $agreement['contribution'],
                "owner_id" => 3,
                "insurance_from" => $this->formatDate($agreement['date_from']),
                "insurance_to" => $this->formatDate($agreement['date_to']),
                "installments" => $agreement['months']
        );
        $parsed['insurance_data'] = array(
                "insurance_number" => $agreement['insurance_number'],
                'if_continuation' => $this->yes_no($agreement['if_continuation']),
                "if_refund_contribution" => $this->yes_no($agreement['if_refund_contribution']),
                "if_load_decision" => $this->yes_no($agreement['if_load_decision']),
                "insurance_type" => $agreement['insurance_type'],
                "months" => $agreement['months'],
                "notification_number" => $agreement['notification_number'],
                "insurance_date" => $this->formatDate($agreement['insurance_date']),
                "date_from" => $this->formatDate($agreement['date_from']),
                "date_to" => $this->formatDate($agreement['date_to']),
                "insurance_company" => $agreement['insurance_company'],
                "contribution" => $agreement['contribution'],
                "rate" => $agreement['rate'],
                "rate_vbl" => $agreement['rate_vbl'],
                "refund" => $agreement['refund'],
                "agreement_payment_way" => $agreement['agreement_payment_way'],
        );
        $parsed['client_data'] = array(
                "name" => $agreement['client_name'],
                "address" => $agreement['client_address'],
                "REGON" => $agreement['client_REGON'],
                "NIP" => $agreement['client_NIP']
        );
        $parsed['object_data'] = array(
                "agreement_insurance_group" => $agreement['agreement_insurance_group']
        );

        return $parsed;
    }

    private function yes_no($value)
    {
        $value = strtoupper($value);
        if($value == 'TAK' || $value == 'YES')
            return 1;
        elseif($value == 'NIE' || $value == 'NO')
            return 0;

        return null;
    }

    private function formatDate($date){
        if($date != '') {
            $myDateTime = DateTime::createFromFormat('n/j/y', $date);
            $date = $myDateTime->format('Y-m-d');
        }
        return $date;
    }

    private function parseClient($client)
    {
        $client['NIP'] = trim(str_replace('-', '', $client['NIP']));
        $checkClients = Clients::where(function($query) use ($client){
                if($client['NIP'] != '')
                    $query->where('NIP', '=' , $client['NIP']);
                })->orWhere(function($query) use ($client){
                    if($client['name'] != '')
                        $query->where('name', '=', $client['name']);
                })->orWhere(function($query) use ($client){
                    if($client['REGON'] != '')
                        $query->where('REGON', '=', $client['REGON']);
                })
                ->orderBy('id', 'desc')->get();

        $address = AddressParser::parseAddress($client['address']);
        $client['registry_post'] =  $client['correspond_post'] = $address['POSTAL_CODE'];
        $client['registry_city'] =  $client['correspond_city'] = ($address['CITY']) ? $address['CITY'] : '';
        $client['registry_street'] =  $client['correspond_street'] = ($address['STREET']) ? $address['STREET'] : '';

        $matcher = new SingleMatching();
        $registry_post = $client['registry_post'];
        if(strlen($registry_post) == 6)
        {
            $voivodeship_id = $matcher->match($registry_post);
            $client['registry_voivodeship_id'] = $voivodeship_id;
            $client['correspond_voivodeship_id'] = $voivodeship_id;
        }

        if( $checkClients->isEmpty() )
        {
            if($client['NIP'] != '' || $client['name'] != '' || $client['REGON'] != '') {
                $client = Clients::create($client);

                return array(
                    'client_id' => $client->id,
                    'status' => 'success'
                );
            }

            return array(
                'client_id' => 0,
                'status' => 'error'
            );
        }
        return $this->compareClients($client, $checkClients);
    }

    private function compareClients($client, $existClients)
    {
        $client['NIP'] = trim(str_replace('-', '', $client['NIP']));
        $equal = false;
        foreach($existClients as $existClient) {
            if (
                ($client['name'] == $existClient->name) &&
                ($client['REGON'] == $existClient->REGON) &&
                ($client['NIP'] == $existClient->NIP) &&
                ($client['registry_post'] == $existClient->registry_post) &&
                ($client['registry_city'] == $existClient->registry_city) &&
                ($client['registry_street'] == $existClient->registry_street)
            ) {
                $equal = true;
                break;
            }
        }

        if($equal) {
            return array(
                'client_id' => $existClient->id,
                'status' => 'success'
            );
        }else {
            $client['parent_id'] = $existClients->first()->id;
            $client = Clients::create($client);
            return array(
                'client_id' => $client->id,
                'status' => 'success'
            );
        }
    }

    private function parseAgreement($agreement)
    {
        $nr_agreement = $this->generateNr_agreement();

        if($agreement['agreement_type'] != '')
            $agreement['leasing_agreement_type_id'] = $this->parseAgreementType($agreement['agreement_type']);

        if($agreement['agreement_payment_way'] != '')
            $agreement['leasing_agreement_payment_way_id'] = $this->parseAgreementPaymentWay($agreement['agreement_payment_way']);

        $agreement['nr_agreement'] = $nr_agreement;
        $agreement['user_id'] = \Auth::user()->id;

        $agreement = LeasingAgreement::create($agreement);
        $this->currentAgreementObject = $agreement;
        Histories::leasingAgreementHistory($agreement->id, 1, \Auth::user()->id);

        return $agreement->id;
    }

    public function generateNr_agreement()
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

    public function parseObject($object, $agreement_id)
    {
        $object['leasing_agreement_id'] = $agreement_id;
        if($object['agreement_insurance_group'] != '')
            $object['object_assetType_id'] = $this->parseAssetType($object['agreement_insurance_group']);

        $object['user_id'] = \Auth::user()->id;

        $this->currentLeasingObject = LeasingAgreementObject::create($object);
    }

    private function parseAssetType($agreement_insurance_group)
    {
        $checkAssetType = \ObjectAssetType::where('name', 'like', '%'.$agreement_insurance_group.'%')->first();
        if( is_null($checkAssetType)){
            $assetType = \ObjectAssetType::create(array(
                'name' => $agreement_insurance_group
            ));
            return $assetType->id;
        }
        return $checkAssetType->id;
    }

    private function parseInsurance($insurance, $agreement_id)
    {
        $insurance['leasing_agreement_id'] = $agreement_id;
        if($insurance['insurance_type'] != '')
            $insurance['leasing_agreement_insurance_type_id'] = $this->parseAgreementInsuranceType($insurance['insurance_type']);

        if($insurance['agreement_payment_way'] != '')
            $insurance['leasing_agreement_payment_way_id'] = $this->parseAgreementPaymentWay($insurance['agreement_payment_way']);

        if($insurance['insurance_company'] != '')
            $insurance['insurance_company_id'] = $this->parseInsuranceCompany($insurance['insurance_company']);

        $insurance['user_id'] = \Auth::user()->id;
        $agreementInsurance = \LeasingAgreementInsurance::create($insurance);
        return $agreementInsurance->id;
    }

    private function parseAgreementType($agreement_type)
    {
        if(strtoupper($agreement_type) == 'LEASING' )
            return 2;
        elseif(strtoupper($agreement_type) == 'POŻYCZKA')
            return 1;

        return null;
    }

    private function parseAgreementPaymentWay($agreement_payment_way)
    {
        if(strtoupper($agreement_payment_way) == 'JEDNORAZOWY' )
            return 2;
        elseif(strtoupper($agreement_payment_way) == 'RATY ROCZNE')
            return 1;

        $agreementPaymentWay = \LeasingAgreementPaymentWay::where('name', 'like', '%'.$agreement_payment_way.'%')->first();
        if(is_null($agreementPaymentWay))
        {
            $agreementPaymentWay = \LeasingAgreementPaymentWay::create(array('name' => $agreement_payment_way));
        }
        return $agreementPaymentWay->id;
    }

    private function parseAgreementInsuranceType($insurance_type)
    {
        if(strtoupper($insurance_type) == 'UMOWA WIELOLETNIA')
            return 1;
        elseif(strtoupper($insurance_type) == 'UMOWA JEDNOROCZNA')
            return 2;

        $agreementInsuranceType = \LeasingAgreementInsuranceType::where('name', 'like', '%'.$insurance_type.'%')->first();
        if(is_null($agreementInsuranceType))
        {
            $agreementInsuranceType = \LeasingAgreementInsuranceType::create(array('name' => $insurance_type));
        }
        return $agreementInsuranceType->id;
    }

    private function parseInsuranceCompany($insurance_company)
    {
        $insuranceCompany = \Insurance_companies::where('name', 'like', '%'.$insurance_company.'%')->first();

        if(is_null($insuranceCompany))
        {
            $insuranceCompany = \Insurance_companies::create(array('name' => $insurance_company));
        }
        return $insuranceCompany->id;
    }

    private function net_gross($net_gross)
    {
        if(strtoupper($net_gross) == 'NETTO')
            return 1;
        elseif(strtoupper($net_gross) == 'BRUTTO')
            return 2;

        return null;
    }

    private function sortAgreements($agreements)
    {
        $sortedAgreements = array();
        $wrongAgreements = array();
        foreach($agreements as $agreement)
        {
            $notification_number = $agreement['insurance_data']['notification_number'];
            if($notification_number != '' && $notification_number != '(auto)') //jeśli jest przydzielony numer zgłoszenia, on wskazuje kolejność
            {
                $exploded_notification_number = explode('/', $notification_number);
                $exploded_notification_number[0] = ltrim($exploded_notification_number[0], '0');

                while (isset($sortedAgreements[$exploded_notification_number[1]][$exploded_notification_number[0]])) {
                    $exploded_notification_number[0] += 1;
                    $toImplode = $exploded_notification_number;
                    $toImplode[0] = str_pad($exploded_notification_number[0], 2, '0', STR_PAD_LEFT);
                    $notification_number = implode('/', $toImplode);
                }

                $sortedAgreements[$exploded_notification_number[1]][$exploded_notification_number[0]] = $agreement;

                $sortedList[$notification_number] = array(
                    'from' => $agreement['insurance_data']['date_from'],
                    'to' => $agreement['insurance_data']['date_to']
                );
            }else{ //jeśli nie przypisano numeru zgłoszenia dopisz do nieposortowanych
                $unsortedAgreements[] = $agreement;
            }
        }
        if(isset($unsortedAgreements)) //jeśli wykryto polisy bez numeru zgłoszenia
        {
            foreach($unsortedAgreements as $k => $agreement)
            {
                $date_from = $agreement['insurance_data']['date_from'];
                $date_to = $agreement['insurance_data']['date_to'];
                if($date_from == '' || $date_from == ''){ //gdy nie ustalone żadne daty trafia do błędnych wpisów
                    $wrongAgreements[] = $agreement;
                }else {
                    if(!isset($sortedList))
                        $wrongAgreements[] = $agreement;
                    else {
                        foreach ($sortedList as $notification_number => $dates) //sprawdzenie czy istnieje polisa zawarta na ten sam okres
                        {
                            if ($date_from == $dates['from'] && $date_to == $dates['to']) {
                                $exploded_notification_number = explode('/', $notification_number);
                                $exploded_notification_number[0] = ltrim($exploded_notification_number[0], '0');

                                reset($sortedAgreements[$exploded_notification_number[1]]);
                                $first_key = key($sortedAgreements[$exploded_notification_number[1]]);

                                $new_key = $first_key - 1;
                                if ($new_key > 0) {
                                    $agreement['insurance_data']['notification_number'] = $new_key . '/' . $exploded_notification_number[1];
                                    $sortedAgreements[$exploded_notification_number[1]][$new_key] = $agreement;
                                    unset($unsortedAgreements[$k]);
                                    break;
                                } else {
                                    $new_year = $exploded_notification_number[1] - 1;
                                    if (isset($sortedList[$new_year])) {
                                        reset($sortedList[$new_year]);
                                        $last_key = end($sortedList[$new_year]);
                                        $new_key = $last_key + 1;
                                    } else
                                        $new_key = 1;

                                    $agreement['insurance_data']['notification_number'] = $new_key . '/' . $new_year;
                                    $sortedAgreements[$new_year][$new_key] = $agreement;

                                    unset($unsortedAgreements[$k]);
                                    break;
                                }
                            }
                        }
                    }
                }

            }
        }

        $result = array(
            'wrongAgreements' => $wrongAgreements,
            'sortedAgreements' => $sortedAgreements
        );
        return $result;
    }

    private function multipleGenerateAgreement($parsedAgreement)
    {
        $parsedClient = $this->parseClient($parsedAgreement['client_data']);
        $agreementInfo['client_id'] = $parsedClient['client_id'];

        if($parsedClient['status'] == 'error')
            $parsedAgreement['agreement_data']['detect_problem'] = 1;

        $parsedAgreement['agreement_data']['client_id'] = $agreementInfo['client_id'];
        $agreementInfo['agreement_id'] = $this->parseAgreement($parsedAgreement['agreement_data']);
        $this->parseObject($parsedAgreement['object_data'], $agreementInfo['agreement_id']);

        return $agreementInfo;
    }

    private function compareExistClientData($agreementInfo, $client_data)
    {
        $client_id = $agreementInfo['client_id'];
        $client = Clients::find($client_id);
        $client_data['NIP'] = trim(str_replace('-', '', $client_data['NIP']));

        $changed = false;
        if($client->name != $client_data['name']) {
            if($client_data['name'] != '')
                $changed = true;
            else
                $client_data['name'] = $client->name;
        }
        if($client->REGON != $client_data['REGON']) {
            if($client_data['REGON'] != '')
                $changed = true;
            else
                $client_data['REGON'] = $client->REGON;
        }
        if($client->NIP != $client_data['NIP']) {
            if($client_data['NIP'] != '')
                $changed = true;
            else
                $client_data['NIP'] = $client->NIP;
        }

        $address = AddressParser::parseAddress($client_data['address']);
        $client_data['registry_post'] =  $client_data['correspond_post'] = $address['POSTAL_CODE'];
        $client_data['registry_city'] =  $client_data['correspond_city'] = ($address['CITY']) ? $address['CITY'] : '';
        $client_data['registry_street'] =  $client_data['correspond_street'] = ($address['STREET']) ? $address['STREET'] : '';

        if($client->registry_post != $client_data['registry_post']){
            if($client_data['registry_post'] != '')
                $changed = true;
            else
                $client_data['registry_post'] = $client_data['correspond_post'] = $client->registry_post;
        }

        if($client->registry_city != $client_data['registry_city']){
            if($client_data['registry_city'] != '')
                $changed = true;
            else
                $client_data['registry_city'] = $client_data['correspond_city'] = $client->registry_city;
        }

        if($client->registry_street != $client_data['registry_street']){
            if($client_data['registry_street'] != '')
                $changed = true;
            else
                $client_data['registry_street'] = $client_data['correspond_street'] = $client->registry_street;
        }

        if($changed)
        {
            $client_data['parent_id'] = $client_id;
            $client = Clients::create($client_data);

            $this->currentAgreementObject->client_id = $client->id;
            $this->currentAgreementObject->save();

            return $client->id;
        }else
            return $client_id;
    }

    private function compareAgreements($parsedAgreement)
    {
        if( $this->currentAgreement['agreement_data']['loan_net_value'] != $parsedAgreement['loan_net_value'] && $parsedAgreement['loan_net_value'] != '')
        {
            $this->currentAgreementObject->loan_net_value = $parsedAgreement['loan_net_value'];
            $this->currentAgreementObject->save();

            $this->currentAgreement['agreement_data']['loan_net_value'] = $parsedAgreement['loan_net_value'];
        }
        if( $this->currentAgreement['agreement_data']['net_gross'] != $parsedAgreement['net_gross'] && $parsedAgreement['net_gross'] != '')
        {
            $this->currentAgreementObject->net_gross = $parsedAgreement['net_gross'];
            $this->currentAgreementObject->save();

            $this->currentAgreement['agreement_data']['net_gross'] = $parsedAgreement['net_gross'];
        }
    }

    private function compareObjects($object_data)
    {
        if( $this->currentAgreement['object_data']['agreement_insurance_group'] != $object_data['agreement_insurance_group'] && $object_data['agreement_insurance_group'] != '')
        {
            $object_assetType_id = $this->parseAssetType($object_data['agreement_insurance_group']);

            $this->currentLeasingObject->object_assetType_id = $object_assetType_id;
            $this->currentLeasingObject->save();

            $this->currentAgreement['object_data']['agreement_insurance_group'] = $object_data['agreement_insurance_group'];
        }
    }

    private function checkIfCession($agreementInfo, $client_data, $lastInsurance)
    {
        $client_id = $agreementInfo['client_id'];
        $client = Clients::find($client_id);

        $changed = false;
        $cession = false;

        $client_data['NIP'] = trim(str_replace('-', '', $client_data['NIP']));

        if(
            ($client->NIP != $client_data['NIP'] && $client->NIP != '' && $client_data['NIP'] != '') ||
            ( ($client->NIP == '' || $client_data['NIP'] == '') && $client->name != $client_data['name'])
        )
        {
            $cession = true;
        }

        if($client->name != $client_data['name']) {
            if($client_data['name'] != '')
                $changed = true;
            else
                $client_data['name'] = $client->name;
        }
        if($client->REGON != $client_data['REGON']) {
            if($client_data['REGON'] != '')
                $changed = true;
            else
                $client_data['REGON'] = $client->REGON;
        }
        if($client->NIP != $client_data['NIP']) {
            if($client_data['NIP'] != '')
                $changed = true;
            else
                $client_data['NIP'] = $client->NIP;
        }

        $address = AddressParser::parseAddress($client_data['address']);
        $client_data['registry_post'] =  $client_data['correspond_post'] = $address['POSTAL_CODE'];
        $client_data['registry_city'] =  $client_data['correspond_city'] = ($address['CITY']) ? $address['CITY'] : '';
        $client_data['registry_street'] =  $client_data['correspond_street'] = ($address['STREET']) ? $address['STREET'] : '';

        if($client->registry_post != $client_data['registry_post']){
            if($client_data['registry_post'] != '')
                $changed = true;
            else
                $client_data['registry_post'] = $client_data['correspond_post'] = $client->registry_post;
        }

        if($client->registry_city != $client_data['registry_city']){
            if($client_data['registry_city'] != '')
                $changed = true;
            else
                $client_data['registry_city'] = $client_data['correspond_city'] = $client->registry_city;
        }

        if($client->registry_street != $client_data['registry_street']){
            if($client_data['registry_street'] != '')
                $changed = true;
            else
                $client_data['registry_street'] = $client_data['correspond_street'] = $client->registry_street;
        }

        if($changed)
        {
            if($cession)
            {
                $this->currentAgreementObject->cessions()->save($client, array('leasing_agreement_insurance_id' => $lastInsurance));
            }

            if( !$cession )
                $client_data['parent_id'] = $client_id;

            $client = Clients::create($client_data);

            $this->currentAgreementObject->client_id = $client->id;



            $this->currentAgreementObject->save();

            return $client->id;
        }else
            return $client_id;
    }

    private function checkIfValidCession()
    {
        if( $this->currentAgreementObject->cessions->isEmpty() )
        {
            $this->currentAgreementObject->cessions()->attach($this->currentAgreementObject->client_id, array('leasing_agreement_insurance_id' => $this->currentAgreementObject->insurances->last()->id));
            $this->currentAgreementObject->save();
        }
    }

}