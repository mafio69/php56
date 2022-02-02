<?php

namespace Idea\Reports\DosOtherReports;


use Config;
use DosOtherInjury;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use Injury;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdersReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

    private $params;
    private $filename;

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;
    }

    public function generateReport()
    {
        set_time_limit(5000);
        \DB::disableQueryLog();

        $response = new StreamedResponse(function() {
            // Open output stream
            $handle = fopen('php://output', 'w');

            fputs( $handle, "\xEF\xBB\xBF" );

            $headers = $this->generateTheads();

            fputcsv($handle, $headers, ';');

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            DosOtherInjury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                ->chunk(200, function($injuries) use (&$handle){
                    $injuries->load('user', 'object', 'object.owner', 'object.assetType', 'client', 'type_incident', 'injuries_type',
                        'invoices',  'uploaded_invoices', 'date_total_status', 'date_finished_status', 'historyEntries', 'leader', 'compensations');
                    foreach ($injuries as $k => $injury) {

                        $injuryType = $this->injuryType($injury);
                        $invoices = $this->invoices($injury);
                        $ct_invoices = count($invoices);
                        $value_of_invoices = $this->value_of_invoices($invoices);
                        $date_invoice = $this->getDateInvoice($injury);
                        $compensation = $this->getCompensation($injury);

                        $row = array(
                            ($injury->client_id == 0) ? '---' : $injury->client->name,
                            $injury->object->nr_contract,
                            $injury->object->contract_status,
                            $injury->object->description,
                            checkObjectIfNotNull($injury->object->assetType, 'name'),
                            $injury->object->factoryNbr,
                            $injury->object->owner->name,
                            $injury->case_nr,
                            substr($injury->created_at, 0, -3),
                            $injury->date_event,
                            (is_null($injury->date_end) || in_array($injury->step, ['-5', '-3'])) ? '---' : substr($injury->date_end, 0, -3),
                            $injury->status ? $injury->status->name : '',
                            $injuryType,
                            $injury->user->name,
                            $injury->leader ? $injury->leader->name : '',
                            $injury->injury_nr,
                            $ct_invoices,
                            $value_of_invoices,
                            ($injury->type_incident_id != 0 && $injury->type_incident_id != NULL) ? $injury->type_incident->name : '---',
                            $injury->injuries_type ? $injury->injuries_type->name : '',
                            $date_invoice,
                            $compensation['sum'],
                            $compensation['date'],
                            $injury->object->insurance_company_id > 0 ? $injury->object->insurance_company()->first()->name : null
                        );
                        fputcsv($handle, $row, ';');
                    }
                });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.($this->filename).'.csv"',
        ]);

        return $response;

        Excel::create($this->filename, function($excel) {

            $excel->sheet('Export', function($sheet) {

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $sheet->appendRow(array('Raport zleceń '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                DosOtherInjury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                    ->chunk(200, function($injuries) use ($sheet){
                        $injuries->load('user', 'object', 'object.owner', 'object.assetType', 'object.insurance_company', 'client', 'type_incident', 'injuries_type',
                            'invoices',  'uploaded_invoices', 'date_total_status', 'date_finished_status', 'historyEntries', 'leader');
                        foreach ($injuries as $k => $injury) {

                            $injuryType = $this->injuryType($injury);
                            $invoices = $this->invoices($injury);
                            $ct_invoices = count($invoices);
                            $value_of_invoices = $this->value_of_invoices($invoices);
                            $date_invoice = $this->getDateInvoice($injury);

                            $sheet->appendRow(array(
                                ($injury->client_id == 0) ? '---' : $injury->client->name,
                                $injury->object->nr_contract,
                                $injury->object->contract_status,
                                $injury->object->description,
                                checkObjectIfNotNull($injury->object->assetType, 'name'),
                                $injury->object->factoryNbr,
                                $injury->object->owner->name,
                                $injury->case_nr,
                                substr($injury->created_at, 0, -3),
                                $injury->date_event,
                                (is_null($injury->date_end) || in_array($injury->step, ['-5', '-3'])) ? '---' : substr($injury->date_end, 0, -3),
                                $injury->status ? $injury->status->name : '',
                                $injuryType,
                                $injury->user->name,
                                $injury->leader ? $injury->leader->name : '',
                                $injury->injury_nr,
                                $ct_invoices,
                                $value_of_invoices,
                                ($injury->type_incident_id != 0 && $injury->type_incident_id != NULL) ? $injury->type_incident->name : '---',
                                $injury->injuries_type->name,
                                $date_invoice,
                                $injury->object->insurance_company ? $injury->object->insurance_company->name : null
                            ));
                        }
                    });
            });

        })->export('xls');

    }



    private function injuryType($injury)
    {
        if ($injury->step == '-5')
            return 'całkowita';
        elseif ($injury->step == '-3')
            return 'kradzież';
        elseif(in_array($injury->step, ['10', '15', '17', '19', '20' ]))
            return 'częściowa';
        elseif(in_array($injury->step, ['0', '-10']))
        {
            if($injury->if_theft == 1)
                return 'kradzież';
            else
                return 'częściowa';
        }else{
            if(in_array('30', $injury->historyEntries->lists('history_type_id')) )
                return 'całkowita';
            else
                return 'kradzież';
        }
    }




    private function invoices($injury)
    {
        $invoicesA = array();
        foreach ($injury->invoices as $k => $invoice) {
            if ($invoice->active == 0) {
                if ($invoice->parent_id == 0)
                    $invoicesA[$invoice->id] = $invoice;
                else
                    $invoicesA[$invoice->parent_id] = $invoice;
            }
        }

        return $invoicesA;
    }

    private function value_of_invoices($invoices)
    {
        if (count($invoices) == 0) {
            return 0;
        } else {
            $sum = 0;
            foreach ($invoices as $k => $v) {
                $sum += $v->netto;
            }
        }

        return $sum;
    }

    public function generateTheads()
    {
        return array(
            'Klient',
            'Umowa',
            'Status Umowy',
            'OPIS',
            'Typ przedmiotu szkody',
            'Nr fabryczny',
            'Właściciel przedmiotu szkody',
            'Numer Szkody wewnętrzny',
            'Data Zgłoszenia',
            'Data Zdarzenia',
            'Data Zakończenia Szkody',
            'Status Szkody',
            'Typ Szkody',
            'Przyjmujący Szkodę',
            'Prowadzący Szkodę',
            'Numer Szkody Towarzystwa',
            'Liczba Faktur Kosztowych',
            'Wartość Netto z Faktury',
            'Rodzaj Zdarzenia',
            'Sposób Rozliczenia',
            'Data podpięcia FV',
            'Kwota odszkodowania',
            'Data decyzji',
            'Nazwa ZU',
        );
    }



    private function getDateInvoice($injury)
    {
        if($injury->uploaded_invoices->count() > 0)
        {
            return substr($injury->uploaded_invoices->first()->created_at,0,-3);
        }

        return '---';
    }

    private function getCompensation($injury)
    {
        $sumCompensation = 0;
        $date = '';

        foreach($injury->compensations as $k => $compensation) {
            if (!is_null($compensation->compensation)) {
                if ($compensation->injury_compensation_decision_type_id == 7) {
                    $compensation->compensation = abs($compensation->compensation) * -1;
                }
                $sumCompensation += $compensation->compensation;
                $date = $compensation->date_decision;
            }
        }

        return ['sum' => $sumCompensation, 'date' => $date];
    }
}
