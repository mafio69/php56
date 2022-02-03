<?php

class LiquidationCardsController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:karty_likwidacji_szkod#wejscie');
    }

	/**
	 * Display a listing of the resource.
	 * GET /liquidationcards
	 *
	 * @return Response
	 */
	public function index()
	{
		$cards = LiquidationCards::where(function($query)
        {
            //czy ustawione jest filtrowanie wyszukiwaniem
            if(Input::has('term')){

                $query->where(function($query2){

                    if(Input::has('registration')){
                        $query2 -> orWhereHas('vehicle', function($q)
                        {
                            $q -> where('registration', 'like', Input::get('term').'%');
                        });
                    }

                    if(Input::has('card_nr')){
                        $query2 -> orWhere('number', 'like', Input::get('term').'%');
                    }

                    if(Input::has('expiration_date')){
                        $query2 -> orWhere('expiration_date', 'like', Input::get('term').'%');
                    }

                    if(Input::has('nr_contract')){
                        $query2 -> orWhereHas('vehicle', function($q)
                        {
                            $q -> where('nr_contract', 'like', Input::get('term').'%');
                        });
                    }

                });
            }

        })->orderBy('number', 'asc')->with('user', 'vehicle')->paginate(Session::get('search.pagin', '10'));

        return View::make('settings.liquidation_cards.index', compact('cards'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /liquidationcards/create
	 *
	 * @return Response
	 */
	public function create()
	{
        $last_card = LiquidationCards::orderBy('number', 'desc')->first();
        if($last_card)
            $number = $last_card->number + 1;
        else
            $number = 1000;
        return View::make('settings.liquidation_cards.create', compact('number'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /liquidationcards
	 *
	 * @return Response
	 */
	public function store()
	{
        $data = Input::all();
        $rules = array(
            'number'  => 'unique:liquidation_cards'
        );
        $validation = Validator::make($data, $rules);

        if ($validation->fails())
        {
            Flash::error('Istnieje już karta w systemie o podanym numerze.');
            return Redirect::back()->withInput();
        }

        $card = LiquidationCards::create(Input::all());

        return Redirect::route('settings.liquidation_cards', array('index'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /liquidationcards/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $card = LiquidationCards::find($id);

        return View::make('settings.liquidation_cards.edit', compact('card'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /liquidationcards/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $data = Input::all();
        $rules = array(
            'number'  => 'unique:liquidation_cards,number,'.$id
        );
        $validation = Validator::make($data, $rules);

        if ($validation->fails())
        {
            Flash::error('Błąd zapisu. Istnieje już karta w systemie o podanym numerze.');

            $result['code'] = 0;
            return json_encode($result);
        }


        $card = LiquidationCards::find($id);

        $card->number = Input::get('number');
        $card->release_date = Input::get('release_date');
        $card->expiration_date = Input::get('expiration_date');
        $card->save();

        $result['code'] = 0;
        return json_encode($result);
	}

    public function delete($id)
    {
        $card = LiquidationCards::find($id);

        return View::make('settings.liquidation_cards.delete', compact('card'));
    }
	/**
	 * Remove the specified resource from storage.
	 * DELETE /liquidationcards/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $card = LiquidationCards::find($id);

        $card->delete();

        $result['code'] = 0;
        return json_encode($result);
	}

    public function getIsdlList(){

        $registration = Input::get('registration');
        $nr_contract = Input::get('nr_contract');

        $existCard = DB::table('liquidation_cards')
            ->select('liquidation_cards.*')
            ->distinct()
            ->leftJoin('vehicles', function($join)
            {
                $join->on('vehicles.id',' =', 'liquidation_cards.vehicle_id');
            })->where(function($query) use ($registration, $nr_contract){
                $query->where('vehicles.registration', '=', $registration)->orWhere('vehicles.nr_contract','=', $nr_contract);
            })->whereNull('deleted_at')->first();

        if( $existCard )
        {
            $result[] = array(
                'status'    => 2,
                'des'       => 'Istnieje już karta likwidacji pojazdu dla podanych danych. Nr karty: <b>'.$existCard->number.'</b>'
            );
            return json_encode($result);
        }

        $username = substr( Auth::user()->login, 0, 10);

        $data = new Idea\Structures\GETVEHICLEDTAInput($nr_contract, $registration, $username);

        $owners = Owners::whereActive('0')->where('wsdl', '!=', '')->get();

        $result = array();

        foreach($owners as $owner) {
            if( !isset($owner_id) || (isset($owner_id) && $result[$owner_id]['status'] != 0) ) {
                $owner_id = $owner->id;

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('getvehicledta_XML');

                $xml = $webservice->getResponseXML();

                $errorCode = $xml->ANSWER->getVehicleDataReturn->Error->ErrorCde;

                if ($errorCode == 'ERR0000') {
                    $xml = $xml->ANSWER->getVehicleDataReturn->getVehicle;

                    $client = Clients::where('NIP', '=', trim($xml->customer->NIP->__toString()))->where('REGON', '=', trim($xml->customer->REGON->__toString()))
                        ->where('active', '=', '0')->orderBy('parent_id', 'desc')->get();
                    if (count($client) == 0) {
                        //brak właściciela w bazie
                        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
                        $registry_post = $xml->customer->address->postalCode->__toString();
                        if (strlen($registry_post) == 6) {
                            $registry_voivodeship_id = $matcher->match($registry_post);
                        } else {
                            $registry_voivodeship_id = null;
                        }

                        $correspond_post = $xml->customer->mailAddress->postalCode->__toString();
                        if (strlen($correspond_post) == 6) {
                            $correspond_voivodeship_id = $matcher->match($correspond_post);
                        } else {
                            $correspond_voivodeship_id = null;
                        }

                        $client = Clients::create(array(
                            'name' => $xml->customer->name->__toString(),
                            'firmID' => $xml->customer->firmID->__toString(),
                            'NIP' => trim(str_replace('-', '', $xml->customer->NIP->__toString())),
                            'REGON' => trim($xml->customer->REGON->__toString()),
                            'registry_post' => $xml->customer->address->postalCode->__toString(),
                            'registry_city' => $xml->customer->address->city->__toString(),
                            'registry_street' => $xml->customer->address->street->__toString(),
                            'registry_voivodeship_id' => $registry_voivodeship_id,
                            'correspond_post' => $xml->customer->mailAddress->postalCode->__toString(),
                            'correspond_city' => $xml->customer->mailAddress->city->__toString(),
                            'correspond_street' => $xml->customer->mailAddress->street->__toString(),
                            'correspond_voivodeship_id' => $correspond_voivodeship_id,
                            'phone' => $xml->customer->phone->__toString(),
                            'email' => $xml->customer->email->__toString()
                        ));
                        $client_id = $client->id;

                    } else {
                        $client_id = $client->first()->id;
                    }

                    $insurance_company_name = trim($xml->policy->insCompany->__toString());


                    if ($xml->policy->assistance->__toString() == 'N') $assistance = 0;
                    else if ($xml->policy->assistance->__toString() == 'T') $assistance = 1;

                    $vehicle = Vehicles::where('registration', '=', $xml->vehicleData->regNumber->__toString())->orderBy('parent_id', 'desc')->get();
                    $exist_in_base = 0;
                    $pos = strpos($xml->vehicleData->description->__toString(), ' ');
                    $brand = trim(substr($xml->vehicleData->description->__toString(), 0, $pos));
                    $model = trim(substr($xml->vehicleData->description->__toString(), $pos));

                    $lessor = $xml->contract->lessor->__toString();
                    if($lessor == 'SKA')
                    {
                        $owner_id = 2;
                    }else{
                        $owner_id = 1;
                    }

                    if (count($vehicle) == 0) {
                        //samochód nie istnieje w bazie

                        $vehicle = Vehicles::create(array(
                            'owner_id' => $owner_id,
                            'client_id' => $client_id,
                            'registration' => $xml->vehicleData->regNumber->__toString(),
                            'VIN' => $xml->vehicleData->VIN->__toString(),
                            'brand' => $brand,
                            'model' => $model,
                            'year_production' => $xml->vehicleData->year->__toString(),
                            'insurance_company_name' => $insurance_company_name,
                            'expire' => $xml->policy->expDate->__toString(),
                            'nr_policy' => $xml->policy->policyNumber->__toString(),
                            'assistance' => $assistance,
                            'assistance_name' => $xml->policy->version->__toString(),
                            'nr_contract' => $xml->contract->number->__toString(),
                            'end_leasing' => $xml->contract->endDate->__toString(),
                            'contract_status' => $xml->contract->status->__toString()
                        ));

                        $id_vehicle = $vehicle->id;
                        $vehicle = Vehicles::find($id_vehicle);

                    } else {
                        $vehicle = $vehicle->first();
                    }

                    $result[$owner_id] = array(
                        'status' => 0,
                        'id' => $vehicle->id,
                        'owner_id' => $vehicle->owner_id,
                        'owner_show' => (Owners::find($vehicle->owner_id)->old_name) ? Owners::find($vehicle->owner_id)->name.' ('.Owners::find($vehicle->owner_id)->old_name.')' : Owners::find($vehicle->owner_id)->name,
                        'client_id' => $vehicle->client_id,
                        'client_show' => Clients::find($vehicle->client_id)->name,
                        'registration_show' => $xml->vehicleData->regNumber->__toString(),
                        'vin_show' => $xml->vehicleData->VIN->__toString(),
                        'brand_show' => $brand,
                        'model_show' => $model,
                        'year_production_show' => $xml->vehicleData->year->__toString(),
                        'expire_show' => $xml->policy->expDate->__toString(),
                        'nr_contract_show' => $xml->contract->number->__toString(),
                        'end_leasing_show' => $xml->contract->endDate->__toString(),
                        'contract_status_show' => $xml->contract->status->__toString()
                    );

                } else if ($errorCode == 'ERR0003') {
                    //brak umowy o zadanych parametrach
                    $result[$owner_id] = array(
                        'status' => 1,
                        'des' => $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString()
                    );
                } else {
                    //pojawił się błąd
                    $result[$owner_id] = array(
                        'status' => 2,
                        'des' => $xml->ANSWER->getVehicleDataReturn->Error->ErrorDes->__toString()
                    );
                }
            }
        }
        return json_encode($result);

    }

    public function setSearch()
    {
        $last =  URL::previous();
        $url = strtok($last, '?');

        $gets = '';

        if(Input::has('search_term')){
            $gets = '?';

            if(Input::has('card_nr'))
                $gets .= 'card_nr=1&';

            if(Input::has('registration'))
                $gets .= 'registration=1&';

            if(Input::has('nr_contract'))
                $gets .= 'nr_contract=1&';

            if(Input::has('expiration_date'))
                $gets .= 'expiration_date=1&';

            $gets.='term='.Input::get('search_term');
        }

        echo $url.$gets;
    }

    public function report()
    {
        return View::make('settings.liquidation_cards.report');
    }

    public function generate_report()
    {
        $cards = LiquidationCards::
            where(function($query)
            {
                if(Input::has('number_from'))
                    $query->where('number', '>=', Input::get('number_from'));
                if(Input::has('number_to'))
                    $query->where('number', '<=', Input::get('number_to'));


                if(Input::has('releaseDate_from'))
                    $query->where('release_date', '>=', Input::get('releaseDate_from'));
                if(Input::has('releaseDate_to'))
                    $query->where('release_date', '<=', Input::get('releaseDate_to'));


                if(Input::has('expirationDate_from') )
                    $query->where('expiration_date', '>=', Input::get('expirationDate_from') );
                if(Input::has('expirationDate_to') )
                    $query->where('expiration_date', '<=', Input::get('expirationDate_to') );

            })
            ->get();
        dd($cards->toArray());
    }

}
