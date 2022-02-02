<?php

use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskReportsController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:raporty#wejscie');
    }

    public function getIndex()
    {
        return View::make('tasks.reports.index');
    }

    public function postUsers()
    {
        \DB::disableQueryLog();
        $filename  = 'raport-obsługi-'.date('Y-m-d');

        $response = new StreamedResponse(function(){
            // Open output stream
            $handle = fopen('php://output', 'w');

            fputs( $handle, "\xEF\xBB\xBF" );


            $date_from = $this->parseDate(Input::get('date_from'), '0');
            $date_to = $this->parseDate(Input::get('date_to'), '+1');

            fputcsv($handle,array('Raport przekierowań '.Input::get('date_from').' - '.Input::get('date_to')), ';');
            fputcsv($handle,array(), ';');

            fputcsv($handle, [
                'numer sprawy',
                'osoba obsługująca',
                'data przydzielenia',
                'data pobrania',
                'data zakończenia',
                'aktualny status',
                'ilość dni w obsłudze',
                'opis sprawy zakończonej'
                ], ';');


            TaskInstance::whereBetween('created_at', array($date_from, $date_to))
                ->with('task',  'step', 'user')
                ->chunk(100, function ($taskInstances) use(&$handle){
                    foreach ($taskInstances as $k => $taskInstance) {
                        if($taskInstance->date_complete)
                        {
                            $days_on_board = $taskInstance->created_at->diffInDays($taskInstance->date_complete);
                        }else {
                            $days_on_board = $taskInstance->created_at->diffInDays( \Carbon\Carbon::now() );
                        }
                        fputcsv($handle, array(
                            $taskInstance->task->case_nb,
                            $taskInstance->user->name,
                            $taskInstance->created_at->format('Y-m-d H:i'),
                            $taskInstance->date_collect ? $taskInstance->date_collect->format('Y-m-d H:i') : '',
                            $taskInstance->date_complete ? $taskInstance->date_complete->format('Y-m-d H:i') : '',
                            $taskInstance->step->name,
                            $days_on_board,
                            $taskInstance->task_step_id == 5 ? $taskInstance->latestHistory->description : ''
                        ), ';');
                    }
                });

        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
        ]);

        return $response;
    }

    protected function parseDate($date, $modify_days)
    {
        if($date != '') {
            $date_from = new DateTime($date);
            $date_from->modify($modify_days.' day');

            return $date_from->format('Y-m-d H:i:s');
        }
        return 0;
    }

}