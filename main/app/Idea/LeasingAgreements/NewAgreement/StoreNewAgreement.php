<?php
namespace Idea\LeasingAgreements\NewAgreement;

use Histories;

class StoreNewAgreement {

    public function store($agreement)
    {
        $parser = new AgreementParser();
        $client_id = $parser->parseClient($agreement['client']);

        $agreement['agreement_data']['client_id'] = $client_id;
        $agreement_id = $parser->parseAgreement($agreement['agreement_data']);

        foreach($agreement['objects'] as $object)
        {
            $parser->parseObject($object, $agreement_id);
        }

        Histories::leasingAgreementHistory($agreement_id, 1);

        return true;
    }
}
