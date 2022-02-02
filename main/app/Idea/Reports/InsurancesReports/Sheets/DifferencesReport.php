<?php

namespace Idea\Reports\InsurancesReports\Sheets;

use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class DifferencesReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

    private $params;
    private $filename;

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;
    }

    public function generateReport()
    {
        set_time_limit(500);

        Excel::create($this->filename, function($excel) {

            $excel->sheet('Export', function($sheet) {

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $sheet->appendRow(array('Zestawienie różnic '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                \LeasingAgreement::
                    whereBetween('created_at', array($date_from, $date_to))
                    ->has('insurances', '=', 1)
                    ->whereHas('insurances', function($query){
                        $query->where('insurance_company_id',$this->params['insurance_company_id']);
                    })
                    ->where(function($query){
                        $query->where(function($query){
                            $query->whereNotNull('initial_contribution')->whereRaw(' `leasing_agreements`.`initial_contribution` != `leasing_agreements`.`contribution`');
                        })->orWhere(function($query){
                            $query->whereNotNull('initial_rate')->whereRaw(' `leasing_agreements`.`initial_rate` != `leasing_agreements`.`rate`');
                        });

                    })
                    ->with('insurances', 'insurances.mismatchingReason')
                    ->chunk(300, function($leasing_agreements) use (&$sheet){
                        foreach($leasing_agreements as $leasing_agreement){
                            $sheet->appendRow(array(
                                $leasing_agreement->nr_contract,
                                $leasing_agreement->initial_contribution,
                                $leasing_agreement->contribution,
                                ($leasing_agreement->initial_contribution - $leasing_agreement->contribution),
                                $leasing_agreement->initial_rate,
                                $leasing_agreement->rate,
                                ($leasing_agreement->initial_rate - $leasing_agreement->rate),
                                ($leasing_agreement->insurances->first()->mismatchingReason) ? $leasing_agreement->insurances->first()->mismatchingReason->name : '---'
                            ));
                        }

                    });
            });

        })->export('xls');
    }

    public function generateTheads()
    {
        return [
            'nr umowy',
            'składka z zestawienia IL',
            'składka z zestawienia EDB',
            'różnica składek (kwota)',
            'stawka z zestawienia IL',
            'stawka z zestawienia EDB',
            'różnica stawek',
            'przyczyna różnicy'
        ];
    }
}