<?php
namespace Idea\Reports\CustomReports;

use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Injury;

class ByContract extends BaseReport implements ReportsInterface{

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

            $excel->sheet('Raport wg '.$this->reportType->default_term, function($sheet) {

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $injuries = Injury::where('active', '=', '0')
                    ->where(function($query) use($date_from, $date_to)
                    {
                        if($date_from != 0)
                            $query->where('created_at', '>=', $date_from);
                        if($date_to != 0)
                            $query->where('created_at', '<=', $date_to);
                    })->vehicleExists('nr_contract', $this->reportType->default_term, 'where')
                    ->whereIn('step', array(0,10, 15, 17, 19, 20))
                    ->distinct()
                    ->with('vehicle')->get();

                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Arial',
                        'size'      =>  8
                    )
                ));

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'B' => '@',
                    'C' => '@'
                ));

                $sheet->setAutoSize(array(
                    'B', 'C'
                ));

                $sheet->loadView('reports.custom.templates.byContract', array('injuries' => $injuries, 'from' => $this->params['date_from'], 'to' => $this->params['date_to'], 'term' => $this->reportType->default_term ) );

            });

        })->download();

    }
}