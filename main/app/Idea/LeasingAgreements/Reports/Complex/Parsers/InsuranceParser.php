<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Histories;

class InsuranceParser {


    private $agreement;
    private $notification_number;
    private $insuranceCompany;

    public function __construct($agreement, $insurance_company_id, $notification_number)
    {
        $this->agreement = $agreement;
        $this->notification_number = $notification_number;
        $this->insuranceCompany = \Insurance_companies::find($insurance_company_id);
    }

    public function parse()
    {
        $contribution = $this->calculateContribution();
        $insurance = [
            'leasing_agreement_insurance_type_id' => $this->parseAgreementInsuranceType(),
            'leasing_agreement_payment_way_id' => $this->agreement->leasing_agreement_payment_way_id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'user_id' => \Auth::id(),
            'leasing_agreement_id' => $this->agreement->id,
            'notification_number' => $this->notification_number,
            'months' => $this->agreement->months,
            'date_from' => $this->agreement->insurance_from,
            'date_to' => $this->agreement->insurance_to,
            'contribution' => $this->agreement->contribution,
            'rate' => $this->agreement->rate,
            'contribution_lessor' => $contribution['contribution_lessor'],
            'last_year_lessor_contribution' => $contribution['last_year_lessor_contribution'],
            'rate_lessor' => $contribution['rate_lessor'],
            'active' => 1
        ];

        $agreementInsurance = \LeasingAgreementInsurance::create($insurance);
        Histories::leasingAgreementHistory($this->agreement->id, 9);
        return $agreementInsurance->id;
    }

    private function parseAgreementInsuranceType()
    {
        $agreementInsuranceType = \LeasingAgreementInsuranceType::where(function($query){
            if($this->agreement->months > 12 )
                $query->whereNull('months');
            else
                $query->where('months', $this->agreement->months);
        })->first();

        if(!$agreementInsuranceType)
            $agreementInsuranceType = \LeasingAgreementInsuranceType::create([
                'months' => $this->agreement->months,
                'name' => $this->agreement->months.' msc'
            ]);

        return $agreementInsuranceType->id;
    }

    private function calculateContribution()
    {
        $leasingAgreement = $this->agreement;

        if($leasingAgreement->net_gross == 2)
            $gross_net = 'loan_gross_value';
        else
            $gross_net = 'loan_net_value';

        $group_rate = $leasingAgreement->insurance_group_row()->first();
        $months = $leasingAgreement->months;

        if($months <= 12){
            return [
                'contribution_lessor' => $leasingAgreement->contribution,
                'rate_lessor' => $leasingAgreement->rate,
                'last_year_lessor_contribution' => 0
            ];
        }

        switch ($months){
            case ($months <= 12):
                $group_rate_column = 'months_12';
                $group_months = 12;
                break;
            case ($months <= 24):
                $group_rate_column = 'months_24';
                $group_months = 24;
                break;
            case ($months <= 36):
                $group_rate_column = 'months_36';
                $group_months = 36;
                break;
            case ($months <= 48):
                $group_rate_column = 'months_48';
                $group_months = 48;
                break;
            case ($months <= 60):
                $group_rate_column = 'months_60';
                $group_months = 60;
                break;
            default:
                $group_rate_column = 'months_72';
                $group_months = 72;
                break;
        }

        if($group_rate)
            $rate = $group_rate->$group_rate_column ;
        else
            $rate = $this->agreement->rate;

        $rate = ($rate / $group_months) * $months;

        $contribution = ($rate / 100) * $leasingAgreement->$gross_net;
        if($this->insuranceCompany->if_rounding == 'true')
            $contribution = round($contribution);

        if($leasingAgreement->leasing_agreement_payment_way_id == 2 || !$group_rate)
        {
            $rate_lessor = $rate;
            $contribution_lessor = $contribution;
        }else {
            $rate_lessor = $group_rate->months_12;
            $contribution_lessor = ($rate_lessor / 100) * $leasingAgreement->$gross_net;
            if($this->insuranceCompany->if_rounding == 'true')
                $contribution_lessor = round($contribution_lessor);
        }

        if($months != $group_months && $months > 12)
        {
            $months_diff = 12-($group_months-$months);
            $last_year_lessor_contribution = ($contribution_lessor/12) * $months_diff ;
            if($this->insuranceCompany->if_rounding == 'true')
                $last_year_lessor_contribution = round($last_year_lessor_contribution);
        }else {
            $last_year_lessor_contribution = 0;
        }

        return array(
            'contribution' => number_format((float)$contribution, 2, '.', ''),
            'rate' => number_format((float)$rate, 2, '.', ''),
            'contribution_lessor' => number_format((float)$contribution_lessor, 2, '.', ''),
            'rate_lessor' => number_format((float)$rate_lessor, 2, '.', ''),
            'last_year_lessor_contribution' => number_format((float)$last_year_lessor_contribution, 2, '.', '')
        );
    }
}