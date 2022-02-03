<?php

namespace Idea\LeasingAgreements\ResumeAgreement;


use Idea\LeasingAgreements\AgreementDocumentParserInterface;
use Idea\LeasingAgreements\BaseAgreementDocumentParser;

class ResumeAgreementDocumentParser extends BaseAgreementDocumentParser implements AgreementDocumentParserInterface {

    function __construct($filename)
    {
        $path = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/resume/';
        $this->file = $path.$filename;

        if(!\File::exists($path)) {
            \File::makeDirectory($path,511, true);
        }
    }

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
                return $this->checkFileStructure();
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    function parse_rows()
    {
        $parsedRows = array();
        $agreementParser = new AgreementParser();
        foreach($this->rows as $k => $row)
        {
            if(isset($row['A']))
            {
                $parsedRow = $agreementParser->parseRow($row);
                $parsedRows[$parsedRow['status']][$parsedRow['row']['agreement']['nr_contract']]['client'] = $parsedRow['row']['client'];
                $parsedRows[$parsedRow['status']][$parsedRow['row']['agreement']['nr_contract']]['agreement'] = $parsedRow['row']['agreement'];
                $parsedRows[$parsedRow['status']][$parsedRow['row']['agreement']['nr_contract']]['insurance'] = $parsedRow['row']['insurance'];
                $parsedRows[$parsedRow['status']][$parsedRow['row']['agreement']['nr_contract']]['objects'][] = $parsedRow['row']['object'];

            }else
                return $this->parseFailed();
        }

        if(isset($parsedRows['existing_agreements']))
            $parsedRows['existing_agreements'] = $agreementParser->parseExistingAgreements($parsedRows['existing_agreements']);

        return $this->convertToHtml($parsedRows);
    }

    private function checkFileStructure()
    {
        if(!isset($this->rows[5]['A']) || $this->rows[5]['A'] != 'Przedmiot')
            return $this->parseFailed();

        if(!isset($this->rows[5]['Y']) || $this->rows[5]['Y'] != 'Wartość brutto')
            return $this->parseFailed();

        $this->rows = array_slice($this->rows, 4, (count($this->rows)-5));


        return true;
    }

    private function convertToHtml($parsedRows)
    {

        if(isset($parsedRows['existing_agreements'])) {
            if (isset($parsedRows['existing_agreements']['with_existing_insurance'])) {
                $k = 0;
                $now = \Date::now()->addMonth();
                foreach ($parsedRows['existing_agreements']['with_existing_insurance'] as $row) {
                    $htmlRow = '<tr';

                    $date_to = \Date::createFromFormat('Y-m-d', $row['insurance']['current_insurance']['date_to']);
                    if( $date_to > $now)
                        $htmlRow .= ' class = "danger" ';

                    $htmlRow .= '>';
                    $htmlRow .= '<td>' . ++$k;
                    $htmlRow .= $this->serializeHtmlRow($row, 'with_existing_insurance');
                    $htmlRow .= '.</td>';
                    $htmlRow .= '<td>' . $row['agreement']['nr_contract'] . '</td>';
                    $htmlRow .= '<td>' .$row['insurance']['current_insurance']['date_to'] . '</td>';
                    $htmlRow .= '<td>' . number_format($row['agreement']['loan_net_value'], 2, ".", " ") . ' zł</td>';
                    $htmlRow .= '<td>' . number_format($row['agreement']['loan_gross_value'], 2, ".", " ") . ' zł</td>';
                    $htmlRow .= '<td>' . $row['agreement']['rate'] . '</td>';
                    $htmlRow .= '<td>' . $row['agreement']['contribution'] . '</td>';
                    $htmlRow .= '<td>' . $row['agreement']['installments'] . '</td>';
                    $htmlRow .= '<td>' . $row['client']['name'] . '</td>';
                    $htmlRow .= '<td>' . $row['client']['NIP'] . '</td>';
                    $htmlRow .= '<td>';
                    $htmlRow .= '<a class="btn btn-sm btn-info btn-popover" tabindex="' . $k . '" role="button" data-toggle="popover" data-trigger="focus" title="przedmioty leasingu do umowy ' . $row['agreement']['nr_contract'] . '"';
                    $htmlRow .= 'data-content="' . $this->object_info($row['objects']) . '">';
                    $htmlRow .= 'przedm. leasingu <span class="badge">' . count($row['objects']) . '</span>';
                    $htmlRow .= '</a>';
                    $htmlRow .= '</td>';
                    $htmlRow .= '<td class="text-center"><input type="checkbox" name="toProceed['.$row['insurance']['parent_id'].']"/> </td>';
                    $htmlRow .= '</tr>';
                    $converted['with_existing_insurance'][] = $htmlRow;
                }
            }

            if (isset($parsedRows['existing_agreements']['without_existing_insurance'])) {
                $k = 0;
                foreach ($parsedRows['existing_agreements']['without_existing_insurance'] as $row) {
                    $htmlRow = '<tr>';
                    $htmlRow .= '<td>' . ++$k;
                    $htmlRow .= '.</td>';
                    $htmlRow .= '<td>' . $row['agreement']['nr_contract'] . '</td>';
                    $htmlRow .= '<td>' . number_format($row['agreement']['loan_net_value'], 2, ".", " ") . ' zł</td>';
                    $htmlRow .= '<td>' . number_format($row['agreement']['loan_gross_value'], 2, ".", " ") . ' zł</td>';
                    $htmlRow .= '<td>' . $row['agreement']['rate'] . '</td>';
                    $htmlRow .= '<td>' . $row['agreement']['contribution'] . '</td>';
                    $htmlRow .= '<td>' . $row['agreement']['installments'] . '</td>';
                    $htmlRow .= '<td>' . $row['client']['name'] . '</td>';
                    $htmlRow .= '<td>' . $row['client']['NIP'] . '</td>';
                    $htmlRow .= '<td>';
                    $htmlRow .= '<a class="btn btn-sm btn-info btn-popover" tabindex="' . $k . '" role="button" data-toggle="popover" data-trigger="focus" title="przedmioty leasingu do umowy ' . $row['agreement']['nr_contract'] . '"';
                    $htmlRow .= 'data-content="' . $this->object_info($row['objects']) . '">';
                    $htmlRow .= 'przedm. leasingu <span class="badge">' . count($row['objects']) . '</span>';
                    $htmlRow .= '</a>';
                    $htmlRow .= '</td>';
                    $htmlRow .= '</tr>';
                    $converted['without_existing_insurance'][] = $htmlRow;
                }
            }

            if(isset($parsedRows['existing_agreements']['in_archive'])){
                $k = 0;
                foreach ($parsedRows['existing_agreements']['in_archive'] as $row) {
                    $htmlRow = '<tr>';
                    $htmlRow .= '<td>' . ++$k;
                    $htmlRow .= '.</td>';
                    $htmlRow .= '<td>' . $row['agreement']['nr_contract'] . '</td>';
                    $htmlRow .= '<td>' . number_format($row['agreement']['loan_net_value'], 2, ".", " ") . ' zł</td>';
                    $htmlRow .= '<td>' . number_format($row['agreement']['loan_gross_value'], 2, ".", " ") . ' zł</td>';
                    $htmlRow .= '<td>' . $row['agreement']['rate'] . '</td>';
                    $htmlRow .= '<td>' . $row['agreement']['contribution'] . '</td>';
                    $htmlRow .= '<td>' . $row['agreement']['installments'] . '</td>';
                    $htmlRow .= '<td>' . $row['client']['name'] . '</td>';
                    $htmlRow .= '<td>' . $row['client']['NIP'] . '</td>';
                    $htmlRow .= '<td>';
                    $htmlRow .= '<a class="btn btn-sm btn-info btn-popover" tabindex="' . $k . '" role="button" data-toggle="popover" data-trigger="focus" title="przedmioty leasingu do umowy ' . $row['agreement']['nr_contract'] . '"';
                    $htmlRow .= 'data-content="' . $this->object_info($row['objects']) . '">';
                    $htmlRow .= 'przedm. leasingu <span class="badge">' . count($row['objects']) . '</span>';
                    $htmlRow .= '</a>';
                    $htmlRow .= '</td>';
                    $htmlRow .= '</tr>';
                    $converted['in_archive'][] = $htmlRow;
                }
            }
        }
        if(isset($parsedRows['missing_agreements'])) {
            $k = 0;
            foreach ($parsedRows['missing_agreements'] as $row) {
                $htmlRow = '<tr>';
                $htmlRow .= '<td>' . ++$k;
                $htmlRow .= '.</td>';
                $htmlRow .= '<td>' . $row['agreement']['nr_contract'] . '</td>';
                $htmlRow .= '<td>' . number_format($row['agreement']['loan_net_value'], 2, ".", " ") . ' zł</td>';
                $htmlRow .= '<td>' . number_format($row['agreement']['loan_gross_value'], 2, ".", " ") . ' zł</td>';
                $htmlRow .= '<td>' . $row['agreement']['rate'] . '</td>';
                $htmlRow .= '<td>' . $row['agreement']['contribution'] . '</td>';
                $htmlRow .= '<td>' . $row['agreement']['installments'] . '</td>';
                $htmlRow .= '<td>' . $row['client']['name'] . '</td>';
                $htmlRow .= '<td>' . $row['client']['NIP'] . '</td>';
                $htmlRow .= '<td>';
                $htmlRow .= '<a class="btn btn-sm btn-info btn-popover" tabindex="' . $k . '" role="button" data-toggle="popover" data-trigger="focus" title="przedmioty leasingu do umowy ' . $row['agreement']['nr_contract'] . '"';
                $htmlRow .= 'data-content="' . $this->object_info($row['objects']) . '">';
                $htmlRow .= 'przedm. leasingu <span class="badge">' . count($row['objects']) . '</span>';
                $htmlRow .= '</a>';
                $htmlRow .= '</td>';
                $htmlRow .= '</tr>';
                $converted['missing_agreements'][] = $htmlRow;
            }
        }

        return $converted;
    }

    private function serializeHtmlRow($row, $row_name)
    {
        $insurance = $row['insurance'];
        $serialized = '';

        foreach($insurance as $name => $value)
        {
            if(!is_array($value))
                $serialized.= '<input name="'.$row_name.'['.$insurance['parent_id'].']['.$name.']" type="hidden" value="'.htmlspecialchars($value).'"/>';
        }

        $serialized .=  '<input name="'.$row_name.'['.$insurance['parent_id'].'][agreement_id]" type="hidden" value="'.htmlspecialchars($row['agreement']['id']).'"/>';

        return $serialized;
    }

    private function object_info($objects)
    {
        $html = '';
        foreach($objects as $k => $object)
        {
            $html .= '<tr>';
            $html .= '<td>'.++$k.'. </td>';
            $html .= '<td> '.$object['name'].' - </td>';
            $html .= '</tr>';
        }

        return $html;
    }
}