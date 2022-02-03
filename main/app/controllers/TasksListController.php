<?php

class TasksListController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:wykaz_zadan#wejscie');
    }

    public function getUnassigned()
    {
        $query = Task::has('currentInstance', '<', 1)->with('sourceType', 'group')->orderByRaw("FIELD(task_source_id , '2') DESC")->orderBy('id','desc');

        $this->passingWheres($query);

        $tasks = $query->paginate(Session::get('search.pagin', '20'));

        $taskUsers = ['' => '--- wybierz ---'] + User::whereIn('id', TaskInstance::lists('user_id', 'user_id'))->lists('name', 'id');

        return View::make('tasks.unassigned', compact('tasks', 'taskUsers'));
    }

    public function getNew()
    {

        $query = Task::whereHas('currentInstance', function($query){
            $query->where('task_step_id', 1);
        })->with('currentInstance', 'sourceType', 'group')
            ->orderByRaw("FIELD(task_source_id , '2') DESC")
            ->orderBy('current_task_instance_id','desc');

        $this->passingWheres($query);

        $taskUsers = ['' => '--- wybierz ---'] +  User::whereIn('id', TaskInstance::lists('user_id', 'user_id'))->lists('name', 'id');
        $tasks = $query->paginate(Session::get('search.pagin', '20'));

        return View::make('tasks.new', compact('tasks', 'taskUsers'));
    }

    public function getInprogress()
    {
        $query = Task::whereHas('currentInstance', function($query){
            $query->where('task_step_id', 2);
        })->with('currentInstance', 'sourceType', 'group')->orderByRaw("FIELD(task_source_id , '2') DESC")->orderBy('current_task_instance_id','desc');

        $this->passingWheres($query);

        $tasks = $query->paginate(Session::get('search.pagin', '20'));

        $taskUsers = ['' => '--- wybierz ---'] +  User::whereIn('id', TaskInstance::lists('user_id', 'user_id'))->lists('name', 'id');

        return View::make('tasks.inprogress', compact('tasks', 'taskUsers'));
    }

    public function getComplete()
    {
        $query = Task::whereHas('currentInstance', function($query){
            $query->where('task_step_id', 4);
        })->with('currentInstance', 'sourceType', 'type', 'group')->orderBy('current_task_instance_id','desc');

        $this->passingWheres($query);

        $tasks = $query->paginate(Session::get('search.pagin', '20'));

        $taskUsers = ['' => '--- wybierz ---'] +  User::whereIn('id', TaskInstance::lists('user_id', 'user_id'))->lists('name', 'id');

        return View::make('tasks.complete', compact('tasks', 'taskUsers'));
    }

    public function getCompleteWithoutAction()
    {
        $query = Task::whereHas('currentInstance', function($query){
            $query->where('task_step_id', 5);
        })->with('currentInstance', 'sourceType', 'group')->orderBy('current_task_instance_id','desc');

        $this->passingWheres($query);

        $tasks = $query->paginate(Session::get('search.pagin', '20'));

        $taskUsers = ['' => '--- wybierz ---'] +  User::whereIn('id', TaskInstance::lists('user_id', 'user_id'))->lists('name', 'id');

        return View::make('tasks.complete-without-action', compact('tasks', 'taskUsers'));
    }

    public function getGlobal()
    {
        $query = Task::with('currentInstance', 'sourceType', 'group')->orderByRaw("FIELD(task_source_id , '2') DESC")->orderBy('id','desc');

        $this->passingWheres($query);

        $tasks = $query->paginate(Session::get('search.pagin', '20'));

        $taskUsers = ['' => '--- wybierz ---'] +  User::whereIn('id', TaskInstance::lists('user_id', 'user_id'))->lists('name', 'id');

        return View::make('tasks.global', compact('tasks', 'taskUsers'));
    }

    public function getShow($task_id)
    {
        Session::put('tasks.card', $task_id);
        if(URL::previous() != Session::get('tasks.previous_url') && URL::previous() != URL::current()) {
            Session::put('tasks.previous_url', URL::previous());
        }
        $task = Task::with('injuries.vehicle', 'comments.user', 'replies.user')->find($task_id);

        if(! $task) return null;

        return View::make('tasks.show', compact('task'));
    }

    private function passingWheres($query)
    {
        if(Input::has('term')){
            if(Input::has('task_from')){
                $query->where(function($query){
                   $query->where('from_name', 'like', '%'.Input::get('term').'%')
                        ->orWhere('from_email', 'like', '%'.Input::get('term').'%');
                });
            }

            if(Input::has('task_subject')) {
                $query->where(function ($query) {
                    $query->where('subject', 'like', '%' . Input::get('term') . '%');
                });
            }

            if(Input::has('task_content')) {
                $query->where(function ($query) {
                    $query->where('content', 'like', '%' . Input::get('term') . '%');
                });
            }

            if(Input::has('case_nb')){
                $query->where('case_nb', 'like', Input::get('term'));
            }
        }

        if(Input::has('task_user_id')){
            $query->whereHas('currentInstance', function($query){
                $query->where('user_id', Input::get('task_user_id'));
            });
        }
    }

}