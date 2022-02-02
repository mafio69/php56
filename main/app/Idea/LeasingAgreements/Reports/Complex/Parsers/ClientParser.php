<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Clients;
use Idea\AddressParser\AddressParser;
use Idea\VoivodeshipMatcher\SingleMatching;

class ClientParser {
    private $client;


    /**
     * ClientParser constructor.
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    public function parse()
    {
        $client = $this->client;
        $client['NIP'] = trim(str_replace('-', '', $client['NIP']));
        $nip = $client['NIP'];
        $checkClient = Clients::where('NIP', '=' , $nip)->orWhere('NIP', '=', str_replace('-','',$nip))->orderBy('id', 'desc')->first();

        $address = AddressParser::parseAddress($client['address']);
        $client['registry_post'] =  $client['correspond_post'] = $address['POSTAL_CODE'];
        $client['registry_city'] =  $client['correspond_city'] = $address['CITY'];
        $client['registry_street'] =  $client['correspond_street'] = $address['STREET'];

        $registry_post = $client['registry_post'];
        if(strlen($registry_post) == 6)
        {
            $matcher = new SingleMatching();
            $voivodeship_id = $matcher->match($registry_post);
            $client['registry_voivodeship_id'] = $voivodeship_id;
            $client['correspond_voivodeship_id'] = $voivodeship_id;
        }

        if( ! $checkClient )
        {
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
}