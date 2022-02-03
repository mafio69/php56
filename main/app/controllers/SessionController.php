<?php

class SessionController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

	public function setSearch(){
		if(!Input::has('locked_status'))
			Input::merge(array('locked_status' => '0'));

		if(!Input::has('yachts_filter'))
			Input::merge(array('yachts_filter' => '0'));

		if(!Input::has('foreign_policy'))
			Input::merge(array('foreign_policy' => '0'));


		foreach (Input::except('_token') as $key => $input) {
			Session::put('search.'.$key, $input);
		}


		return 0;
	}

	

}
