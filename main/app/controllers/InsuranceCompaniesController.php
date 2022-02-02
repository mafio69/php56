<?php

class InsuranceCompaniesController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:lista_ubezpieczalni#wejscie');
    }

	public function index()
	{
        $insurance_companies = Insurance_companies::where('active', '=', '0')->orderBy('name', 'asc')->whereNull('parent_id')->paginate(Session::get('search.pagin', '20'));

        return View::make('settings.insurance_companies.index', compact('insurance_companies'));
	}


	public function getCreate()
	{
        return View::make('settings.insurance_companies.create');
	}

	public function getCreateInjury()
	{
		return View::make('settings.insurance_companies.create-in-injury');
	}

	public function postCreate()
	{
		$input = Input::all();

		$insurance_company = Insurance_companies::where('name', 'like', Input::get('name'))->first();

		if($insurance_company) {
            if ($insurance_company->active == 0) {
                return json_encode(['error' => 'Istnieje już w systemie ubezpieczyciel o podanej nazwie.']);
            } else {
                $insurance_company->update([
                    'post' => Input::get('post'),
                    'street' => Input::get('street'),
                    'city' => Input::get('city'),
                    'contact_person' => Input::get('contact_person'),
                    'email' => Input::get('email'),
                    'phone' => Input::get('phone'),
                    'if_rounding' => (Input::has('if_rounding')) ? 1 : 0,
                    'if_full_year' => (Input::has('if_full_year')) ? 1 : 0
                ]);

                return json_encode(['code' => 0]);
            }
        }

		$validator = Validator::make($input ,
			array(
				'name' => 'required'
			)
		);

		if($validator -> fails()){
			return json_encode(['error' => 'Pole nazwa jest wymagane.']);
		}else{
			$insurance_company = Insurance_companies::create(array(
					'name' => Input::get('name'),
				    'post' => Input::get('post'),
				    'street' => Input::get('street'),
				    'city' => Input::get('city'),
				    'contact_person' => Input::get('contact_person'),
				    'email'	=> Input::get('email'),
				    'phone'	=> Input::get('phone'),
                    'if_rounding' => (Input::has('if_rounding')) ? 1 : 0,
					'if_full_year' => (Input::has('if_full_year')) ? 1 : 0
				));

			if($insurance_company){
				return json_encode(['code' => 0]);
			}else{
				return json_encode(['error' => 'Wystąpił błąd w trakcie dodawania ubezpieczalni. Skontaktuj się z administratorem.']);
			}
		}
	}

	public function getEdit($id)
	{
		$insurance_company = Insurance_companies::find( $id );

        return View::make('settings.insurance_companies.edit', compact('insurance_company'));
	}

	public function set($id)
	{
		$input = Input::all();

		$validator = Validator::make($input ,
			array(
				'name' => 'required|Unique:insurance_companies,name,'.$id
			)
		);

		if($validator -> fails()){
			return json_encode(['error' => 'Istnieje już w systemie ubezpieczyciel o podanej nazwie.']);
		}

		$insurance_company = Insurance_companies::find( $id );

		$insurance_company->name = Input::get('name');
	    $insurance_company->post = Input::get('post');
	    $insurance_company->street = Input::get('street');
	    $insurance_company->city = Input::get('city');
	    $insurance_company->contact_person = Input::get('contact_person');
	    $insurance_company->email = Input::get('email');
	    $insurance_company->phone = Input::get('phone');
		$insurance_company->if_rounding = (Input::has('if_rounding')) ? 1 : 0;
		$insurance_company->if_full_year = (Input::has('if_full_year')) ? 1 : 0;

		if($insurance_company->save()){
			return json_encode(['code' => 0]);
		}else{
			return json_encode(['error' => 'Wystąpił błąd w trakcie zapisu zmian. Skontaktuj się z administratorem.']);
		}
	}

	public function getDelete($id)
	{
		$insurance_company = Insurance_companies::find( $id );

        return View::make('settings.insurance_companies.delete', compact('insurance_company'));
	}

	public function getSetParent($id)
	{
		$insurance_company = Insurance_companies::find( $id );
		$insurance_companies = Insurance_companies::where('active', '=', '0')->where('id','!=', $id)->whereNull('parent_id')->orderBy('name', 'asc')->get();


        return View::make('settings.insurance_companies.set-parent', compact('insurance_company', 'insurance_companies'));
	}

	public function postSetParent($id)
	{
		$insurance_company = Insurance_companies::findOrFail( $id );
		$new_parent = Insurance_companies::find( Request::get('parent_id') );
		$insurance_company->parent_id = $new_parent?$new_parent->id:null;
		if($new_parent) {
			$insurance_company->parent_id = $new_parent->id;
			if(is_null($insurance_company->street))$insurance_company->street = $new_parent->street;
			if(is_null($insurance_company->post))$insurance_company->post = $new_parent->post;
			if(is_null($insurance_company->city))$insurance_company->city = $new_parent->city;
			if(is_null($insurance_company->email))$insurance_company->email = $new_parent->email;
			if(is_null($insurance_company->phone))$insurance_company->phone = $new_parent->phone;

		} else {
			$insurance_company->parent_id = null;
		}
		$insurance_company->save();

		return json_encode(['code' => 0]);
	}

	public function delete($id)
	{
		$insurance_company = Insurance_companies::find( $id );

	    $insurance_company->active = 9;

		if($insurance_company->save()){
            $insurance_company->touch();
			return json_encode(['code' => 0]);
		}else{
			return json_encode(['error' => 'Wystąpił błąd w trakcie zapisu zmian. Skontaktuj się z administratorem.']);
		}
	}

	public function getList(){
		$insurance_companies = Insurance_companies::where('active', '=', '0')->orderBy('name', 'asc')->whereNull('parent_id')->get();

		$last_id = $insurance_companies->max('id');

		$result = '<option value="">---wybierz---</option>';

		foreach ($insurance_companies as $key => $company) {
			if($company->id == $last_id) $select = "selected";
			else $select = "";
    		$result .= '<option value="'.$company->id.'" '.$select.'>'.$company->name.'</option>';
		}
		return $result;
	}

}
