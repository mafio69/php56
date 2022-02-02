<?php

namespace Idea\Logging\LeasingAgreements\Translations;


class InsuranceTranslator extends BaseTranslator{

    protected $translations = [
        'insurance_number' => 'Nr polisy',
        'months' => 'Liczba miesięcy',
        'insurance_date' => 'Data polisy',
        'date_from' => 'Data od',
        'date_to' => 'Data do',
        'contribution_lessor' => 'Składka leasingobiorcy',
        'rate_lessor' => 'Stawka leasingobiorcy',
        'rate_vbl' => 'Stawka vbl',
        'status' => 'Status',
        'refund' => 'Wysokość zwrotu',
        'leasing_agreement_insurance_type_id' => 'Typ polisy',
        'insurance_company_id' => 'Ubezpieczyciel',
        'leasing_agreement_payment_way_id' => 'Płatn. ubezp. przez leasingobiorcę',
        'if_cession' => 'Czy cesja',
        'if_continuation' => 'Czy kontynuacja',
        'if_refund_contribution' => 'Czy zwrot składki',
        'if_load_decision' => 'Czy decyzja obciążenia',
        'commission_value'  => 'Wysokość prowizji',
        'commission_date'   => 'Data prowizji'
    ];

    protected function leasing_agreement_insurance_type_id($key, $values)
    {
        $old_value = \LeasingAgreementInsuranceType::find($values['old_value']);
        if($old_value)
            $old_value = $old_value->name;
        else
            $old_value = '';

        $new_value = \LeasingAgreementInsuranceType::find($values['new_value']);
        if($new_value) {
            $new_value = $new_value->name;
        }else
            $new_value = '';

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function insurance_company_id($key, $values)
    {
        $old_value = \Insurance_companies::find($values['old_value']);
        if($old_value)
            $old_value = $old_value->name;
        else
            $old_value = '';

        $new_value = \Insurance_companies::find($values['new_value']);
        if($new_value) {
            $new_value = $new_value->name;
        }else
            $new_value = '';

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function leasing_agreement_payment_way_id($key, $values)
    {
        $old_value = \LeasingAgreementPaymentWay::find($values['old_value']);
        if($old_value)
            $old_value = $old_value->name;
        else
            $old_value = '';

        $new_value = \LeasingAgreementPaymentWay::find($values['new_value']);
        if($new_value) {
            $new_value = $new_value->name;
        }else
            $new_value = '';

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function if_cession($key, $values)
    {
        if($values['old_value'] == 1)
            $old_value = 'tak';
        else
            $old_value = 'nie';

        if($values['new_value'])
            $new_value = 'tak';
        else
            $new_value = 'nie';

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function if_continuation($key, $values)
    {
        if($values['old_value'] == 1)
            $old_value = 'tak';
        else
            $old_value = 'nie';

        if($values['new_value'])
            $new_value = 'tak';
        else
            $new_value = 'nie';

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function if_refund_contribution($key, $values)
    {
        if($values['old_value'] == 1)
            $old_value = 'tak';
        else
            $old_value = 'nie';

        if($values['new_value'])
            $new_value = 'tak';
        else
            $new_value = 'nie';

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

    protected function if_load_decision($key, $values)
    {
        if($values['old_value'] == 1)
            $old_value = 'tak';
        else
            $old_value = 'nie';

        if($values['new_value'])
            $new_value = 'tak';
        else
            $new_value = 'nie';

        return [$this->translations[$key] =>
            ['old_value' => $old_value, 'new_value' => $new_value]
        ] ;
    }

}