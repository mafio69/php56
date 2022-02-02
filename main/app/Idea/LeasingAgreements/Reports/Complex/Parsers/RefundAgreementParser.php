<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Auth;
use Idea\Exceptions\ImportException;
use LeasingAgreement;
use LeasingAgreementObject;
use PHPExcel_Shared_Date;
use PHPExcel_Worksheet;

class RefundAgreementParser {

    private $sheet;
    private $rows;
    private $owner_id;

    public $existingAgreementsList = array();
    public $unparsedAgreementsList = array();
    public $alreadyArchivedAgreementsList = array();

    private $notification_number ;
    private $filename;


    public function __construct(PHPExcel_Worksheet $sheet, $owner_id, $insurance_company_id, $notification_number)
    {
        $this->sheet = $sheet;
        $this->owner_id = $owner_id;
        $this->insurance_company_id = $insurance_company_id;
        $this->notification_number = $notification_number;
    }

    public function parse()
    {
        $row_begin_nr = $this->checkHeaders();

        $maxCell = $this->sheet->getHighestRowAndColumn();
        $data = $this->sheet->rangeToArray('A'.$row_begin_nr . ':Q' . $maxCell['row'],
            NULL,
            TRUE,
            FALSE,
            TRUE);
        $data = array_map("array_trim", $data);
        $this->rows = $data;

        $this->processAgreements();
    }

    private function checkHeaders()
    {
        $header_a = trim($this->sheet->getCell('A11'));

        if(mb_strtolower($header_a) == 'przedmiot ubezpieczenia')
            return 12;
        else{
            $header_a = trim($this->sheet->getCell('A10'));
            if(mb_strtolower($header_a) == 'przedmiot ubezpieczenia')
                return 11;
        }

        throw new ImportException('błędne nagłówki arkusza wznow');
    }

    public function processAgreements()
    {
        $agreement = null;
        foreach($this->rows as $k => $row)
        {
            if(count($row) == 0 || !isset($row['A']))
                break;

            $agreement = $this->parseAgreement($row);

            if(! is_null($agreement))
            {
                $this->refundAgreement($agreement, $row);
            }
        }
    }

    private function parseAgreement($row)
    {
        $nr_contract = $row['B'];
        $findAgreement = \LeasingAgreement::where('nr_contract', $nr_contract)->first();

        if($findAgreement){
            if(!is_null($findAgreement->archive))
            {
                array_push($this->alreadyArchivedAgreementsList, $nr_contract);
                return null;
            }elseif(! in_array($nr_contract, $this->existingAgreementsList))
            {
                array_push($this->existingAgreementsList, $nr_contract);
                $net_gross = $this->net_gross($row);

                $data_to_update = [
                    'months' => $row['L'],
                    'leasing_agreement_insurance_group_row_id' => (is_null($this->parseInsuranceGroupRow($row))) ? $findAgreement->leasing_agreement_insurance_group_row_id : $this->parseInsuranceGroupRow($row),
                    'rate' => $row['H'],
                    'contribution' => $row['I'],
                    'loan_net_value' => $net_gross['loan_net_value'],
                    'loan_gross_value' => $net_gross['loan_gross_value']
                ];
                $findAgreement->update($data_to_update);
            }

            return $findAgreement;
        }

        array_push($this->unparsedAgreementsList, $nr_contract);

        return null;
    }

    private function net_gross($row)
    {
        if(mb_strtolower($row['G']) == 'netto')
        {
            return [
                'net_gross' => 1,
                'loan_net_value' => $row['F'],
                'loan_gross_value' => 0
            ];
        }

        return [
            'net_gross' => 2,
            'loan_net_value' => 0,
            'loan_gross_value' => $row['F']
        ];
    }

    private function parseInsuranceGroupRow($row)
    {
        try {
            $parser = new InsuranceGroupRowParser($this->insurance_company_id, $row['L'], $row['H']);
            $leasing_agreement_insurance_group_row_id = $parser->parse();
            return $leasing_agreement_insurance_group_row_id;
        }catch (ImportException $e) {
            return null;
        }
    }

    private function refundAgreement($agreement, $row)
    {
        $parser = new RefundParser($agreement, $this->notification_number, $row);
        $parser->parse();
    }

}