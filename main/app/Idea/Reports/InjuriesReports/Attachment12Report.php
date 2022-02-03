<?php
namespace Idea\Reports\InjuriesReports;


use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Injury;
use InjuryFiles;

class Attachment12Report extends BaseReport implements ReportsInterface
{
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

        $template = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/zal nr 12  ZESTAWIENIE WYSTAWIONYCH UPOWAZNIEN - raport.xlsx';

        Excel::load($template, function($excel)  {

            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();

            $row = 3;

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

                    InjuryFiles::whereHas('injury', function ($query){
                         $query->where('active',0)->where('step' , '!=' , '-10');
                    }
                    )->with(['injury.user', 'injury.vehicle', 'injury.client'])->whereActive(0)->whereType(3)->whereBetween('created_at', array($date_from, $date_to))->where('if_fee_collected', true)->with('user')->orderBy('created_at')->chunk(200, function($filesA) use (&$sheet, $date_to, $date_from, &$row){
                    foreach ($filesA as $k => $file) {
                        $injury = $file->injury;
                            $rowsToInsert = array(
                                $row - 2,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->registration,
                                $injury->injury_nr,
                                ($injury->client_id == 0) ? '---' : $injury->client->name,
                                $injury->vehicle->program, // program sprzedaży
                                substr($file->created_at, 0, -3),
                                $file->user->name,
                                $injury->date_event,
                            );
                            $sheet->fromArray($rowsToInsert, null, 'A' . $row);
                            $row++;
                    }

                });
        })->setFileName($this->filename)->export('xls');
    }


}