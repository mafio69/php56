<?php
namespace Idea\Reports\CustomReports;


use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Injury;

class ReportOC extends BaseReport implements ReportsInterface{

    private $params;
    private $filename;
    private $reportType;

    function __construct($reportType, $filename, $params = array())
    {
        $this->reportType = $reportType;
        $this->params = $params;
        $this->filename = $filename;
    }

    public function generateReport()
    {
        set_time_limit(500);

        return Excel::create($this->filename, function($excel) {

            $excel->sheet('Raport OC za 2014r.', function($sheet) {

                $sheet->appendRow(array('Raport OC za 2014r. '));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                $date_from = '2014-01-01';
                $date_to = '2014-12-31';

                Injury::whereBetween('created_at', array( $date_from, $date_to ) )
                    ->where('step' , '!=' , '-10')
                    ->whereIn('injuries_type_id', array(2,5,6))->whereActive(0)
                    ->with( 'injuries_type',  'vehicle', 'type_incident', 'getInfo', 'invoices', 'type_incident', 'branch',
                            'getRemarks', 'damages', 'damages.damage', 'status')
                    ->chunk(100, function($injuries) use (&$sheet) {
                        foreach ($injuries as $k => $injury) {
                            $sheet->appendRow(array(
                                $injury->injuries_type->name,
                                checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand),
                                checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model),
                                ($injury->getRemarks) ? $injury->getRemarks->content : '---',
                                (!$injury->damages->isEmpty()) ? $this->concatDamages($injury->damages) : '---',
                                $injury->event_post.' '.$injury->event_city.' '.$injury->event_street,
                                $injury->date_event,
                                ($injury->type_incident) ? $injury->type_incident->name : '---',
                                ($injury->branch) ? $injury->branch->short_name.' - '.$injury->branch->post.' '.$injury->branch->city.', '.$injury->branch->street : '---',
                                ($injury->getInfo) ? $injury->getInfo->content : '---',
                                (!$injury->invoices->isEmpty()) ? $this->countInvoices($injury->invoices) : '---',
                                $injury->status->name
                            ));
                        }
                    });

            });

        })->download();

    }

    private function generateTheads()
    {
        return array(
            'rodzaj szkody',
            'marka',
            'model',
            'opis zdarzenia',
            'uszkodzenia',
            'miejsce zdarzenia',
            'data zdarzenia',
            'rodzaj zdarzenia',
            'serwis',
            'uwagi',
            'wysokość odszkodowania',
            'status szkody'
        );
    }

    private function concatDamages($damages)
    {
        $result = '';
        foreach($damages as $damage)
        {
            $result .= $damage->damage->name;
            if($damage->param != 0) {
                if ($damage->param == 1)
                    $result .= ' lewe/y';
                else
                    $result .= ' prawe/y';
            }
            $result .= ', ';
        }
        return $result;
    }

    private function countInvoices($invoices)
    {
        $value = 0;
        foreach($invoices as $invoice)
        {
            $value += $invoice->netto+$invoice->vat;
        }
        return money_format("%.2n",$value);
    }
}