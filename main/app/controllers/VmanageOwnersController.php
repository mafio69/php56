<?php

class VmanageOwnersController extends \BaseController {

	/**
	 * VmanageOwnersController constructor.
	 */
	public function __construct()
	{
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
	}

    public function getCreate()
    {
        return View::make('vmanage.companies.owners.create');
    }

    public function postStore()
    {
        $result = array();
        $find_user = Owners::whereName(Input::get('name'))->get();
        if( ! $find_user->isEmpty() ){
            $result['code'] = 1;
            $result['message'] = "Istnieje już firma o podanej nazwie.";
        }else{
            $data = Input::all();
            $data['short_name'] = shortenName($data['name']);
            $data['owners_group_id'] = 4;
            $user_db = Owners::create($data);
            $result['owner']['id'] = $user_db->id ;
            $result['owner']['value'] = $user_db['name'];
            $result['code'] = 0;
        }

        return json_encode($result);
    }

    public function postSearchOwner()
    {
        $term = Input::get('term');
        $owner_id = Input::get('owner_id');

        $owners = Owners::where('id', '!=', $owner_id)
                ->where('owners_group_id', '>', 3)
                ->where('name', 'like', '%'.$term.'%')
                ->get();
        $result = array();

        foreach($owners as $k => $v){
            $result[] = array(
                "id"=>$v->id,
                "label"=> $v->name,
                "value" => $v->name
            );
        }

        return json_encode($result);
    }

}
