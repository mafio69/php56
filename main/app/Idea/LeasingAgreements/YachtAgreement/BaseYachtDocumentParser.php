<?php

namespace Idea\LeasingAgreements\YachtAgreement;


use DateTime;
use Idea\LeasingAgreements\AgreementDocumentParserInterface;
use Idea\LeasingAgreements\BaseAgreementDocumentParser;
use Idea\VoivodeshipMatcher\SingleMatching;
use PHPExcel_Shared_Date;

class BaseYachtDocumentParser extends BaseAgreementDocumentParser implements AgreementDocumentParserInterface
{

    function load()
    {
        set_time_limit(500);

        if(file_exists($this->file)) {
            if ($reader = \Excel::load($this->file, 'windows-1250')) {

                $objWorksheet = $reader->getActiveSheet();
                $maxCell = $objWorksheet->getHighestRowAndColumn();
                $data = $objWorksheet->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'],
                    NULL,
                    TRUE,
                    FALSE,
                    TRUE);
                $data = array_map('array_filter', $data);

                $this->rows = array_filter($data);
                $this->rows = array_values($this->rows);
                return true;
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    function parse_rows()
    {
        return 'failed';
    }

    function parseAgreementClient($client_data)
    {
        if(is_null($client_data['name']) && is_null($client_data['NIP']))
            return null;

        $client = \Clients::where(function($query) use($client_data){
            if(!is_null($client_data['NIP']))
                $query->where('NIP', $client_data['NIP']);
            else
                $query->where('name', $client_data['name']);
        })->orderBy('id', 'desc')->first();

        if(is_null($client))
        {
            $registry_post = $client_data['registry_post'];
            if(strlen($registry_post) == 6)
            {
                $matcher = new SingleMatching();
                $voivodeship_id = $matcher->match($registry_post);
                $client['registry_voivodeship_id'] = $voivodeship_id;
            }

            $client = \Clients::create(array(
                'name' => $client_data['name'],
                'registry_post' => $client_data['registry_post'],
                'registry_city' => $client_data['registry_city'],
                'registry_street' => $client_data['registry_street'],
                'NIP' => $client_data['NIP']
            ));
        }

        return $client->id;
    }

    function generateNr_agreement()
    {
        $lastAgreement = \LeasingAgreement::orderby('id', 'desc')->first();
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

    function parseExcelDate($date){
        if($date == '0-00-00')
            return null;

        $d = DateTime::createFromFormat('Y-m-d', $date);
        if( $d && $d->format('Y-m-d') == $date)
            return $date;

        $date = PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }

    function parseInsuranceCompany($insuranceCompany)
    {
        if(mb_substr($insuranceCompany, 0, 1) == '#')
            $insuranceCompany = mb_substr($insuranceCompany, 1);

        $company = \Insurance_companies::where('name', '=', $insuranceCompany)->first();
        if(is_null($company))
        {
            $company = \Insurance_companies::create(array(
                'name' => $insuranceCompany
            ));
        }

        return $company->id;
    }
}