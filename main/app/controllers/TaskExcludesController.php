<?php

class TaskExcludesController extends \BaseController {

    public function __construct()
    {
    }

	public function getIndex()
	{
		$users = User::whereIn( 'id',TaskExclude::lists('user_id', 'user_id') )->with(
		    [
		        'taskExcludes' => function($query){
                    $query->whereDate('absence', '>=', \Carbon\Carbon::now()->startOfDay());
                },
                'taskInstances' => function($query){
		            $query->whereIn('task_step_id' , [1,2]);
                }
            ])->paginate(Session::get('search.pagin', '20'));

		return View::make('tasks.excludes.index', compact('users'));
	}

	public function getCreate()
    {
        return View::make('tasks.excludes.create');
    }

    public function postStore()
    {
        if(!Input::get('user_id')){
            Flash::error('Proszę wskazać pracownika');
            return Redirect::back();
        }

        TaskExclude::where('user_id', Input::get('user_id'))->whereDate('absence', '>=', \Carbon\Carbon::now()->startOfDay())->delete();

        foreach(explode(',', Input::get('days')) as $day){
            if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$day)) {
                TaskExclude::create([
                    'technical_user_id' => Auth::user()->id,
                    'user_id' => Input::get('user_id'),
                    'absence' => $day
                ]);
            }
        }

        return Redirect::to(url('tasks/excludes'));
    }

    public function getAbsences($user_id){
        $absence_user = User::with(['taskExcludes' => function($query){
            $query->whereDate('absence', '>=', \Carbon\Carbon::now()->startOfDay());
        }])->find($user_id);

        return View::make('tasks.excludes.absences', compact('absence_user'));
    }


    public function postUpdate($user_id)
    {
        TaskExclude::where('user_id', $user_id)->where(function($query){
            $query->whereDate('absence', '>=', \Carbon\Carbon::now()->startOfDay())->orWhere('absence', '0000-00-00');
        })->delete();

        foreach(explode(',', Input::get('days')) as $day){
            if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$day)) {
                TaskExclude::create([
                    'technical_user_id' => Auth::user()->id,
                    'user_id' => $user_id,
                    'absence' => $day
                ]);
            }
        }

        return Redirect::to(url('tasks/excludes'));
    }

    public function getDistribute($user_id)
    {
        $absence_user = User::find($user_id);

        return View::make('tasks.excludes.distribute', compact('absence_user'));
    }

    public function postDistribute($user_id)
    {
        $absence_user = User::with(['taskInstances' => function($query){
            $query->whereIn('task_step_id' , [1,2]);
        }])->find($user_id);

        foreach($absence_user->taskInstances as $taskInstance)
        {
            $taskInstance->update([
                'date_complete' => \Carbon\Carbon::now(),
                'task_step_id' => 6
            ]);

            TaskStepHistory::create([
                'task_instance_id' => $taskInstance->id,
                'task_step_id' => 6
            ]);

            $status = \Idea\Tasker\Tasker::assign($taskInstance->task, [$taskInstance->user_id]);
            if($status['status'] == 'error'){
                $taskInstance->task->update(['current_task_instance_id' => null]);
            }
        }

        Cache::forget('task.stats');
        Cache::forget('task.user.'.$user_id);

        return json_encode(['code' => 0]);
    }

    public function getTasks($user_id)
    {
        $absence_user = User::with(['taskInstances' => function($query){
            $query->whereIn('task_step_id' , [1,2]);
        }])->find($user_id);

        return View::make('tasks.excludes.tasks', compact('absence_user'));
    }

    public function getDistributeSingle($user_id, $task_instance_id)
    {
        $absence_user = User::find($user_id);

        return View::make('tasks.excludes.distribute-single', compact('absence_user', 'task_instance_id'));
    }

    public function postDistributeSingle($user_id, $task_instance_id)
    {
        $taskInstance = TaskInstance::find($task_instance_id);
        $taskInstance->update([
            'date_complete' => \Carbon\Carbon::now(),
            'task_step_id' => 6
        ]);

        TaskStepHistory::create([
            'task_instance_id' => $taskInstance->id,
            'task_step_id' => 6
        ]);

        $status = \Idea\Tasker\Tasker::assign($taskInstance->task, [$taskInstance->user_id]);
        if($status['status'] == 'error'){
            $taskInstance->task->update(['current_task_instance_id' => null]);
        }

        Cache::forget('task.stats');
        Cache::forget('task.user.'.\Auth::user()->id);

        return json_encode(['code' => 0]);
    }
}