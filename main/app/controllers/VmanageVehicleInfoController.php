<?php

class VmanageVehicleInfoController extends \BaseController {

    private $managed_fleet;

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_pojazdu', ['only' => ['postInjuryVehicle', 'getEditInjuryVehicle']]);

        $this->managed_fleet = Auth::user()->vmanage_companies()->get()->lists('id', 'name');
    }

    public function getTechnicalInfo($vehicle_id)
    {
        $vehicle = VmanageVehicle::with('brand', 'model', 'generation')->find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $engines_db = Car_engines::all();
        $engines = generateSelectOptions($engines_db);

        $gearboxes_db = Car_gearboxes::all();
        $gearboxes = generateSelectOptions($gearboxes_db);

        $car_category_db = Car_category::all();
        $car_category = generateSelectOptions($car_category_db);

        if($vehicle->{'model'})
            $generations = Brands_models_generation::where('id_model_om', '=', $vehicle->{'model'}->key_otomoto)->lists('name', 'id');
        else
            $generations = [];

        return View::make('vmanage.companies.vehicles.info.dialogs.edit-technical-info', compact('vehicle', 'engines', 'gearboxes', 'car_category', 'generations'));
    }

    public function postTechnicalInfo($vehicle_id)
    {
        $data = Input::all();
        $vehicle = VmanageVehicle::find($vehicle_id);
        $vehicle->update($data);

        Flash::success('Dane pojazdu zostały zaktualizowane.');
        return json_encode(['code' => 0]);
    }

    public function getRegistrationInfo($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);
        return View::make('vmanage.companies.vehicles.info.dialogs.edit-registration-info', compact('vehicle'));
    }

    public function postRegistrationInfo($vehicle_id)
    {
        $data = Input::all();
        $rules = array(
            'vin'  => 'unique:vmanage_vehicles,vin,'.$vehicle_id.',id,outdated,0,deleted_at,NULL'
        );

        $validation = Validator::make($data, $rules);

        if ($validation->fails())
        {
            return json_encode(['code' => 2, 'error' => 'istnieje już pojazd o podanym numerze VIN']);
        }

        if(!isset($data['cfm'])) $data['cfm'] = 0;

        if(!isset($data['if_vip'])) $data['if_vip'] = 0;

        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $vehicle_new = VmanageVehicle::create($vehicle->toArray());
        $vehicle_new->update($data);

        $vehicle->outdated = 1;
        $vehicle->save();

        $all_id_update = array();
        $injuries_temp = array();

        $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $vehicle->id)->orWhere('previous_vmanage_vehicle_id', $vehicle->id)->first();

        if($existing_history)
        {
            VmanageVehicleHistory::create([
                'history_id' => $existing_history->history_id,
                'vmanage_vehicle_id'    =>  $vehicle_new->id,
                'previous_vmanage_vehicle_id'   => $vehicle->id
              ]);

            $all_id_update[] = $existing_history->vmanage_vehicle_id;
            $info = [];
           if((isset($data['update_all'])||isset($data['update_all_vip']))&&$existing_history){
              do{
                  $all_id_update[] = $existing_history->previous_vmanage_vehicle_id;

                  $vehicle = VmanageVehicle::find($existing_history->previous_vmanage_vehicle_id);
                  if(isset($data['update_all'])){
                    $data_new = $data;
                    unset($data_new['if_vip']);
                    unset($data_new['if_return']);
                    unset($data_new['cfm']);
                    $vehicle->fill($data_new);
                  }
                  elseif(isset($data['update_all_vip'])){
                    $vehicle->fill(['if_vip'=>$vehicle_new->if_vip,'cfm'=>$vehicle_new->cfm,'if_return'=>$vehicle_new->if_return]);
                  }

                  $changes = $vehicle->getDirty();

                  $info = [];
                  foreach($changes as $changed_field => $changed_value)
                  {
                      switch ($changed_field){
                          case 'cfm':
                              $info[] = 'cfm: '.($vehicle->getOriginal('cfm') == 0 ? 'nie' : 'tak').' -> '.($changed_value == 0 ? 'nie' : 'tak');
                              break;
                          case 'registration':
                              $info[] = 'rejestracja: '.$vehicle->getOriginal('registration').' -> '.$changed_value;
                              break;
                          case 'vin':
                              $info[] = 'VIN: '.$vehicle->getOriginal('VIN').' -> '.$changed_value;
                              break;
                          case 'nr_contract':
                              $info[] = 'numer umowy: '.$vehicle->getOriginal('nr_contract').' -> '.$changed_value;
                              break;
                          case 'first_registration':
                              $info[] = 'data pierwszej rejestracji: '.$vehicle->getOriginal('first_registration').' -> '.$changed_value;
                              break;
                          case 'if_vip':
                              $info[] = 'pojazd VIP: '.($vehicle->getOriginal('if_vip') == 0 ? 'nie' : 'tak').' -> '.($changed_value == 0 ? 'nie' : 'tak');
                              break;
                          default: break;
                      }
                  }

                  $vehicle->save();
                  $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $existing_history->previous_vmanage_vehicle_id)->first();

              } while($existing_history);
           }

           if(isset($data['update_all_vip'])){
                Injury::where('vehicle_type','VmanageVehicle')->whereIn('vehicle_id',$all_id_update)->update(['if_vip'=>$vehicle_new->if_vip]);
            }

            $injuries = Injury::where('vehicle_type','VmanageVehicle')->whereIn('vehicle_id',$all_id_update)->get();
           foreach($injuries as $injury){
               Histories::history($injury->id, 153, Auth::user()->id, '-1', implode('; ', $info) );
           }
        }else{
            $highest_history = VmanageVehicleHistory::orderBy('history_id', 'desc')->first();
            if($highest_history)
            {
                $history_id = $highest_history->history_id + 1;
            }else{
                $history_id = 1;
            }

            VmanageVehicleHistory::create([
                'history_id' => $history_id,
                'vmanage_vehicle_id'    =>  $vehicle_new->id,
                'previous_vmanage_vehicle_id'   => $vehicle->id
            ]);
        }

        //Flash::success(json_encode($all_id_update));
        Flash::success('Dane pojazdu zostały zaktualizowane.');
        return json_encode(['code' => 1, 'url' => URL::action('VmanageVehiclesController@getShow',[$vehicle_new->id])]);
    }

    public function getCurrentInfo($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);
        return View::make('vmanage.companies.vehicles.info.dialogs.edit-current-info', compact('vehicle'));
    }

    public function postCurrentInfo($vehicle_id)
    {
        $data = Input::all();
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        if(isset($data['technical_exam_date']) && $data['technical_exam_date'] == '') $data['technical_exam_date'] = null;
        if(isset($data['servicing_date']) && $data['servicing_date'] == '') $data['servicing_date'] = null;
        if(isset($data['insurance_expire_date']) && $data['insurance_expire_date'] == '') $data['insurance_expire_date'] = null;

        $vehicle->update($data);

        Flash::success('Dane pojazdu zostały zaktualizowane.');
        return json_encode(['code' => 0]);
    }

    public function getOwnerInfo($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        return View::make('vmanage.companies.vehicles.info.dialogs.edit-owner-info', compact('vehicle'));
    }

    public function postOwnerInfo($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $data = Input::all();

        if($data['if_leasing'] == 1) {
            $vehicle->owner_id = $vehicle->company->owner_id;
            $vehicle->save();
        }else {
            $find_owner = Owners::whereName(Input::get('name'))->get();
            if (!$find_owner->isEmpty()) {
                $result['code'] = 2;
                $result['error'] = "Istnieje już firma o podanej nazwie.";
                return json_encode($result);
            }

            $data['short_name'] = shortenName($data['name']);
            $data['owners_group_id'] = 4;
            $owner = Owners::create($data);

            $vehicle->owner_id = $owner->id;
            $vehicle->save();
        }

        Flash::success('Dane pojazdu zostały zaktualizowane.');
        return json_encode(['code' => 0]);

    }

    public function getAssignUser($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        return View::make('vmanage.companies.vehicles.info.dialogs.assign-user', compact('vehicle'));
    }

    public function postSearchUser()
    {
        $term = Input::get('term');
        $vmanage_user_id=0;
        if(Input::has('vmanage_user_id'))
            $vmanage_user_id = Input::get('vmanage_user_id');

        $vmanage_company_id = Input::get('vmanage_company_id');
        $users = VmanageUser::
                where('id', '!=', $vmanage_user_id)
                ->where('vmanage_company_id', $vmanage_company_id)
                ->where(function($query) use($term) {
                    $query->where('name', 'like', '%'.$term.'%');
                    $query->orWhere('surname', 'like', '%'.$term.'%');
                })->get();
        $result = array();

        foreach($users as $k => $v){
            $result[] = array(
                "id"=>$v->id,
                "label"=> $v->name.' '.$v->surname,
                "value" => $v->name.' '.$v->surname
            );
        }

        return json_encode($result);
    }

    public function postAssignUser($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $vehicle->users()->attach($vehicle->vmanage_user_id, array('created_at' => date('Y-m-d H:i:s')));
        if(Input::has('vmanage_user_id'))
        {
            $vehicle->vmanage_user_id = Input::get('vmanage_user_id');
            $vehicle->save();
        }else{
            $data = Input::all();
            $rules = array(
                'name'  => 'required',
                'surname' => 'required'
            );
            $validation = Validator::make($data, $rules);

            if ($validation->fails())
            {
                return json_encode(['code' => 2, 'error' => 'imię i nazwisko użytkownika są wymagane']);
            }

            $data['vmanage_company_id'] = $vehicle->vmanage_company_id;
            $user = VmanageUser::create($data);

            $vehicle->vmanage_user_id = $user->id;
            $vehicle->save();
        }

        Flash::success('Użytkownik pojazdu został zmieniony');
        return json_encode(['code' => 0]);
    }

    public function getEditClient($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        return View::make('vmanage.companies.vehicles.info.dialogs.edit-client-info', compact('vehicle'));
    }

    public function getChangeClient($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        return View::make('vmanage.companies.vehicles.info.dialogs.change-client', compact('vehicle'));
    }

    public function postUpdateClient($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $client = $vehicle->client()->first();
        while( ! $client->child->isEmpty())
        {
            $client = $client->child->first();
        }
        $inputs = Input::all();
        $inputs['parent_id'] = $client->id;

        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
        if(Input::has('registry_post') && strlen(Input::has('registry_post')) == 6)
        {
            $registry_post = $inputs['registry_post'];
            $voivodeship_id = $matcher->match($registry_post);
            $inputs['registry_voivodeship_id'] = $voivodeship_id;
        }
        if(Input::has('correspond_post') && strlen(Input::has('correspond_post')) == 6)
        {
            $correspond_post = $inputs['correspond_post'];
            $voivodeship_id = $matcher->match($correspond_post);
            $inputs['correspond_voivodeship_id'] = $voivodeship_id;
        }
        $old_client = $client;

        $client = Clients::create($inputs);
        $old_client->update(['active' => 1]);
        $vehicle->client_id = $client->id;
        $vehicle->save();

        Flash::success('Dane klienta zostały zaktualizowane');
        return json_encode(['code' => 0]);
    }

    public function postSearchClient()
    {
        $term = Input::get('term');

        $clients = Clients::distinct()
                            ->where(function($query){
                                if(Input::has('client_id')) {
                                    $client_id = Input::get('client_id');
                                    $query->where('id', '!=', $client_id);
                                }
                            })
                            ->where('name', 'like', '%'.$term.'%')
                            ->groupBy('name')
                            ->get();
        $result = array();

        foreach($clients as $k => $v){
            $result[] = array(
                "id"=>$v->id,
                "label"=> $v->name,
                "value" => $v->name
            );
        }

        return json_encode($result);
    }

    public function postChangeClient($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        if(Input::has('client_id'))
        {
            $client_id = Input::get('client_id');
            if($client_id == '')
                return json_encode(['msg' => 'Proszę poprawnie wybrać nowego klienta.']);

            $vehicle->client_id = Input::get('client_id');
            $vehicle->save();

            Flash::success('Dane klienta zostały zaktualizowane');
            return json_encode(['code' => 0]);
        }

        $inputs = Input::all();
        $inputs['NIP'] = stripNonNumeric($inputs['NIP']);

        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
        if(Input::has('registry_post') && strlen(Input::has('registry_post')) == 6)
        {
            $registry_post = $inputs['registry_post'];
            $voivodeship_id = $matcher->match($registry_post);
            $inputs['registry_voivodeship_id'] = $voivodeship_id;
        }
        if(Input::has('correspond_post') && strlen(Input::has('correspond_post')) == 6)
        {
            $correspond_post = $inputs['correspond_post'];
            $voivodeship_id = $matcher->match($correspond_post);
            $inputs['correspond_voivodeship_id'] = $voivodeship_id;
        }

        $client = Clients::create($inputs);
        $vehicle->client_id = $client->id;
        $vehicle->save();

        Flash::success('Dane klienta zostały zaktualizowane');
        return json_encode(['code' => 0]);
    }

    public function getChangeOwner($vehicle_id){
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $vmanage_companies = VmanageCompany::where('id', '!=', $vehicle->vmanage_company_id)->has('owner')->with('owner')->get();
        $steps = InjurySteps::lists('name', 'id');

        return View::make('vmanage.companies.vehicles.info.dialogs.change-owner', compact('vehicle', 'vmanage_companies', 'steps'));
    }

    public function postChangeOwner($vehicle_id){
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $vmanage_company = VmanageCompanies::find(Input::get('vmanage_company_id'));

        $new_vehicle = VmanageVehicle::create($vehicle->toArray());
        $new_vehicle->vmanage_company_id = $vmanage_company->id;
        $new_vehicle->owner_id = $vmanage_company->owner_id;
        $new_vehicle->save();

        $vehicle->outdated = 1;
        $vehicle->save();

        $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $vehicle->id)->orWhere('previous_vmanage_vehicle_id', $vehicle->id)->first();

        if($existing_history)
        {
            VmanageVehicleHistory::create([
                'history_id' => $existing_history->history_id,
                'vmanage_vehicle_id'    =>  $new_vehicle->id,
                'previous_vmanage_vehicle_id'   => $vehicle->id
            ]);
        }else{
            $highest_history = VmanageVehicleHistory::orderBy('history_id', 'desc')->first();
            if($highest_history)
            {
                $history_id = $highest_history->history_id + 1;
            }else{
                $history_id = 1;
            }

            VmanageVehicleHistory::create([
                'history_id' => $history_id,
                'vmanage_vehicle_id'    =>  $new_vehicle->id,
                'previous_vmanage_vehicle_id'   => $vehicle->id
            ]);
        }

        if(Input::has('steps'))
        {
            $injuries = Injury::where('vehicle_type', 'VmanageVehicle')->where('vehicle_id', $vehicle->id)->whereIn('step', Input::get('steps'))->lists('id');

            Injury::whereIn('id', $injuries)->update(array('vehicle_id' => $new_vehicle->id));
        }

        Flash::success('Właściciel pojazdu został zmieniony.');
        return json_encode(['code' => 1, 'url' => URL::action('VmanageVehiclesController@getShow',[$new_vehicle->id])]);
    }

    public function getEditSeller($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        return View::make('vmanage.companies.vehicles.info.dialogs.edit-seller-info', compact('vehicle'));
    }

    public function postUpdateSeller($vehicle_id)
    {
        $vehicle = VmanageVehicle::find($vehicle_id);

        if( ! in_array($vehicle->vmanage_company_id, $this->managed_fleet))
            throw new \Idea\Exceptions\PermissionException("Brak wymaganych uprawnień do przeglądania tej strony.");

        $vehicle->seller->update(Input::all());

        Flash::success('Dane dostawcy zostały zaktualizowane');
        return json_encode(['code' => 0]);
    }

    public function getChangeSeller($vehicle_id)
    {

    }

    public function getEditInjuryVehicle($injury_id)
    {
        $injury = Injury::find($injury_id);
        $vehicle = $injury->vehicle;


        return View::make('injuries.dialog.edit-vehicle-vmanage', compact('vehicle', 'injury_id'));
    }

    public function postInjuryVehicle($vehicle_id, $injury_id)
    {
        $data = Input::all();
        if(! Input::has('cfm'))
        {
            $data['cfm'] = 0;
        }
        $vehicle = VmanageVehicle::find($vehicle_id);

        $vehicle_new = VmanageVehicle::create($vehicle->toArray());
        $vehicle_new->fill($data);

        $changes = $vehicle_new->getDirty();

        $info = [];
        foreach($changes as $changed_field => $changed_value)
        {
            switch ($changed_field){
                case 'cfm':
                    $info[] = 'cfm: '.($vehicle_new->getOriginal('cfm') == 0 ? 'nie' : 'tak').' -> '.($changed_value == 0 ? 'nie' : 'tak');
                    break;
                case 'registration':
                    $info[] = 'rejestracja: '.$vehicle_new->getOriginal('registration').' -> '.$changed_value;
                    break;
                case 'vin':
                    $info[] = 'vin: '.$vehicle_new->getOriginal('VIN').' -> '.$changed_value;
                    break;
                case 'brand':
                    $info[] = 'marka: '.$vehicle_new->getOriginal('brand').' -> '.$changed_value;
                    break;
                case 'model':
                    $info[] = 'model: '.$vehicle_new->getOriginal('model').' -> '.$changed_value;
                    break;
                case 'year_production':
                    $info[] = 'rok produkcji: '.$vehicle_new->getOriginal('year_production').' -> '.$changed_value;
                    break;
                case 'engine':
                    $info[] = 'silnik: '.$vehicle_new->getOriginal('engine').' -> '.$changed_value;
                    break;
                case 'mileage':
                    $info[] = 'przebieg: '.$vehicle_new->getOriginal('mileage').' -> '.$changed_value;
                    break;
                case 'first_registration':
                    $info[] = 'data pierwszej rejestracji: '.$vehicle_new->getOriginal('first_registration').' -> '.$changed_value;
                    break;
                case 'register_as':
                    $info[] = 'rejestrowany w as: '.($vehicle_new->getOriginal('register_as') == 0 ? 'nie' : 'tak').' -> '.($changed_value == 0 ? 'nie' : 'tak');
                    break;
                case 'type':
                    $info[] = 'typ pojazdu: '.($vehicle_new->getOriginal('type') == 1 ? 'osobowy' : 'ciężarowy').' -> '.($changed_value == 1 ? 'osobowy' : 'ciężarowy');
                    break;
                default: break;
            }
        }

        $vehicle_new->save();

        $vehicle->outdated = 1;
        $vehicle->save();

        $existing_history = VmanageVehicleHistory::where('vmanage_vehicle_id', $vehicle->id)->orWhere('previous_vmanage_vehicle_id', $vehicle->id)->first();

        if($existing_history)
        {
            VmanageVehicleHistory::create([
                'history_id' => $existing_history->history_id,
                'vmanage_vehicle_id'    =>  $vehicle_new->id,
                'previous_vmanage_vehicle_id'   => $vehicle->id
            ]);
        }else{
            $highest_history = VmanageVehicleHistory::orderBy('history_id', 'desc')->first();
            if($highest_history)
            {
                $history_id = $highest_history->history_id + 1;
            }else{
                $history_id = 1;
            }

            VmanageVehicleHistory::create([
                'history_id' => $history_id,
                'vmanage_vehicle_id'    =>  $vehicle_new->id,
                'previous_vmanage_vehicle_id'   => $vehicle->id
            ]);
        }
        $injury = Injury::find($injury_id);
        $injury->update(['vehicle_id' => $vehicle_new->id]);

        Histories::history($injury_id, 153, Auth::user()->id, '-1', implode('; ', $info) );

        Flash::success('Dane pojazdu zostały zaktualizowane.');
        return json_encode(['code' => 0]);
    }
}
