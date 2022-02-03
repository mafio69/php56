<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Idea\Exceptions\ImportException;

class InsuranceGroupRowParser {


    private $insurance_company_id;
    private $months;
    private $rate;

    public function __construct($insurance_company_id, $months, $rate)
    {
        $this->insurance_company_id = $insurance_company_id;
        $this->months = $months;
        $this->rate = $rate;
    }

    public function parse()
    {
        $leasing_agreement_insurance_group_row = $this->parseGroupRow();
        if(is_null($leasing_agreement_insurance_group_row))
            return null;

        return $leasing_agreement_insurance_group_row->id;
    }

    private function parseGroupRow()
    {
        $months = $this->months;

        switch ($months){
            case ($months <= 12):
                $group_rate_column = 'months_12';
                break;
            case ($months <= 24):
                $group_rate_column = 'months_24';
                break;
            case ($months <= 36):
                $group_rate_column = 'months_36';
                break;
            case ($months <= 48):
                $group_rate_column = 'months_48';
                break;
            case ($months <= 60):
                $group_rate_column = 'months_60';
                break;
            default:
                $group_rate_column = 'months_72';
                break;
        }
        $currentGroup = \LeasingAgreementInsuranceGroupRow::whereHas('insurance_group', function($query){
            $latest = \LeasingAgreementInsuranceGroup::where('insurance_company_id', $this->insurance_company_id)->latest()->first();
            $query->where('id', $latest->id);
        })->where($group_rate_column, $this->rate)->first();

        if(! $currentGroup){
            $currentGroup = \LeasingAgreementInsuranceGroupRow::whereHas('insurance_group', function($query){
                $latest = \LeasingAgreementInsuranceGroup::where('insurance_company_id', $this->insurance_company_id)->lists('id');
                $query->whereIn('id', $latest);
            })->where($group_rate_column, $this->rate)->latest()->first();

            /*
            if(! $currentGroup) {
                throw new ImportException('Niedopasowano stawki ubezpieczenia do istniejących w systemie '.$this->rate);
            }
            */
        }

        return $currentGroup;
    }
}