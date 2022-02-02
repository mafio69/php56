<?php

class VmanageVehiclesController extends \BaseController {

	private $managed_fleet;

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));

        $this->managed_fleet = Auth::user()->vmanage_companies()->get()->lists('id', 'name');
	}



	public function getIndex($id = null, $if_truck = 0)
	{
        if(is_null($id))
            $id = head($this->managed_fleet);
        else if( ! in_array($id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        if(! $id)
        {
            return View::make('vmanage.companies.vehicles.blank', compact('if_truck'));
        }

        $company = VmanageCompany::with('owner')->find($id);
		if($if_truck == 0) {
            $vehicles = $company->vehicles();
        }else{
            $vehicles = $company->trucks();
        }

        $vehicles = $vehicles->where(function ($query) {
                $this->passingWheres($query);
            })
            ->with('brand', 'model', 'user')->paginate(Session::get('search.pagin', '10'));

        $managed_fleet = $this->managed_fleet;
        return View::make('vmanage.companies.vehicles.guardian_index', compact('company', 'vehicles', 'managed_fleet', 'if_truck'));

        return View::make('vmanage.companies.vehicles.index', compact('company', 'vehicles', 'if_truck'));
	}


	public function getCreate($id)
	{
        if( ! in_array($id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

		$company = VmanageCompany::find($id);

		$engines_db = Car_engines::all();
		$engines = generateSelectOptions($engines_db);

		$gearboxes_db = Car_gearboxes::all();
		$gearboxes = generateSelectOptions($gearboxes_db);

		$car_category_db = Car_category::all();
		$car_category = generateSelectOptions($car_category_db);

		$users_db = $company->users()->get();
		$users = generateSelectOptions($users_db, 'name,surname');

		$owners_db = Owners::whereIn('owners_group_id', [4,6])->where('id', '!=', $company->owner_id)->get();
		$owners = generateSelectOptions($owners_db);

		return View::make('vmanage.companies.vehicles.create', compact('owners', 'engines', 'gearboxes', 'company', 'car_category', 'users'));
	}

	public function postStore()
	{
		$data = Input::all();

        if( ! in_array($data['vmanage_company_id'], $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

		$rules = array(
			'vin'  => 'unique:vmanage_vehicles,vin,NULL,id,outdated,0,deleted_at,NULL'
		);

		$validation = Validator::make($data, $rules);

		if ($validation->fails())
		{
			Flash::error('Istnieje już w systemie pojazd o podanym numerze VIN.');
			return Redirect::back()->withInput();
		}

        if($data['if_leasing'] == 1) {
            $company = VmanageCompany::find($data['vmanage_company_id']);
            $data['owner_id'] = $company->owner_id;
        }else{

        }

		if( ! Input::has('client_id') )
		{
			$matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
			if(Input::has('registry_post') && strlen(Input::has('registry_post')) == 6)
			{
				$registry_post = $data['registry_post'];
				$voivodeship_id = $matcher->match($registry_post);
				$data['registry_voivodeship_id'] = $voivodeship_id;
			}
			if(Input::has('correspond_post') && strlen(Input::has('correspond_post')) == 6)
			{
				$correspond_post = $data['correspond_post'];
				$voivodeship_id = $matcher->match($correspond_post);
				$data['correspond_voivodeship_id'] = $voivodeship_id;
			}

			$client = Clients::create($data);
			$data['client_id'] = $client->id;
		}

        VmanageVehicle::create($data);
        Flash::success('Pojazd został utworzony.');

		return Redirect::action('VmanageVehiclesController@getIndex', [Input::get('vmanage_company_id')]);
	}

	public function getShow($vehicle_id)
	{
        $vehicle = VmanageVehicle::where('outdated', 0)->with('company', 'brand', 'model', 'generation', 'car_engine', 'car_category', 'car_gearbox', 'user', 'users')->findOrFail($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        return View::make('vmanage.companies.vehicles.show', compact('vehicle'));
	}

    public function getDelete($vehicle_id)
    {
		$vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

		return View::make('vmanage.companies.vehicles.delete', compact('vehicle'));
    }

	public function postDestroy($vehicle_id){
		$vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

		$vehicle->delete();

		$result['code'] = 0;
		Flash::success('Pojazd '.$vehicle->registration.' została usunięty.');
		return json_encode($result);
	}

	public function postBrands()
	{
		$term = Input::get('term');
		$type = Input::get('type');

		$brands = Brands::where('typ', '=', $type)->where('name', 'like', '%'.$term.'%')->get();
		$result = array();

		foreach($brands as $k => $v){
			$result[] = array(
				"id"=>$v->id,
				"label"=>$v->name,
				"value" => $v->name
			);
		}

		return json_encode($result);
	}

	public function postModels()
	{
		$term = Input::get('term');
		$brand = Input::get('brand');

		$models = Brands_model::where('brand_id', '=', $brand)->where('name', 'like', '%'.$term.'%')->get();
		$result = array();

		foreach($models as $k => $v){
			$result[] = array(
				"id"=>$v->id,
				"label"=>$v->name,
				"value" => $v->name
			);
		}

		return json_encode($result);
	}

	public function postGenerations()
	{
		$model = Input::get('model');
		$model = Brands_model::find($model);

		$generations = Brands_models_generation::where('id_model_om', '=', $model->key_otomoto)->get();
		$result = '<option value="0" selected>--- wybierz ---</option>';

		foreach($generations as $k => $v){
			$result .= '<option value="'.$v->id.'">'.$v->name.'</option>';
		}

		return $result;
	}

	public function postCategories()
	{
		if( Input::get('type') == 1 )
			$categories = Car_category::all();
		else
			$categories = Car_truck_category::all();

		$result = '<option value="0" selected>--- wybierz ---</option>';

		foreach($categories as $k => $v){
			$result .= '<option value="'.$v->id.'">'.$v->name.'</option>';
		}

		return $result;
	}

    public function postSearch()
    {
        $last =  URL::previous();
        $url = strtok($last, '?');

        $gets = '?';

        if(Input::has('search_term')){

            if(Input::has('brand'))
                $gets .= 'brand=1&';

            if(Input::has('model'))
                $gets .= 'model=1&';

            if(Input::has('registration'))
                $gets .= 'registration=1&';

            if(Input::has('vin'))
                $gets .= 'vin=1&';

			if(Input::has('vin'))
				$gets .= 'vin=1&';

            if(Input::has('nr_contract'))
                $gets .= 'nr_contract=1&';

            $gets.='term='.Input::get('search_term').'&';
        }


        return $url.$gets;
    }

    private function passingWheres(&$query, $parameters = null)
    {
        if(is_null($parameters))
            $parameters = Input::all();

        if ( isset($parameters['term']) ) {
            $query->where(function ($query2) use($parameters){
                if (isset($parameters['brand'])) {
                    $brands = Brands::where('name', 'like', '%'.$parameters['term'].'%')->lists('id');

                    if(count($brands) > 0) {
                        $query2->orWhereHas('brand', function ($query3) use ($brands) {
                            $query3->whereIn('id', $brands);
                        });
                    }
                }
                if (isset($parameters['model'])) {
                    $models = Brands_model::where('name', 'like', '%'.$parameters['term'].'%')->lists('id');

                    if(count($models) > 0) {
                        $query2->orWhereHas('model', function ($query3) use ($models) {
                            $query3->whereIn('id', $models);
                        });
                    }
                }
                if (isset($parameters['registration'])) {
                    $query2->where('registration', 'like', '%'.$parameters['term'].'%');
                }

                if (isset($parameters['vin'])) {
                    $query2->where('vin', 'like', '%'.$parameters['term'].'%');
                }

				if (isset($parameters['nr_contract'])){
					$query2->where('nr_contract', 'like', '%'.$parameters['term'].'%');
				}

                if (isset($parameters['vmanage_user'])) {
                    $users = VmanageUser::where('name', 'like', '%'.$parameters['term'].'%')
                                        ->orWhere('name', 'like', '%'.$parameters['term'].'%')->lists('id');

                    $query2->orWhereHas('user', function($query3) use($users){
                        $query3->whereIn('id', $users);
                    });
                }
            });
        }

        return $query;
    }
}
