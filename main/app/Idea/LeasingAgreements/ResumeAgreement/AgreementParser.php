<?php

namespace Idea\LeasingAgreements\ResumeAgreement;


use PHPExcel_Shared_Date;

class AgreementParser
{

    public function parseRow($row)
    {
        $row = $this->explode_row($row);
        $agreement = \LeasingAgreement::where('nr_contract', '=', $row['agreement']['nr_contract'])->first();
        if($agreement)
        {
            $result['status'] = 'existing_agreements';

        }else{
            $result['status'] = 'missing_agreements';
        }
        $result['row'] = $row;

        return  $result;
    }

    private function explode_row($row)
    {
        return array(
            'agreement' => array(
                'nr_contract' => $row['B'],
                'guaranteed_sum' => $row['F'],
                'rate' => (isset($row['G'])) ? number_format((float)$row['G'], 2, '.', '') : '0',
                'contribution' => (isset($row['H'])) ? number_format((float)$row['H'], 2, '.', '') : '0',
                'insurance_from' => $this->excelDateToDate($row['I']),
                'insurance_to' => $this->excelDateToDate($row['J']),
                'installments' => $row['K'],
                'status' => $row['L'],
                'loan_net_value' => (isset($row['X'])) ? $this->parseValue($row['X']) : '0',
                'loan_gross_value' => (isset($row['Y'])) ? $this->parseValue($row['Y']) : '0'
            ),
            'object' => array(
                'name' => $row['A'],
                'production_year' => (isset($row['T'])) ? $row['T'] : '',
                'fabric_number' =>  (isset($row['U'])) ? $row['U'] : '',
                'chassis_number' => implode(' ', array((isset($row['V'])) ? $row['V']. ' ' : ''. ' ', (isset($row['W'])) ? $row['W'] : ''))
            ),
            'client' => array(
                'name' => $row['C'],
                'NIP' => $row['D'],
                'address' => $row['E']
            ),
            'insurance' => array(
                'insurance_number' => $row['M'],
                'status' => $row['N'],
                'date_to' => $row['O'],
                'insurance_company' => $row['P'],
                'phone' => (isset($row['Q'])) ? ($row['Q']) : '' . (isset($row['R'])) ? $row['R'] : '',
                'releasing' => (isset($row['S'])) ? ($row['S']) : ''
            )
        );
    }

    public function parseExistingAgreements($rows)
    {
        $nr_contracts = array_keys($rows);

        $agreements = \LeasingAgreement::whereIn('nr_contract', $nr_contracts)->with('objects', 'insurances')->get();

        $parsedExistingAgreements = array();
        foreach($agreements as $agreement)
        {
            $nr_contract = $agreement->nr_contract;
            $row_agreement = $rows[$nr_contract];

            $row_agreement['agreement']['id'] = $agreement->id;

            if($agreement->insurances->isEmpty()) {
                $row_agreement['insurance']['leasing_agreement_id'] = $agreement->id;
                $parsedExistingAgreements['without_existing_insurance'][$nr_contract] = $row_agreement;
            }else{
                $activeInsurance = $agreement->insurances()->active()->first();
                $row_agreement['insurance']['parent_id'] = $activeInsurance->id;
                $row_agreement['insurance']['current_insurance'] = $activeInsurance->toArray();
                if(is_null($agreement->archive)) {
                    $parsedExistingAgreements['with_existing_insurance'][$nr_contract] = $row_agreement;
                }else{
                    $parsedExistingAgreements['in_archive'][$nr_contract] = $row_agreement;
                }
            }
        }

        return $parsedExistingAgreements;
    }

    private function excelDateToDate($date){
        $date = PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }


    private function parseValue($value)
    {
        $value = htmlentities($value, ENT_QUOTES | ENT_IGNORE, "UTF-8");
        $value = str_replace('&nbsp;', '', $value);

        if(!is_numeric($value))
        {
            $value = 0;
        }

        return $value;
    }
}


