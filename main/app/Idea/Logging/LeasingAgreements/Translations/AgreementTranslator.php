<?php

namespace Idea\Logging\LeasingAgreements\Translations;


class AgreementTranslator extends BaseTranslator {

    protected $translations = [
        'owner_id'  => 'Finansujący',
        'nr_contract' => 'Nr umowy leasingu',
        'nr_agreement' => 'Nr zgłoszenia',
        'installments' => 'Ilość rat',
        'contribution' => 'Składka',
        'rate'  => 'Stawka',
        'loan_net_value' => 'Wartość netto pożyczki',
        'loan_gross_value' => 'Wartość brutto pożyczki',
        'leasing_agreement_type_id' => 'Rodzaj umowy',
        'leasing_agreement_insurance_group_row_id' => 'Grupa ubezpieczenia',
        'net_gross' => 'Ubezpieczenie od kwoty',
        'status' => 'Status umowy',
        'date_acceptation' => 'Data akceptacji',
        'insurance_from' => 'Okres ubezp. od',
        'insurance_to' => 'Okres ubezp. do'
    ];

    protected function leasing_agreement_insurance_group_row_id($key, $values)
    {
        $old_value = \LeasingAgreementInsuranceGroupRow::find($values['old_value']);
        if($old_value)
            $old_value = $old_value->rate->name;

        $new_value = \LeasingAgreementInsuranceGroupRow::find($values['new_value']);
        if($new_value) {
            $rate = \LeasingAgreementInsuranceGroupRate::find($new_value['leasing_agreement_insurance_group_rate_id']);
            $new_value = $rate->name;
        }
        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function owner_id($key, $values)
    {
        $old_value = \Owners::find($values['old_value']);
        if($old_value)
            $old_value = ($old_value->old_name) ? $old_value->name.' ('.$old_value->old_name.')' : $old_value->name;

        $new_value = \Owners::find($values['new_value']);
        if($new_value)
            $new_value = ($new_value->old_name) ? $new_value->name.' ('.$new_value->old_name.')' : $new_value->name;

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function leasing_agreement_type_id($key, $values)
    {
        $old_value = \LeasingAgreementType::find($values['old_value']);
        if($old_value)
            $old_value = $old_value->name;

        $new_value = \LeasingAgreementType::find($values['new_value']);
        if($new_value)
            $new_value = $new_value->name;

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function client_id($key, $values)
    {
        $old_client = \Clients::find($values['old_value']);
        $new_client = \Clients::find($values['new_value']);

        $result = array(
            'Korzystający' =>
                [
                    'old_value' => $old_client->name,
                    'new_value' => $new_client->name
                ],
            'Adres korzystającego' =>
                [
                    'old_value' => $old_client->register_street,
                    'new_value' => $new_client->register_street
                ],
            'Kod pocztowy korzystającego' =>
                [
                    'old_value' => $old_client->register_post,
                    'new_value' => $new_client->register_post
                ],
            'Miejscowość korzystającego' =>
                [
                    'old_value' => $old_client->register_city,
                    'new_value' => $new_client->register_city
                ]
        );

        return $result;
    }
}
