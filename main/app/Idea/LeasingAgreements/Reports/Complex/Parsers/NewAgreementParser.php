<?php

namespace Idea\LeasingAgreements\Reports\Complex\Parsers;


use Auth;
use Idea\Exceptions\ImportException;
use Idea\Exceptions\PermissionException;
use LeasingAgreement;
use LeasingAgreementObject;
use PHPExcel_Shared_Date;
use PHPExcel_Worksheet;

class NewAgreementParser {

    private $sheet;
    private $rows;
    private $owner_id;

    public $existingAgreementsList = array();
    private $leasing_agreement_type_id;
    public $parsedAgreementsList = array();
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
        $data = $this->sheet->rangeToArray('A'.$row_begin_nr . ':N' . $maxCell['row'],
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
        $header_a = trim($this->sheet->getCell('A16'));
        $header_n = trim($this->sheet->getCell('N16'));
        if(mb_strtolower($header_a) == 'przedmiot leasingu' && mb_strtolower($header_n) == 'grupa')
            return 17;
        else{
            $header_a = trim($this->sheet->getCell('A11'));
            $header_n = trim($this->sheet->getCell('N11'));
            if(mb_strtolower($header_a) == 'przedmiot leasingu' && mb_strtolower($header_n) == 'grupa')
                return 12;
        }

        throw new ImportException('błędne nagłówki arkusza nowych');
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
                $first = true;
            }

            if($first && !in_array($agreement->nr_contract, $this->existingAgreementsList))
                $this->parseInsurance($agreement);

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
            if(! in_array($nr_contract, $this->existingAgreementsList))
                array_push($this->existingAgreementsList, $nr_contract);

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
            'client_id'            => $client_id,
            'user_id'              => $user_id,
            'owner_id'             => $this->owner_id,
            'nr_contract'          => $row['B'],
            'nr_agreement'         => $nr_agreement,
            'installments'         => $row['M'],
            'months'               => $row['M'],
            'rate'                 => $row['I'],
            'contribution'         => $row['J'],
            'initial_rate'         => $row['I'],
            'initial_contribution' => $row['J'],
            'loan_net_value'                           => $net_gross['loan_net_value'],
            'loan_gross_value'                         => $net_gross['loan_gross_value'],
            'leasing_agreement_type_id'                => $this->leasing_agreement_type_id,
            'net_gross'                                => $net_gross['net_gross'],
            'leasing_agreement_payment_way_id'         => $this->leasing_agreement_payment_way_id,
            'leasing_agreement_insurance_group_row_id' => $leasing_agreement_insurance_group_row_id,
            'insurance_from'                           => $this->parseExcelDate($row['K']),
            'insurance_to'                             => $this->parseExcelDate($row['L']),
            'detect_problem'                           => ($this->leasing_agreement_payment_way_id) ? 0 : 1,
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
            'REGON' => checkIfEmpty('F', $row, ''),
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
        if(mb_strtolower($row['H']) == 'netto')
        {
            return [
                'net_gross' => 1,
                'loan_net_value' => $row['G'],
                'loan_gross_value' => 0
            ];
        }

        return [
            'net_gross' => 2,
            'loan_net_value' => 0,
            'loan_gross_value' => $row['G']
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
        $assetType = \ObjectAssetType::where('name', 'like', $row['N'])->first();

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

    private function parseInsurance($agreement)
    {
        $parser = new InsuranceParser($agreement, $this->insurance_company_id, $this->notification_number);
        $insurance = $parser->parse();
        return $insurance;
    }

    private function parseInsuranceGroupRow($row)
    {
        $parser = new InsuranceGroupRowParser($this->insurance_company_id, $row['M'], $row['I']);
        $leasing_agreement_insurance_group_row_id = $parser->parse();
        return $leasing_agreement_insurance_group_row_id;
    }

}