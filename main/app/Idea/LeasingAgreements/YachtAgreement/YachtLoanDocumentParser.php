<?php

namespace Idea\LeasingAgreements\YachtAgreement;


use Carbon\Carbon;
use Config;

class YachtLoanDocumentParser extends BaseYachtDocumentParser {

    private $agreement_id;

    private $headers = [
        'A' => 'Numer umowy',
        'B' => 'Status nazwa',
        'C' => 'Kod promocji',
        'D' => 'Ostatni status - Data zmiany',
        'E' => 'Typ przedmiotu',
        'F' => 'Nazwa przedmiotu leasingu',
        'G' => 'Nazwa przedmiotu leasingu z numerem przedm',
        'H' => 'Numer rejestracyjny',
        'I' => 'Numer rejestracyjny (P2)',
        'J' => 'Pełna nazwa',
        'K' => 'Kod Pocztowy',
        'L' => 'Miejscowość',
        'M' => 'Ulica',
        'N' => 'NIP',
        'O' => 'Data aktywacji umowy',
        'P' => 'Data ostatniej raty',
        'Q' => 'Ubezpieczyciel ostatni',
        'R' => 'Polisa od ostatnia',
        'S' => 'Polisa do ostatnia',
        'T' => 'Nr polisy ostatni',
    ];
    /**
     * @var
     */
    public $unparsedRows = [];

    function __construct($filename, $parameters)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/yachts/';
        $this->file = $path.$filename;
        $this->filename = $filename;

        if(!\File::exists($path)) {
            \File::makeDirectory($path,511, true);
        }
        $this->parameters = $parameters;
    }

    function load()
    {
        if( ! parent::load() )
            return false;

        return $this->checkFileStructure();
    }

    function parse_rows()
    {
        $groupedRows = $this->groupRows();
        foreach($groupedRows as $nr_contract => $objects)
        {
            $leasingAgreement = \LeasingAgreement::where('nr_contract','=', $nr_contract)->first();
            if(is_null($leasingAgreement))
            {
                \Log::info('create new agreement', $objects[0]);
                $this->createAgreement($objects[0]);
            }else{
                if(is_null($leasingAgreement->leasing_agreement_type_id))
                {
                    $leasingAgreement->leasing_agreement_type_id = 1;
                }else if($leasingAgreement->leasing_agreement_type_id != 1) {
                    $leasingAgreement->detect_problem = 1;
                    $leasingAgreement->save();

                    \Histories::leasingAgreementHistory($leasingAgreement->id, 7, \Auth::user()->id, '<b>pożyczka</b>');
                }
                $leasingAgreement->owner_id = $this->parameters['owner_id'];
                $leasingAgreement->has_yacht = 1;

                $leasingAgreement->save();
                \Histories::leasingAgreementHistory($leasingAgreement->id, 20, \Auth::user()->id, '<b>leasing</b>');

                $this->agreement_id = $leasingAgreement->id;
            }
            $this->createInsurance($objects[0]);

            foreach($objects as $object)
            {
                $this->createObject($object);
            }

        }

        return 'success';
    }

    private function checkFileStructure()
    {
        if(! isset($this->rows[1]) || count($this->rows[1]) != 20)
            return $this->parseFailed('Błędne nagłówki tabeli.', 'Błędne nagłówki tabeli.');

        if(!count(array_diff_assoc($this->headers, $this->rows[1]) ) == 0)
            return $this->parseFailed('Błędne nagłówki tabeli.', 'Błędne nagłówki tabeli.');

        unset($this->rows[0]);
        unset($this->rows[1]);

        return true;
    }

    private function groupRows()
    {
        foreach($this->rows as $row_nb => $row)
        {
            if(isset($row['A']))
                $groupedRows[trim($row['A'])][] = array_map('trim', $row);
            else
                $this->unparsedRows[] = $row_nb;
        }

        return $groupedRows;
    }

    private function createAgreement($agreement)
    {
        $client_data = $this->prepareClientData($agreement);
        $client_id = $this->parseAgreementClient($client_data);

        $agreement_data = $this->prepareAgreementData($agreement, $client_id);
        $agreement = \LeasingAgreement::create($agreement_data);
        \Histories::leasingAgreementHistory($agreement->id, 1, \Auth::user()->id);

        $this->agreement_id = $agreement->id;
    }

    private function prepareAgreementData($agreement, $client_id)
    {
        $date_from = (isset($insurance['R'])) ? $this->parseExcelDate($insurance['R']) : null;
        $date_to = (isset($insurance['S'])) ? $this->parseExcelDate($insurance['S']) : null;

        return array(
            'owner_id' => $this->parameters['owner_id'],
            'client_id' => $client_id,
            'user_id' => \Auth::user()->id,
            'nr_contract' => $agreement['A'],
            'nr_agreement' => $this->generateNr_agreement(),
            'leasing_agreement_type_id' => 1,
            'status' => (isset($agreement['B'])) ? $agreement['B'] : null,
            'insurance_from' => $date_from,
            'insurance_to' =>  $date_to,
            'date_acceptation'  => (isset($agreement['O'])) ? $this->parseExcelDate($agreement['O']) : null,
            'has_yacht' => 1,
            'creating_way' => 3,
            'filename' => $this->filename
        );
    }

    private function createInsurance($insurance)
    {
        $insurance_data = $this->prepareInsuranceData($insurance);
        if(!is_null($insurance_data['insurance_number']))
        {
            $insurance_db = \LeasingAgreementInsurance::
            where('leasing_agreement_id', $insurance_data['leasing_agreement_id'])
                ->where(function($query) use($insurance_data){
                    $query->where('insurance_number', $insurance_data['insurance_number']);
                    $query->orWhere('insurance_number', $insurance_data['insurance_number'].'/');
                })
                ->latest('id')->first();
            if($insurance_db)
            {
                if($insurance_db->payments->count() == 0)
                {
                    \LeasingAgreementInsurancePayment::create(
                        [
                            'leasing_agreement_insurance_id' => $insurance_db->id,
                            'deadline' => (isset($insurance['P'])) ? $this->parseExcelDate($insurance['P']) : null,
                        ]
                    );
                }
                if(!is_null($insurance_data['insurance_company_id']) && is_null($insurance_db->insurance_company_id) )
                {
                    $insurance_db->insurance_company_id = $insurance_data['insurance_company_id'];
                    $insurance_db->save();
                }

                return true;
            }
        }

        $leasing_agreement_insurance = \LeasingAgreementInsurance::create($insurance_data);

        \LeasingAgreementInsurancePayment::create(
            [
                'leasing_agreement_insurance_id' => $leasing_agreement_insurance->id,
                'deadline' => (isset($insurance['P'])) ? $this->parseExcelDate($insurance['P']) : null,
            ]
        );
        return true;
    }

    private function prepareInsuranceData($insurance)
    {
        $date_from = (isset($insurance['R'])) ? $this->parseExcelDate($insurance['R']) : null;
        $date_to = (isset($insurance['S'])) ? $this->parseExcelDate($insurance['S']) : null;
        if(!is_null($date_from) && !is_null($date_to)){
            $d1 = Carbon::createFromFormat('Y-m-d', $date_from);
            $d2 = Carbon::createFromFormat('Y-m-d', $date_to);

            $months = $d1->diffInMonths($d2);
        }else
            $months = null;

        return array(
            'user_id' => \Auth::user()->id,
            'leasing_agreement_id' => $this->agreement_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'months' =>  $months,
            'status' => (isset($insurance['B'])) ? $insurance['B'] : null,
            'insurance_company_id' => (isset($insurance['Q']))?$this->parseInsuranceCompany($insurance['Q']):null,
            'leasing_agreement_payment_way_id' => 2,
            'insurance_number' => (isset($insurance['T'])) ? $insurance['T'] : null,
            'promo_code'    => (isset($insurance['C'])) ? $insurance['C'] : null,
            'active'        => 1
        );
    }

    private function prepareClientData($agreement)
    {
        return [
            'name'          => (isset($agreement['J'])) ? $agreement['J'] : null,
            'registry_post' => (isset($agreement['K'])) ? $agreement['K'] : null,
            'registry_city' => (isset($agreement['L'])) ? $agreement['L'] : null,
            'registry_street' => (isset($agreement['M'])) ? $agreement['M'] : null,
            'NIP'           => (isset($agreement['N'])) ? $agreement['N'] : null,
        ];
    }

    private function createObject($object)
    {
        \LeasingAgreementObject::create(array(
            'user_id' => \Auth::user()->id,
            'leasing_agreement_id' => $this->agreement_id,
            'name' => (isset($object['F'])) ? $object['F'] : null,
            'registration_number' => (isset($object['H'])) ? mb_strtoupper( str_replace(' ', '', $object['H']) ) : null,
            'object_assetType_id' => $this->parseObjectAssetType($object)
        ));
        \Log::info('new object', $object);
    }

    private function parseObjectAssetType($object)
    {
        if(! isset($object['E']))
            return null;

        $assetType = \ObjectAssetType::where('name', '=', $object['E'])->first();

        if(is_null($assetType))
            $assetType = \ObjectAssetType::create(array('name' => $object['E'], 'if_yacht' => 1));

        return $assetType->id;
    }
}