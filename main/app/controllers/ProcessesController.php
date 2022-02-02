<?php

class ProcessesController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:edycja_procesow#wejscie');
    }

	public function index()
	{

        $processes = DokProcesses::whereActive(0)->get();
        $processesTree = new DokProcesses_tree($processes);

        $processes = DokProcesses::whereActive(0)->with('processes', 'users')->orderBy('name', 'asc')->get();


        return View::make('settings.processes.index', compact('processes', 'processesTree'));
	}

	public function getInfo($id)
	{
		$process = DokProcesses::find($id);
		return View::make('settings.processes.info', compact('process'));
	}

    public function getInfoNode($id)
    {
        $process = DokProcesses::find($id);
        return View::make('settings.processes.info-node', compact('process'));
    }

	public function getEdit($id)
	{
		$process = DokProcesses::find($id);
		return View::make('settings.processes.edit', compact('process'));
	}

    public function getEditNode($id)
    {
        $process = DokProcesses::find($id);
        return View::make('settings.processes.edit-node', compact('process'));
    }

	public function set($id)
	{
		$process = DokProcesses::find($id);
		$process->weight = Input::get('weight');
		$process->time_limit = Input::get('time_limit');
        $process->description = Input::get('description');
		$process->save();
		return 0;

	}

    public function setNode($id)
    {
        $process = DokProcesses::find($id);
        $process->weight = Input::get('weight');
        $process->time_limit = Input::get('time_limit');
        $process->save();

        $process->setProcesses(Input::except('_token'));

        return 0;

    }

	public function getAppendUser($id)
	{
		$process = DokProcesses::find($id);
		return View::make('settings.processes.appendUser', compact('process'));
	}

	public function getSearchUsers($id)
	{
		$name = Input::get('q');

		$usersBefore = DokProcessesUsers::whereDok_processes_id($id)->get();
		$usersExistA = array();
		foreach ($usersBefore as $k => $user) {
			$usersExistA[] = $user->user_id;
		}

		if(count($usersExistA) > 0 )
        	$users = User::select('id', 'name as text')->where('active', '=', '0')->where('name', 'like', '%'.$name.'%')->whereNotIn('id', $usersExistA)->get();
        else
        	$users = User::select('id', 'name as text')->where('active', '=', '0')->where('name', 'like', '%'.$name.'%')->get();

        $result = array();
        foreach($users as $k => $v){
        	$result[] = array("id"=>$v->id, "text"=>$v->text);
        }

        return json_encode($result);
    }

    public function appendUser($id)
    {
    	if(Input::get('users') != ''){
			$users = explode(",", Input::get('users'));
			foreach($users as $k => $v){

				DokProcessesUsers::create(array(
						'dok_processes_id' 	=> $id,
						'user_id'			=> $v
					)
				);

			}
		}
		return 0;
    }

    public function getDeleteUser($id)
	{
		$user = DokProcessesUsers::find($id);
		return View::make('settings.processes.deleteUser', compact('user'));
	}

	public function deleteUser($id)
	{
		$user = DokProcessesUsers::find($id);
		$user->delete();
		return 0;
	}

    public function setPriority($id)
    {
        $process = DokProcesses::find($id);
        $process->priority = Input::get('val');
        $process->save();

        $result = array();
        $result['code'] = 0;
        $result['message'] = 'Ustawienia priorytetu zostały zmienione.';
        return json_encode($result);
    }

    public function setPriorityNode($id)
    {
        $process = DokProcesses::find($id);
        $process->priority = Input::get('priority');
        $process->save();

        $process->setProcesses(Input::except('_token'));

        $result = array();
        $result['code'] = 0;
        $result['message'] = 'Ustawienia priorytetu zostały zmienione.';
        return json_encode($result);

    }
}
