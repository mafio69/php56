<?php
namespace Idea\Reports\CustomReports;

use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use InjuryFiles;

class Authorizations extends BaseReport implements ReportsInterface{

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
            $excel->sheet('Ubezpieczenie pakietowe', function($sheet) {

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $authorizations = InjuryFiles::whereBetween('created_at', array( $date_from, $date_to ) )->whereType(3)->whereActive(0)
                    ->whereHas('injury', function($query){
                        $query->whereActive(0)->vehicleExists('cfm', '0', 'where')->where('step' , '!=' , '-10');
                    })
                    ->whereHas('document_type', function($query){
                        $query->whereHas('ownersGroups', function($query){
                            $query->whereIn('id', [2,3]);
                        })->whereTask_authorization(1);
                    })
                    ->with('injury', 'document_type', 'injury.client', 'injury.vehicle', 'injury.injuries_type')
                    ->get();

                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Arial',
                        'size'      =>  8
                    )
                ));

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'B' => '@',
                    'C' => '@',
                    'D' => '@',
                    'E' => '@',
                    'F' => '@',
                    'G' => '@',
                    'H' => '@',
                    'J' => '_-* #,##0.00\ [$zł-415]_-'
                ));

                $sheet->loadView('reports.custom.templates.authorizations',
                    array('authorizations' => $authorizations, 'from' => $this->params['date_from'], 'to' => $this->params['date_to']) );

            });

        })->download('xls');
    }
}