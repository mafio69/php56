<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 16.02.15
 * Time: 15:52
 */

namespace Idea\LeasingAgreements\CollectionAgreement;


use Config;
use DateTime;
use File;
use Idea\LeasingAgreements\AgreementDocumentParser;
use Idea\LeasingAgreements\BaseAgreementDocumentParser;

class CollectionAgreementDocumentParser extends BaseAgreementDocumentParser {

    private $fileHandler;

    function __construct($filename)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/collection/';
        $this->file = $path.$filename;

        if(!File::exists($path)) {
            File::makeDirectory($path,511, true);
        }
    }

    function load()
    {
        set_time_limit(500);

        if(file_exists($this->file)) {
            if(($handle = fopen($this->file, 'r')) !== false)
            {
                $this->fileHandler = $handle;
                return true;
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    function parse_rows($limit)
    {
        $header = fgetcsv($this->fileHandler);
        $countLine = 0;
        $parsed_rows = array();

        while($countLine < $limit && ($data = fgetcsv($this->fileHandler)) !== false)
        {
            $parsed_row = $this->parse_row($data);
            $parsed_rows[$parsed_row['nr_contract']][] = $parsed_row;

            unset($data);
            $countLine++;
        }
        fclose($this->fileHandler);

        $parsed_rows = $this->convertToHtml($parsed_rows);

        return $parsed_rows;
    }

    private function parse_agreements($rows)
    {
        $nr_contracts = array_keys($rows);

        $agreements = \LeasingAgreement::whereIn('nr_contract', $nr_contracts)->with('objects', 'insurances')->get();

        $parsedAgreements = array();
        foreach($agreements as $agreement)
        {
            $nr_contract = $agreement->nr_contract;
            $row_agreement = $rows[$nr_contract];

            $row_agreement['agreement']['id'] = $agreement->id;

            if($agreement->insurances->isEmpty()){
                $row_agreement['insurance']['leasing_agreement_id'] = $agreement->id;
                $parsedExistingAgreements['without_existing_insurance'][$nr_contract] = $row_agreement;
            }else{
                $row_agreement['insurance']['parent_id'] = $agreement->insurances->last()->id;
                $parsedExistingAgreements['with_existing_insurance'][$nr_contract] = $row_agreement;
            }
        }
    }

    private function parse_row($data)
    {
        $data = array_map('trim', $data);
        return array(
            "no" => $data[0],
            "nr_contract" => $data[1],
            "if_actual" => $this->yes_no($data[2]),
            "correction" => $data[3],
            "if_client_update" => $this->yes_no($data[4]),
            "if_refund_contribution" => $this->yes_no($data[5]),
            "agreement_type" => $data[10],
            "owner" => $data[20],
            "agreement_payment_way" => $data[26],
            "loan_net_value" => $data[27],
            "net_gross" => $data[28],
            "location" => $data[33],
            "if_load_decision" => $this->yes_no($data[35]),
            "insurance" => array(
                "insurance_number" => $data[6],
                'if_continuation' => $this->yes_no($data[7]),
                "insurance_type" => $data[8],
                "months" => $data[9],
                "notification_number" => $data[11],
                "insurance_date" => $this->formatDate($data[12]),
                "date_from" => $this->formatDate($data[13]),
                "date_to" => $this->formatDate($data[14]),
                "insurance_company" => $data[15],
                "rate" => $data[29],
                "contribution" => $data[30],
                "contribution_vbl" => $data[31],
                "refund" => $data[32]
            ),
            "client" => array(
                "name" => $data[16],
                "address" => $data[17],
                "REGON" => $data[18],
                "NIP" => $data[19]
            ),
            "object" => array(
                "agreement_insurance_group" => $data[34]
            )
        );
    }

    private function yes_no($value)
    {
        $value = strtoupper($value);
        if($value == 'TAK' || $value == 'YES')
            return true;
        else
            return false;
    }

    private function true_false($value)
    {
        if($value)
            return 'tak';
        else
            return 'nie';
    }

    private function formatDate($date){
        if($date != '') {
            $myDateTime = DateTime::createFromFormat('n/j/y', $date);
            $date = $myDateTime->format('Y-m-d');
        }
        return $date;
    }

    private function convertToHtml($parsed_rows)
    {
        $i = 0;
        foreach ($parsed_rows as $nr_contract => $row) {
            $htmlRow = '<tr>';
                $htmlRow .= '<td>' . ++$i . '</td>';
                $htmlRow .= '<td>'. $nr_contract .'</td>';
                $htmlRow .= '<td>'. $this->true_false(end($row)['if_actual']) . '</td>';
                $htmlRow .= '<td>'. end($row)['correction'] . '</td>';
                $htmlRow .= '<td>'. $this->true_false(end($row)['if_client_update']) . '</td>';
                $htmlRow .= '<td>'. $this->true_false(end($row)['if_refund_contribution']) . '</td>';
                $htmlRow .= '<td>'. end($row)['agreement_type'] . '</td>';
                $htmlRow .= '<td>'. end($row)['owner'] . '</td>';
                $htmlRow .= '<td>'. end($row)['agreement_payment_way'] . '</td>';
                $htmlRow .= '<td>'. number_format(end($row)['loan_net_value'], 2, ".", " ")  . '</td>';
                $htmlRow .= '<td>'. end($row)['net_gross'] . '</td>';
                $htmlRow .= '<td>'. end($row)['location'] . '</td>';
                $htmlRow .= '<td>'. $this->true_false(end($row)['if_load_decision']) . '</td>';
                $htmlRow .= '<td>'. end($row)['object']['agreement_insurance_group'] . '</td>';
                $htmlRow .= '<td><button type="button" class="btn btn-sm btn-info show_hide_insurances">polisy do umowy <span class="badge">' . count($row) . '</span></button></td>';
            $htmlRow .= '</tr>';
            $converted['agreements'][] = $htmlRow;

            $htmlRow = '<tr class="insurance_row" style="display: none;">';
                $htmlRow .= '<td colspan="15">';
                    $htmlRow .= '<table class="table table-condensed table-bordered">';
                        $htmlRow .= '<thead>';
                            $htmlRow .= '<th>lp.</th>';
                            $htmlRow .= '<th>Nr polisy</th>';
                            $htmlRow .= '<th>Czy kontynuacja</th>';
                            $htmlRow .= '<th>Typ polisy</th>';
                            $htmlRow .= '<th>Ile m-cy</th>';
                            $htmlRow .= '<th>Nr zgłoszenia</th>';
                            $htmlRow .= '<th>Data polisy</th>';
                            $htmlRow .= '<th>Polisa od</th>';
                            $htmlRow .= '<th>Polisa do</th>';
                            $htmlRow .= '<th>Umowa generalna</th>';
                            $htmlRow .= '<th>Składka</th>';
                            $htmlRow .= '<th>Stawka</th>';
                            $htmlRow .= '<th>Stawka VBL</th>';
                            $htmlRow .= '<th>Wysokość zwrotu</th>';
                        $htmlRow .= '</thead>';
                        $k = 0;
                        foreach($row as $agreement) {
                            $insurance = $agreement['insurance'];
                            $htmlRow .= '<tr class="info">';
                                $htmlRow .= '<td>' . ++$k . '</td>';
                                $htmlRow .= '<td>' . $insurance['insurance_number'] . '</td>';
                                $htmlRow .= '<td>' . $this->true_false($insurance['if_continuation']) . '</td>';
                                $htmlRow .= '<td>' . $insurance['insurance_type'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['months'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['notification_number'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['insurance_date'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['date_from'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['date_to'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['insurance_company'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['rate'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['contribution'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['contribution_vbl'] . '</td>';
                                $htmlRow .= '<td>' . $insurance['refund'] . '</td>';
                            $htmlRow .= '</tr>';
                        }
                    $htmlRow .= '</table>';
                $htmlRow .= '</td>';
            $htmlRow .= '</tr>';
            $converted['agreements'][] = $htmlRow;
        }
        return $converted;
    }

}