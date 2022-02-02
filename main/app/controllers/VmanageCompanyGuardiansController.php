<?php

class VmanageCompanyGuardiansController extends \BaseController {


	/**
	 * VmanageCompanyGuardiansController constructor.
	 */
	public function __construct()
	{
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
	}


    public function getIndex($company_id)
    {
        $company = VmanageCompany::with('guardians')->find($company_id);
        $guardians = $company->guardians()->paginate(Session::get('search.pagin', '10'));

        return View::make('vmanage.companies.guardians.index', compact('company', 'guardians'));
    }

    public function getCreate($company_id)
    {
        $company = VmanageCompany::with('guardians')->find($company_id);
        return View::make('vmanage.companies.guardians.create', compact('company'));
    }

    public function postStore($company_id)
    {
        $input = Input::all();

        if(Input::has('existing_guardian'))
        {
            if(Input::has('user_id'))
            {
                $user = User::find($input['user_id']);
            }else{
                Flash::error('Nie wybrano poprawnie opiekuna floty');
                return Redirect::back()->withInput();
            }
        }else {
            $validator = Validator::make($input,
                array(
                    'login' => 'required|Unique:users',
                    'name' => 'required',
                    'password' => 'required',
                    'password_confirm' => 'required|same:password'
                )
            );

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }

            $input['password'] = Hash::make($input['password']);
        }

        $user->vmanage_companies()->attach($company_id);

        Flash::success('Opiekun '.$user->name.' został przypisany.');

        return Redirect::action('VmanageCompanyGuardiansController@getIndex', [$company_id]);
    }

    public function getEdit($guardian_id)
    {
        $guardian = User::find($guardian_id);

        return View::make('vmanage.companies.guardians.edit', compact('guardian'));
    }

    public function postUpdate($guardian_id)
    {
        $data = Input::all();
        $rules = array(
            'name'  => 'required',
            'login' => 'required|unique:users,login,'.$guardian_id
        );
        $validation = Validator::make($data, $rules);

        if ($validation->fails())
        {
            return json_encode(['code' => 2, 'error' => 'W systemie istnieje już użytkownik o podanym loginie.']);
        }

        $guardian = User::find($guardian_id);
        $guardian->update($data);

        Flash::success('Dane opiekuna floty zostały zmienione.');
        return json_encode(['code' => 0]);
    }

    public function getPassword($guardian_id)
    {
        return View::make('vmanage.companies.guardians.password', compact('guardian_id'));
    }

    public function postPassword($guardian_id)
    {
        $data = Input::all();
        $rules = array(
            'password'  => 'required',
            'password_confirm' => 'required|same:password'
        );
        $validation = Validator::make($data, $rules);

        if ($validation->fails())
        {
            return json_encode(['code' => 2, 'error' => 'Wprowadzone hasła muszą być identyczne.']);
        }

        $guardian = User::find($guardian_id);
        $data['password'] = Hash::make($data['password']);
        $guardian->update($data);

        Flash::success('Hasło opiekuna floty zostało zmienione.');
        return json_encode(['code' => 0]);
    }

    public function getDelete($guardian_id, $company_id)
    {
        $guardian = User::find($guardian_id);

        return View::make('vmanage.companies.guardians.delete', compact('guardian', 'company_id'));
    }

    public function postDestroy($guardian_id, $company_id)
    {
        $guardian = User::find($guardian_id);
        $guardian->vmanage_companies()->detach($company_id);

        Flash::success('Opiek floty został '.$guardian->name.' usunięty.');
        return json_encode(['code' => 0]);
    }

    public function postSearchGuardian()
    {
        $term = Input::get('term');
        $vmanage_company_id = Input::get('company_id');

        $guardians = User::
            whereNotExists(function($query) use($vmanage_company_id){
                $query->select(DB::raw(1))
                    ->from('vmanage_company_user')
                    ->whereRaw('vmanage_company_user.vmanage_company_id = '.$vmanage_company_id)
                    ->whereRaw('vmanage_company_user.user_id = users.id');
            })
            ->where(function($query) use($term){
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
}
