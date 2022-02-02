<?php
namespace Idea\Reports\InjuriesReports;

use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use Injury;
use InjuryFiles;

class OutdatedReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

            $excel->sheet('Raport zaległych rozrachunków', function($sheet) {
                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');


                $sheet->appendRow(array('Raport zaległych rozrachunków '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                Injury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                    ->whereHas('documents', function($query){
                        $query->where('category', 23)->where('active', 0)->where('type', 2);
                    })
                    ->where('step' , '!=' , '-10')
                    ->with('vehicle', 'client', 'documents')
                    ->chunk(200, function($injuries) use ($sheet){
                        foreach ($injuries as $k => $injury) {
                            $doc = null;
                            foreach($injury->documents as $document)
                            {
                                if($document->category == 23 && $document->type == 2 && $document->active == 0) {
                                    $doc = $document;
                                    break;
                                }
                            }
                            $sheet->appendRow(array(
                                $injury->client->firmID,
                                $injury->vehicle->nr_contract,
                                $doc->name,
                                substr($doc->created_at, 0, -3)
                            ));
                        }
                    });
            });

        })->export('xls');
    }

    public function generateTheads()
    {
        return array(
            'kod klienta',
            'numer umowy',
            'kwota zaległości',
            'data wprowadzenia dokumentu'
        );
    }
}