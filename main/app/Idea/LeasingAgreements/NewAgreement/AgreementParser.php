<?php
namespace Idea\LeasingAgreements\NewAgreement;

use Clients;
use Idea\AddressParser\AddressParser;
use Idea\VoivodeshipMatcher\SingleMatching;
use LeasingAgreement;

class AgreementParser {

    public function check_agreement($new_agreement)
    {
        $agreement_data = $new_agreement['agreement']['agreement_data'];
        $exist_agreement = LeasingAgreement::where('nr_contract', '=', $agreement_data['nr_contract'])->get();
        if($exist_agreement->isEmpty())
        {
            return 'new';
        }else{
            return 'exist';
        }
    }

    public function parseClient($client)
    {
        if($client['NIP']) {
            $client['NIP'] = trim(str_replace('-', '', $client['NIP']));
            $nip = $client['NIP'];
            $checkClient = Clients::where('NIP', '=', $nip)->orWhere('NIP', '=', str_replace('-', '', $nip))->orderBy('id', 'desc')->first();
        }else{
            $checkClient = Clients::where('name', '=', $client['name'])->orderBy('id', 'desc')->first();
        }

        $address = AddressParser::parseAddress($client['address']);
        $client['registry_post'] =  $client['correspond_post'] = $address['POSTAL_CODE'];
        $client['registry_city'] =  $client['correspond_city'] = $address['CITY'];
        $client['registry_street'] =  $client['correspond_street'] = $address['STREET'];

        $matcher = new SingleMatching();
        $registry_post = $client['registry_post'];
        if(strlen($registry_post) == 6)
        {
            $voivodeship_id = $matcher->match($registry_post);
            $client['registry_voivodeship_id'] = $voivodeship_id;
            $client['correspond_voivodeship_id'] = $voivodeship_id;
        }

        if( is_null($checkClient))
        {
            $client['parent_id'] = 0;
            $client = Clients::create($client);

            return $client->id;
        }
        return $this->compareClients($client, $checkClient);
    }

    private function compareClients($client, $existClient)
    {
        $equal = true;
        $client['NIP'] = trim(str_replace('-', '', $client['NIP']));

        if($client['name'] != $existClient->name) {
            $equal = false;
        }
        if($client['REGON'] != $existClient->REGON) {
            $equal = false;
        }

        if($equal)
            return $existClient->id;

        $client['parent_id'] = $existClient->id;
        $client = Clients::create($client);
        return $client->id;
    }

    public function parseAgreement($agreement)
    {
        $nr_agreement = $this->generateNr_agreement();

        $agreement['nr_agreement'] = $nr_agreement;
        $agreement['user_id'] = \Auth::user()->id;

        if(isset($agreement['rate'])) $agreement['initial_rate'] = $agreement['rate'];
        if(isset($agreement['contribution'])) $agreement['initial_contribution'] = $agreement['contribution'];

        $agreement = LeasingAgreement::create($agreement);
        return $agreement->id;
    }

    public function parseObject($object, $agreement_id)
    {
        $object['leasing_agreement_id'] = $agreement_id;
        $object['user_id'] = \Auth::user()->id;
        \LeasingAgreementObject::create($object);
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

}
