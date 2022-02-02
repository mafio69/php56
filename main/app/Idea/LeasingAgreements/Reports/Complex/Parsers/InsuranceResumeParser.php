<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Histories;
use PHPExcel_Shared_Date;

class InsuranceResumeParser {


    private $agreement;
    private $notification_number;
    private $insuranceCompany;
    private $row;

    public function __construct($agreement, $insurance_company_id, $notification_number, $row)
    {
        $this->agreement = $agreement;
        $this->notification_number = $notification_number;
        $this->insuranceCompany = \Insurance_companies::find($insurance_company_id);
        $this->row = $row;
    }

    public function parse()
    {
        $agreement = $this->agreement;
        $last_insurance = $agreement->insurances->last();
        if($last_insurance) {
            $last_insurance->active = 0;
            $last_insurance->save();
        }

        $insurance = [
            'leasing_agreement_insurance_type_id' => $this->parseAgreementInsuranceType(),
            'leasing_agreement_payment_way_id' => $this->agreement->leasing_agreement_payment_way_id,
            'insurance_company_id' => $this->insuranceCompany->id,
            'user_id' => \Auth::id(),
            'leasing_agreement_id' => $this->agreement->id,
            'notification_number' => $this->notification_number,
            'months' => $this->agreement->months,
            'date_from' => $this->parseExcelDate($this->row['J']),
            'date_to' => $this->parseExcelDate($this->row['K']),
            'contribution' => $this->agreement->contribution,
            'rate' => $this->agreement->rate,
            'contribution_lessor' => $this->agreement->contribution,
            'rate_lessor' => $this->agreement->rate,
            'active' => 1
        ];
        $agreementInsurance = \LeasingAgreementInsurance::create($insurance);

        Histories::leasingAgreementHistory($agreement->id, 9);

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

        return $agreementInsuranceType->id;
    }

    private function parseExcelDate($date){
        $date = PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }


}