<?php


namespace Idea\Observers;


class TaskObserver
{
    public function creating($model)
    {
        $last_task = \Task::orderBy('id', 'desc')->first();

        if ($last_task) {
            $case_nb = $last_task->case_nb;
            if (substr($case_nb, -4) == date('Y')) {
                $case_nb = intval(substr($case_nb, 0, -5));
                $case_nb++;
                $case_nb .= '/' . date('Y');
            } else {
                $case_nb = '1/' . date('Y');
            }
        } else {
            $case_nb = '1/' . date('Y');
        }

        $model->case_nb = $case_nb;
    }

    public function created($model)
    {
        \Cache::forget('task.stats');
    }
}