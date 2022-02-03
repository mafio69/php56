<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Auth;
use Idea\Exceptions\ImportException;
use Idea\Exceptions\PermissionException;
use LeasingAgreement;
use LeasingAgreementObject;
use PHPExcel_Shared_Date;
use PHPExcel_Worksheet;

class ResumeAgreementParser {

    private $sheet;
    private $rows;
    private $owner_id;

    public $existingAgreementsList = array();
    public $parsedAgreementsList = array();
    public $alreadyResumedAgreementsList = array();

    private $leasing_agreement_type_id;
    private $notification_number;
    private $leasing_agreement_payment_way_id;
    private $filename;

    public function __construct(PHPExcel_Worksheet $sheet, $owner_id, $insurance_company_id, $leasing_agreement_type_id, $notification_number, $leasing_agreement_payment_way_id, $filename = null)
    {
        $this->sheet = $sheet;
        $this->owner_id = $owner_id;
        $this->insurance_company_id = $insurance_company_id;
        $this->leasing_agreement_type_id = $leasing_agreement_type_id;
        $this->notification_number = $notification_number;
        $this->leasing_agreement_payment_way_id = $leasing_agreement_payment_way_id;
        $this->filename = $filename;
    }

    public function parse()
    {
        $row_begin_nr = $this->checkHeaders();

        $maxCell = $this->sheet->getHighestRowAndColumn();
        $data = $this->sheet->rangeToArray('A'.$row_begin_nr . ':M' . $maxCell['row'],
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
        $header_b = trim($this->sheet->getCell('B10'));
        $header_c = trim($this->sheet->getCell('C10'));
        if( mb_strtolower($header_b) == 'nr umowy' && mb_strtolower($header_c) == '')
            $this->sheet->removeColumn('C');

        $header_f = trim($this->sheet->getCell('F10'));
        if( mb_strtolower($header_f) == 'miejsce uzytkowania')
            $this->sheet->removeColumn('F');
        $header_a = trim($this->sheet->getCell('A10'));
        $header_n = trim($this->sheet->getCell('M10'));

        if(mb_strtolower($header_a) == 'przedmiot leasingu' && mb_strtolower($header_n) == 'grupa')
            return 11;

        $header_b = trim($this->sheet->getCell('B15'));
        $header_c = trim($this->sheet->getCell('C15'));
        if( mb_strtolower($header_b) == 'nr umowy' && mb_strtolower($header_c) == '')
            $this->sheet->removeColumn('C');

        $header_f = trim($this->sheet->getCell('F15'));

        if($header_f == '') {
            $this->sheet->removeRow(15);

            $header_f = trim($this->sheet->getCell('F15'));
            if($header_f == '')
                $this->sheet->removeRow(15);
        }

        $header_f = trim($this->sheet->getCell('F15'));
        if( mb_strtolower($header_f) == 'miejsce uzytkowania')
            $this->sheet->removeColumn('F');

        $header_a = trim($this->sheet->getCell('A15'));
        $header_n = trim($this->sheet->getCell('M15'));

        if(mb_strtolower($header_a) == 'przedmiot leasingu' && mb_strtolower($header_n) == 'grupa')
            return 16;

        throw new ImportException('błędne nagłówki arkusza wznow');
    }

    public function processAgreements()
    {
        $agreement = null;
        $first = false;
        foreach($this->rows as $k => $row)
        {
            if(count($row) == 0)
                break;

            if(is_null($agreement) || $agreement->nr_contract != $row['B'])
            {
                $agreement = $this->parseAgreement($row);

                if(!in_array($agreement->nr_contract, $this->alreadyResumedAgreementsList))
                {
                    $this->parseResumeInsurance($agreement, $row);
                }

                $first = true;
            }

            if(!in_array($agreement->nr_contract, $this->existingAgreementsList))
            {
                $this->parseObject($row, $agreement->id, $first);
                $first = false;
            }
        }
    }

    private function parseAgreement($row)
    {
        $nr_contract = $row['B'];
        $findAgreement = \LeasingAgreement::where('nr_contract', $nr_contract)->first();

        if($findAgreement){
            if(! in_array($nr_contract, $this->existingAgreementsList) && ! in_array($nr_contract, $this->alreadyResumedAgreementsList))
            {
                $last_insurance = $findAgreement->insurances->last();
                if($last_insurance && $last_insurance->notification_number == $this->notification_number)
                {
                    array_push($this->alreadyResumedAgreementsList, $findAgreement->nr_contract);
                }else {
                    array_push($this->existingAgreementsList, $nr_contract);
                }
                $net_gross = $this->net_gross($row);

                $data_to_update = [
                    'months' => $row['L'],
                    'leasing_agreement_insurance_group_row_id' => $this->parseInsuranceGroupRow($row),
                    'leasing_agreement_payment_way_id' => ($this->leasing_agreement_payment_way_id) ? $this->leasing_agreement_payment_way_id : $findAgreement->leasing_agreement_payment_way_id,
                    'rate' => $row['H'],
                    'contribution' => $row['I'],
                    'loan_net_value' => $net_gross['loan_net_value'],
                    'loan_gross_value' => $net_gross['loan_gross_value']
                ];
                $findAgreement->update($data_to_update);
            }

            return $findAgreement;
        }

        $agreement = $this->createAgreement($row);

        array_push($this->parsedAgreementsList, $agreement->toArray());

        return $agreement;
    }

    private function createAgreement($row)
    {
        $user_id = Auth::id();
        $client_id = $this->parseClient($row);
        $nr_agreement = $this->generateNr_agreement();
        $net_gross = $this->net_gross($row);
        $leasing_agreement_insurance_group_row_id = $this->parseInsuranceGroupRow($row);

        $agreement = \LeasingAgreement::create([
            'client_id' => $client_id,
            'user_id' => $user_id,
            'owner_id' => $this->owner_id,
            'nr_contract' => $row['B'],
            'nr_agreement' => $nr_agreement,
            'installments' => $row['L'],
            'months' => $row['L'],
            'rate' => $row['H'],
            'contribution' => $row['I'],
            'initial_rate'         => $row['H'],
            'initial_contribution' => $row['I'],
            'loan_net_value' => $net_gross['loan_net_value'],
            'loan_gross_value' => $net_gross['loan_gross_value'],
            'leasing_agreement_type_id' => $this->leasing_agreement_type_id,
            'net_gross' => $net_gross['net_gross'],
            'leasing_agreement_payment_way_id' => $this->leasing_agreement_payment_way_id,
            'leasing_agreement_insurance_group_row_id' => $leasing_agreement_insurance_group_row_id,
            'insurance_from' => $this->parseExcelDate($row['J']),
            'insurance_to' => $this->parseExcelDate($row['K']),
            'detect_problem' => ($this->leasing_agreement_payment_way_id) ? 0 : 1,
            'creating_way'         => 4,
            'filename'             => $this->filename
        ]);

        return $agreement;
    }

    private function parseClient($row)
    {
        $client_data = [
            'name' => $row['C'],
            'NIP' => $row['E'],
            'REGON' => '',
            'address' => $row['D']
        ];

        $clientParser = new ClientParser($client_data);
        return $clientParser->parse();
    }

    public function generateNr_agreement()
    {
        $lastAgreement = LeasingAgreement::orderby('id', 'desc')->first();
        if(is_null($lastAgreement)){
            return '1/'.date('n/Y');
        }

        $nr = explode('/', $lastAgreement->nr_agreement);
        $year = date('Y');
        $month = date('n');
        if($year > $nr[2]){
            return '1/'.date('n/Y');
        }

        if($month > (int)$nr[1]){
            return '1/'.date('n').'/'.$nr[2];
        }
        $nr[0] +=1;
        return $nr[0].'/'.$nr[1].'/'.$nr[2];
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

    private function parseExcelDate($date){
        $date = PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }

    public function parseObject($row, $agreement_id, $first)
    {
        $object['leasing_agreement_id'] = $agreement_id;
        $object['name'] = $row['A'];
        $object['user_id'] = Auth::id();
        $assetType = \ObjectAssetType::where('name', 'like', $row['M'])->first();

        if($assetType)
            $object['object_assetType_id'] = $assetType->id;

        if($first)
        {
            $net_gross = $this->net_gross($row);
            $object['net_value'] = $net_gross['loan_net_value'];
            $object['gross_value'] = $net_gross['loan_gross_value'];
        }

        LeasingAgreementObject::create($object);
    }

    private function parseResumeInsurance($agreement, $row)
    {
        $parser = new InsuranceResumeParser($agreement, $this->insurance_company_id, $this->notification_number, $row);
        $insurance = $parser->parse();
        return $insurance;
    }

    private function parseInsuranceGroupRow($row)
    {
        $parser = new InsuranceGroupRowParser($this->insurance_company_id, $row['L'], $row['H']);
        $leasing_agreement_insurance_group_row_id = $parser->parse();
        return $leasing_agreement_insurance_group_row_id;
    }

}