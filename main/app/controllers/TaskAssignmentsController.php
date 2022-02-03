<?php

class TaskAssignmentsController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('permitted:przypisania_indywidualne#wejscie');
    }

	public function getIndex()
	{
		$assignments = TaskAssignment::with('user')->get();

		return View::make('tasks.assignments.index', compact('assignments'));
	}

    public function getCreate()
    {
        return View::make('tasks.assignments.create');
    }


    public function postSearch()
    {
        $term = Input::get('term');

        $guardians = User::where(function($query) use($term){
                $query->where('login', 'like', '%'.$term.'%');
                $query->orWhere('name', 'like', '%'.$term.'%');
            })
            ->get();

        $result = array();

        foreach($guardians as $k => $v){
            $result[] = array(
                "id" => $v->id,
                "label" => $v->name . ' - ' . $v->login,
                "value" => $v->name . ' - ' . $v->login,
                'login' => $v->login,
                'name' => $v->name
            );
        }

        return json_encode($result);
    }

    public function postStore()
    {
        TaskAssignment::create([
            'email_from' => Input::get('email'),
            'user_id' => Input::get('user_id')
        ]);

        return Redirect::to('tasks/assignments');
    }

    public function getDelete($id)
    {
        $assignment = TaskAssignment::find($id);

        return View::make('tasks.assignments.delete', compact('assignment'));
    }

    public function postDelete($id)
    {
        $assignment = TaskAssignment::find($id);
        $assignment->delete();

        return json_encode(['code' => 0]);
    }
}