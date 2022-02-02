<?php
namespace Idea\LeasingAgreements\Reports\Policies;

use Auth;
use Idea\Exceptions\ImportException;
use Idea\Exceptions\PermissionException;
use PHPExcel_Shared_Date;
use PHPExcel_Worksheet;

class PoliciesParser
{
    private $sheet;
    private $insurance_company_id;
    private $filename;
    public $missing = [];
    public $existing = [];
    public $parsed = [];

    public function __construct(PHPExcel_Worksheet $sheet, $insurance_company_id, $filename)
    {
        $this->sheet = $sheet;
        $this->insurance_company_id = $insurance_company_id;
        $this->filename = $filename;
    }

    public function parse()
    {
        $row_begin_nr = $this->checkHeaders();
        $maxCell = $this->sheet->getHighestRowAndColumn();
        $data = $this->sheet->rangeToArray('A'.$row_begin_nr . ':CD' . $maxCell['row'],
            NULL,
            TRUE,
            FALSE,
            TRUE);
        $data = array_map("array_trim", $data);
        $this->rows = $data;
        $this->processPolicies();
    }

    private function checkHeaders()
    {
        $header_a = trim($this->sheet->getCell('CD3'));

        if(mb_strtolower($header_a) == 'nr umowy')
            return 4;

        throw new ImportException('błędne nagłówki arkusza nowych');
    }

    private function processPolicies()
    {
        foreach($this->rows as $k => $row)
        {
            if(count($row) == 0)
                break;

            $rowFields = [
                'policy_number' => $row['A'],
                'insurance_date' => $row['B'],
                'date_from' => $row['C'],
                'date_to' => $row['D'],
                'contract_number' => $row['CD']
            ];

            $agreement = $this->parseAgreement($row);
            if($agreement) {
                $rowFields['leasing_agreement_id'] = $agreement->id;
                $policies = $this->parseInsurance($agreement, $row);
                if($policies->count() > 0){
                    foreach($policies as $policy)
                    {
                        $policy_number = trim($row['A']);
                        if(mb_strtoupper($policy->insurance_number) != mb_strtoupper( $policy_number )){
                            $policy->update([
                               'insurance_number' => $policy_number
                            ]);
                            array_push($this->parsed, $rowFields);
                        }else{
                            array_push($this->existing, $rowFields);
                        }
                    }
                }else{
                    array_push($this->missing, $rowFields);
                }
            }else{
                array_push($this->missing, $rowFields);
            }
        }
    }

    private function parseAgreement($row)
    {
        $nr_contract = $row['CD'];
        $agreement = \LeasingAgreement::where('nr_contract', $nr_contract)->first();

        return $agreement;
    }

    private function parseInsurance($agreement, $row)
    {
        $policies = \LeasingAgreementInsurance::where('leasing_agreement_id', $agreement->id)
                                                ->where('insurance_company_id', $this->insurance_company_id)
                                                ->where('insurance_date', $row['B'])
                                                ->where('date_from', $row['C'])
                                                ->where('date_to', $row['D'])
                                                ->get();

        return $policies;
    }
}