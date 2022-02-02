<?php
namespace Idea\LeasingAgreements\NewAgreement;

use Excel;
use Config;
use File;
use Idea\LeasingAgreements\AgreementDocumentParser;
use Idea\LeasingAgreements\AgreementDocumentParserInterface;
use Idea\LeasingAgreements\BaseAgreementDocumentParser;
use Log;
use PHPExcel_Shared_Date;

class NewAgreementDocumentParser extends BaseAgreementDocumentParser implements AgreementDocumentParserInterface{

    private $slice;

    private $headers = [
            'A' => 'Numer umowy' ,
            'B' => 'Nazwa przedmiotu leasingu' ,
            'C' => 'Data aktywacji umowy' ,
            'D' => 'Płatność polisy' ,
            'E' => 'Pełna nazwa' ,
            'F' => 'Adres' ,
            'G' => 'NIP' ,
            'H' => 'Wart. netto pożyczki' ,
            'I' => 'Stawka' ,
            'J' => 'Składka' ,
            'K' => 'Okres ubezpieczenie Od' ,
            'L' => 'Okres ubezpieczenia Do' ,
            'M' => 'Ilość rat' ,
            'N' => 'Okres umowy',
            'O' => 'Wart. z faktury netto przedm. umowy pożyczki' ,
            'P' => 'Wart. brutto' ,
            'Q' => 'Data akceptacji' ,
            'R' => 'netto/brutto',
            'S' => 'REGON/PESEL',
            'T' => 'Ubezpieczenie',
            'U' => 'TU',
            'V' => 'Oddział',
            'W' => 'Handlowiec' ,
            'X' => 'Osoba wprowadzająca wniosek' ,
            'Y' => 'Symbole przedmiotów (EVO)' ,
            'Z' => 'Składka ubezpieczenia' ,
            'AA' => 'uwagi'
        ];

    function __construct($filename)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/new/';
        $this->file = $path.$filename;
        $this->filename = $filename;

        if(!File::exists($path)) {
            File::makeDirectory($path,511, true);
        }
    }

    public function load()
    {
        set_time_limit(500);

        if(file_exists($this->file)) {
            if ($reader = Excel::load($this->file, 'windows-1250')) {
                $objWorksheet = $reader->getActiveSheet();

                $maxCell = $objWorksheet->getHighestRowAndColumn();
                $data = $objWorksheet->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'],
                    NULL,
                    TRUE,
                    FALSE,
                    TRUE);
                $data = array_map('array_filter', $data);
                $this->rows = array_filter($data);
                return $this->checkFileStructure();
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    public function parse_rows()
    {
        $new_agreement = array();
        $parsedRows = array();
        $agreementParser = new AgreementParser();
        $prev_row = null;
        $i = 0;
        foreach($this->rows as $k => $row)
        {
            $i++;

            if( $prev_row && $row['A'] != $prev_row['A'] ) {
                if (!$new_agreement['agreement'] = $this->parse_agreement($prev_row, $k))
                    return false;

                $parsing_result = $agreementParser->check_agreement($new_agreement);
                $parsedRows[$parsing_result][] = $new_agreement;
                $new_agreement = array();
                $i = 0;
            }
            if(!$object = $this->add_object($row, $i, $k))
                return false;

            $new_agreement['objects'][] = $object;
            $prev_row = $row;
        }
        if(count($new_agreement) > 0)
        {
            if (!$new_agreement['agreement'] = $this->parse_agreement($prev_row, $k))
                return false;

            $parsing_result = $agreementParser->check_agreement($new_agreement);
            $parsedRows[$parsing_result][] = $new_agreement;
        }

        return $this->convertToHtml($parsedRows);
    }

    private function checkFileStructure()
    {
        $slice = 1;

        foreach($this->headers as $column => $value){
            if(! isset($this->rows[$slice][$column]) || mb_strtoupper($this->rows[$slice][$column]) != mb_strtoupper($value)){
                return $this->parseFailed('zła struktura pliku - zła kolumna '.$column.' - powinna być '.$value);
            }
        }

        $keys = array_keys($this->rows);
        $this->rows = array_slice($this->rows, $slice);
        unset($keys);

        $this->slice = ++$slice;
        return true;
    }

    private function add_object($row, $i, $current_row)
    {
        ++$current_row;
        if(!isset($row['B'])) return $this->parseFailed('object name '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['B']);
        $object['name'] = $row['B'];

        if($i == 1 && !isset($row['H'])) return $this->parseFailed('net value '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['H']);
        $object['net_value'] = ($i == 1) ? $this->simplifyValue($row['H']) : 0;

        if($i == 1 && !isset($row['P'])) return $this->parseFailed('gross value '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['P']);
        $object['gross_value'] = ($i == 1) ? $this->simplifyValue($row['P']) : 0;

        return $object;
    }


    private function parse_agreement($row, $k)
    {
        if(!$client = $this->parse_agreement_client($row, $k))
            return false;

        if(!$agreement_data = $this->parse_agreement_data($row, $k))
            return false;

        $agreement['client'] = $client;
        $agreement['agreement_data'] = $agreement_data;

        return $agreement;
    }

    private function parse_agreement_client($row, $k)
    {
        $current_row = $k+$this->slice;
        if(!isset($row['E'])) return $this->parseFailed('client name '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['E']);
        $client['name'] = $row['E'];

        if(!isset($row['F'])) return $this->parseFailed('client address '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['F']);
        $client['address'] = $row['F'];

        if(!isset($row['G'])) $client['NIP'] = null;
        else $client['NIP'] = $row['G'];

        if(isset($row['S']))
            $client['REGON'] = $row['S'];
        else
            $client['REGON'] = null;

        return $client;
    }

    private function parse_agreement_data($row, $k)
    {
        $current_row = $k+$this->slice;
        if(!isset($row['A']))
            return $this->parseFailed('agreement nr_contract '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['B']);

        $agreement['nr_contract'] = $row['A'];

        if( mb_strtoupper( substr($agreement['nr_contract'], -2) ) == '/P')
        {
            $agreement['leasing_agreement_type_id'] = 1; //pożyczka
        }else{
            $agreement['leasing_agreement_type_id'] = 2; //leasing
        }

        if(!isset($row['D']))
            return $this->parseFailed('agreement leasing_agreement_payment_way '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['D']);

        $payment_way_cell = trim($row['D']);
        $payment_way_cell = mb_strtolower($payment_way_cell);
        $payment_way_cell = str_replace(' ','', $payment_way_cell);
        if(
            $payment_way_cell == 'pł.wratach' || $payment_way_cell == 'pl.wratach'
            || $payment_way_cell == 'pł.coroczna' || $payment_way_cell == 'pl.coroczna'
            || $payment_way_cell == 'pł.roczna-polisa1-roczna'
        ) {
            $agreement['leasing_agreement_payment_way_id'] = 1;
            $agreement['if_reportable'] = 1;
        }elseif($payment_way_cell == 'pł.jednorazowa' || $payment_way_cell == 'pl.jednorazowa') {
            $agreement['leasing_agreement_payment_way_id'] = 2;
            $agreement['if_reportable'] = 1;
        }elseif($payment_way_cell == 'wielolatka') {
            $agreement['leasing_agreement_payment_way_id'] = 2;
            $agreement['if_reportable'] = 0;
        }else {
            return $this->parseFailed('agreement leasing_agreement_payment_way ' . $current_row . ' ' . $payment_way_cell, "Błędna struktura pliku. W wierszu: " . $current_row . " nieprawidłowa wartość w kolumnie: " . $this->headers['D']);
        }

        if(!isset($row['H']))
            return $this->parseFailed('agreement loan_net_value '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['H']);
        $agreement['loan_net_value'] = $this->simplifyValue($row['H']);

        if(!isset($row['I'])) $agreement['rate'] = $this->simplifyValue(0);
        else
            $agreement['rate'] = $this->simplifyValue($row['I']);

        if(isset($row['J']))
            $agreement['contribution'] = $row['J'];
        else
            $agreement['contribution'] = null;

        if(!isset($row['K']))
            return $this->parseFailed('agreement insurance_from '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['K']);
        $agreement['insurance_from'] = $this->parseExcelDate($row['K']);

        if(!isset($row['N']))
            return $this->parseFailed('agreement installments '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['N']);
        $agreement['installments'] = $row['N'];
        $agreement['months'] = $this->parseMonths($agreement['installments']);

        $carbonFrom = \Date::createFromFormat('Y-m-d', $agreement['insurance_from']);
        $agreement['insurance_to'] = $carbonFrom->addMonths($agreement['months'])->subDay()->toDateString();

        if(!isset($row['P'])) $agreement['loan_gross_value'] = 0;
        else $agreement['loan_gross_value'] = $this->simplifyValue($row['P']);

        if(!isset($row['Q']))
            return $this->parseFailed('agreement date_acceptation '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['Q']);
        $agreement['date_acceptation'] = $this->parseExcelDate($row['Q']);;

        if(!isset($row['R']))
            return $this->parseFailed('agreement net_gross '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." brakuje wartości w kolumnie: ".$this->headers['R']);

        if($row['R'] == 'netto')
            $agreement['net_gross'] = 1;
        elseif($row['R'] == 'brutto')
            $agreement['net_gross'] = 2;
        else
            return $this->parseFailed('agreement net_gross '.$current_row, "Błędna struktura pliku. W wierszu: ".$current_row." nieprawidłowa wartość w kolumnie: ".$this->headers['R']);

        if(!isset($row['U'])) $agreement['import_insurance_company'] = '';
        else $agreement['import_insurance_company'] = $row['U'];

        if(isset($row['AA'])) $agreement['remarks'] = $row['AA'];

        $agreement['filename'] = $this->filename;

        return $agreement;
    }

    private function parseExcelDate($date){
        $date = PHPExcel_Shared_Date::ExcelToPHP($date);
        return date('Y-m-d',(int) $date);
    }

    private function simplifyValue($value)
    {
        $value = trim($value);
        $value = str_replace(',', '.', $value);
        $value = floatval($value);
        return $value;
    }

    private function convertToHtml($parsedRows)
    {
        $converted = array();
        if(isset($parsedRows['new']))
        {
            foreach($parsedRows['new'] as $k => $row)
            {
                $htmlRow = '<tr>';
                $htmlRow .= '<td>'.++$k;
                $htmlRow .= $this->serializeHtmlRow($row, $k);
                $htmlRow .= '.</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['nr_contract'].'</td>';
                $htmlRow .= '<td>'.number_format($row['agreement']['agreement_data']['loan_net_value'],2,"."," ").' zł</td>';
                $htmlRow .= '<td>'.number_format($row['agreement']['agreement_data']['loan_gross_value'],2,"."," ").' zł</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['rate'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['contribution'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['insurance_from'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['insurance_to'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['installments'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['date_acceptation'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['client']['name'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['client']['NIP'].'</td>';
                $htmlRow .= '<td>';
                $htmlRow .= '<a class="btn btn-sm btn-info btn-popover" tabindex="'.$k.'" role="button" data-toggle="popover" data-trigger="focus" title="przedmioty leasingu do umowy '.$row['agreement']['agreement_data']['nr_contract'].'"';
                $htmlRow .= 'data-content="'.$this->object_info($row['objects']).'">';
                $htmlRow .= 'przedm. leasingu <span class="badge">'.count($row['objects']).'</span>';
                $htmlRow .= '</a>';
                $htmlRow .= '</td>';
                $htmlRow .= '</tr>';
                $converted['new'][] = $htmlRow;
            }
        }

        if(isset($parsedRows['exist']))
        {
            foreach($parsedRows['exist'] as $k => $row)
            {
                $htmlRow = '<tr>';
                $htmlRow .= '<td>'.++$k.'.</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['nr_contract'].'</td>';
                $htmlRow .= '<td>'.number_format($row['agreement']['agreement_data']['loan_net_value'],2,"."," ").' zł</td>';
                $htmlRow .= '<td>'.number_format($row['agreement']['agreement_data']['loan_gross_value'],2,"."," ").' zł</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['rate'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['contribution'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['insurance_from'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['insurance_to'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['installments'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['agreement_data']['date_acceptation'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['client']['name'].'</td>';
                $htmlRow .= '<td>'.$row['agreement']['client']['NIP'].'</td>';
                $htmlRow .= '</tr>';
                $converted['exist'][] = $htmlRow;
            }
        }

        if(isset($parsedRows['mismatched']))
        {
            foreach($parsedRows['mismatched'] as $k => $row)
            {
                $htmlRow = '<tr>';
                $htmlRow .= '<td>'.++$k.'.</td>';
                $htmlRow .= '<td>'.$row['name'].'</td>';
                $htmlRow .= '<td>'.number_format($row['net_value'],2,"."," ").' zł</td>';
                $htmlRow .= '</tr>';
                $converted['mismatched'][] = $htmlRow;
            }
        }

        return $converted;
    }

    private function serializeHtmlRow($row, $lp)
    {
        $serialized = '';
        foreach($row['agreement']['agreement_data'] as $name => $value)
        {
            $serialized.= '<input name="agreements['.$lp.'][agreement_data]['.$name.']" type="hidden" value="'.htmlspecialchars($value).'"/>';
        }
        foreach($row['agreement']['client'] as $name => $value)
        {
            $serialized.= '<input name="agreements['.$lp.'][client]['.$name.']" type="hidden" value="'.htmlspecialchars($value).'"/>';
        }
        foreach ($row['objects'] as $k => $object) {
            foreach ($object as $name => $value) {
                $serialized .= '<input name="agreements[' . $lp . '][objects][' . $k . '][' . $name . ']" type="hidden" value="' . htmlspecialchars($value) . '"/>';
            }
        }


        return $serialized;
    }

    private function object_info($objects)
    {
        $html = '';
        foreach($objects as $k => $object)
        {
            $object_name = htmlspecialchars($object['name']);
            $html .= '<tr>';
            $html .= '<td>'.++$k.'. </td>';
            $html .= '<td> '.$object_name.' - </td>';
            $html .= '<td> <b> '.number_format($object['net_value'],2,"."," ").' zł</b></td>';
            $html .= '</tr>';
        }

        return $html;
    }

    private function parseMonths($installments)
    {
        return ceil($installments / 12) * 12;
    }


}
