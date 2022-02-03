<?php

class VmanageUsersController extends \BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }


    public function getCreate($id)
    {
        $company = VmanageCompany::find($id);

        return View::make('vmanage.companies.users.create', compact('company'));
    }

    public function postStore()
    {
        $result = array();
        $find_user = VmanageUser::whereName(Input::get('name'))->whereSurname(Input::get('surname'))->where('vmanage_company_id', Input::get('vmanage_company_id'))->get();
        if( ! $find_user->isEmpty() ){
            $result['code'] = 1;
            $result['message'] = "Istnieje już użytkownik o podanym imieniu i nazwisku.";
        }else{
            $user_db = VmanageUser::create(Input::all());
            $result['user']['id'] = $user_db->id ;
            $result['user']['value'] = $user_db['name'].' '.$user_db['surname'];
            $result['code'] = 0;
        }

        return json_encode($result);
    }

}
