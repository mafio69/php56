<?php

namespace Idea\Reports\InjuriesReports;


use Carbon\Carbon;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;

class DocsReport extends BaseReport implements ReportsInterface
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

        Excel::create($this->filename, function($excel) {
            $excel->sheet('Export', function($sheet) {
                $sheet->appendRow($this->getHeaders());

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $users =  \User::
                            where(function($query) use($date_from, $date_to){
                                $query->whereHas('documents', function ($query) use($date_from, $date_to) {
                                    $query->where('active', 0)
                                        ->whereBetween('created_at', array($date_from, $date_to))
                                        ->whereHas('injury', function ($query)  {
                                            $query->where('active', 0)->where('step' , '!=' , '-10');
                                        });
                                })->orWhereHas('histories', function($query) use($date_from, $date_to){
                                    $query->where('history_type_id', 168)->whereBetween('created_at', array($date_from, $date_to));
                                });
                            })->get();

                foreach ($users as $k => $user) {
                    $sheet->appendRow(array(
                        $user->name,

                        $user->documents()->whereHas('injury', function($query){
                                $query->where('active', 0)->whereIn('step', [0, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25 ]);
                            })->where('type', 2)->whereBetween('created_at', array($date_from, $date_to))->count(),
                        $user->documents()->whereHas('injury', function($query){
                                $query->where('active', 0)->whereIn('step', [0, 10, 11, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25 ]);
                            })->where('type', 3)->whereBetween('created_at', array($date_from, $date_to))->count(),

                        $user->documents()->whereHas('injury', function($query){
                            $query->where('active', 0)->whereIn('step', [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7', '-5', '-3']);
                        })->where('type', 2)->whereBetween('created_at', array($date_from, $date_to))->count(),
                        $user->documents()->whereHas('injury', function($query){
                            $query->where('active', 0)->whereIn('step', [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7', '-5', '-3']);
                        })->where('type', 3)->whereBetween('created_at', array($date_from, $date_to))->count(),

                        $user->histories()->where('history_type_id', 168)->whereBetween('created_at', array($date_from, $date_to))->count()
                    ));
                }
            });

        })->export('xls');
    }

    private function getHeaders()
    {
        return [
            'Użytkownik',
            'Ilość wgranych dokumentów - szkody częściowe',
            'Ilość wygenerowanych dokumentów - szkody częściowe',
            'Ilość wgranych dokumentów - szkody całkowite',
            'Ilość wygenerowanych dokumentów - szkody całkowite',
            'Ilość wysłanych dokumentów'
        ];
    }

}