<?php
namespace Idea\Reports\InjuriesReports;

use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use Injury;
use InjuryFiles;

class InjuriesBuyerReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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
        \DB::disableQueryLog();

        Excel::create($this->filename, function($excel) {

            $excel->sheet('Raport nabywcow', function($sheet) {
                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');


                $sheet->appendRow(array('Raport nabywcow '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                Injury::whereBetween('created_at', array($date_from, $date_to))
                    ->where('active', '=', '0')->whereIn('step', [30,31,32,33,34,35,36,37,44,45,46,47,'-7'])
                    ->chunk(200, function($injuries) use ($sheet){
                        $injuries->load('wreck');

                        foreach($injuries as $injury)
                        {
                            if($injury->wreck && $injury->wreck->buyer > 0) {
                                $sheet->appendRow(array(
                                    $injury->case_nr,
                                    $injury->injury_nr,
                                    substr($injury->created_at, 0, -3),
                                    Config::get('definition.wreck_buyers')[$injury->wreck->buyer]
                                ));
                            }
                        }
                    });
            });

        })->export('xls');
    }

    public function generateTheads()
    {
        return array(
            'nr sprawy',
            'nr szkody',
            'data zgłoszenia',
            'nabywca'
        );
    }
}