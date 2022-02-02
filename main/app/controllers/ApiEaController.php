<?php

class ApiEaController extends \BaseController {



    /**
     * @api {post} ea/vehicle Wyszukanie pojazdu w bazie.
     * @apiName Wyszukaj pojazd
     * @apiGroup Metody
     *
     * @apiHeader {String} Authorization Bearer: Token sesji
     *
     * @apiParam {String} registration Numer rejestracyjny pojazdy
     * @apiParam {String} vin Vin pojazdu
     * @apiParam {String} contract_number Numer umowy pojazdu
     * @apiParam {String} api_key Klucz api do modułu
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "vehicles":[
     *              {
     *                  "vehicle_id":220,
     *                  "vehicle_type":"1",
     *                  "registration":"WB7843H",
     *                  "brand":"Audi",
     *                  "model":"A6",
     *                  "vin":"WAUZZZ4G5EN041271",
     *                  "year_production":2013,
     *                  "owner":"VW Leasing",
     *                  "sales_program":IDX,
     *                  "contract_number":201/2019,
     *                  "end_leasing":"2019-12-13",
     *                  "insurance_company":"PZU S.A.",
     *                  "insurance_expire_date":"2015-11-08",
     *                  "policy_number":"EN04123SA12/12"
     *              }
     *          ],
     *          "token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL3ZlaGljbGUiLCJpYXQiOiIxNTcxNDAzOTA5IiwiZXhwIjoiMTU3MTQwNzkwNyIsIm5iZiI6IjE1NzE0MDQzMDciLCJqdGkiOiIyYjkwMjYwNWMyZWEwMjM4ZTU0YjY2NDYzOWUyMGNmNyJ9.MmZjNzZmN2E5NDkxNjk5YTQ4ZTBmMTY5ZmI0NzZkNTRiOGViZjQ1ZmM4ODkwZGZjYzMzYzFkNTQxNjJmYjRkYg"
     *      }
     *
     * @apiErrorExample Brakujący token
     *     HTTP 400
     *     {
     *       "error": "token_not_provided"
     *     }
     *
     * @apiErrorExample Wygasły token
     *     HTTP 401
     *     {
     *       "error": "token_invalid"
     *     }
     *
     * @apiErrorExample Brak przesłanego api key
     *     HTTP 400
     *     {
     *       "error": "api_key_required"
     *     }
     *
     * @apiErrorExample Błędny api key
     *     HTTP 400
     *     {
     *       "error": "api_key_invalid"
     *     }
     *
     * @apiErrorExample Brak przesłanych parametrów
     *     HTTP 400
     *     {
     *        "error": "data_missed"
     *     }
     */

    public function vehicle()
    {
        if(
            trim(Input::get('registration', '')) == '' &&
            trim(Input::get('contract_number', '')) == ''&&
            trim(Input::get('vin', '')) == ''
        ){
            return Response::json(['error' => 'data_missed'], 400);
        }


        $searcher = new \Idea\Searcher\Searcher(
                        trim(Input::get('registration', '')),
                        trim(Input::get('contract_number', '')),
                        trim(Input::get('vin', ''))
                    );

        $vehicles = $searcher->searchApiVehicles();

        $response = compact('vehicles');
        $this->logRequest($response);

        $token = JWTAuth::refresh(JWTAuth::getToken());


        return Response::json(compact('vehicles', 'token'));
    }

    /**
     * @api {post} ea/car-workshops Wyszukanie dopasowanych warsztatów.
     * @apiName Wyszukaj warsztat
     * @apiGroup Metody
     *
     * @apiHeader {String} Authorization Bearer: Token sesji
     *
     * @apiParam {String} sales_program Kod programu sprzedażowego
     * @apiParam {String} [city] Miejscowość w której znajduje się serwis
     * @apiParam {Decimal} [lat] Współrzędna latitude położenia serwisu
     * @apiParam {Decimal} [lng] Współrzędna longitude położenia serwisu
     * @apiParam {Integer} [radius] Obszar [km] w promieniu którego znajduje się serwis
     * @apiParam {String} api_key Klucz api do modułu
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *       "workshops": [
     *           {
     *               "id": 4137,
     *               "name": " \"Auto-Błysk\" Andrzej Baranowski ",
     *               "nip": "",
     *               "street": "Młynarska 10",
     *               "code": "84-351",
     *               "city": "Nowa Wieś Lęborska",
     *               "email": "",
     *               "phone": "",
     *               "contact_people": "",
     *               "lat": 54.562988,
     *               "lng": 17.735567,
     *               "open_time": "08:00:00",
     *               "close_time": "18:00:00",
     *               "available_range": [
     *                   "blacharsko-lakierniczy (ciężarowe)",
     *                   "blacharsko-lakierniczy (dostawcze do 3,5 t)",
     *                   "blacharsko-lakierniczy (osobowe)",
     *                   "diagnostyka okręgowa (ciężarowe)",
     *                   "diagnostyka okręgowa (osobowe)",
     *                   "diagnostyka podstawowa (ciężarowe)",
     *                   "diagnostyka podstawowa (osobowe)",
     *                   "mechaniczny (ciężarowe)",
     *                   "mechaniczny (osobowe)",
     *                   "szyby",
     *                   "wulkanizacyjny (ciężarowe)",
     *                   "wulkanizacyjny (osobowe)"
     *               ],
     *               "available_brands": [
     *                   "Audi",
     *                   "BMW",
     *                   "Wielton",
     *                   "Still"
     *               ],
     *               "plan_groups": [
     *                   {
     *                       "name": "PN1",
     *                       "conditional_list": false
     *                   },
     *                   {
     *                       "name": "PN2",
     *                       "conditional_list": true
     *                   }
     *               ],
     *               "address": "84-351 Nowa Wieś Lęborska, Młynarska 10",
     *               "company": {
     *                   "name": " \"Auto-Błysk\" Andrzej Baranowski ",
     *                   "street": "Młynarska 10 ",
     *                   "code": "84-351",
     *                   "city": "Nowa Wieś Lęborska",
     *                   "nip": "",
     *                   "krs": "",
     *                   "regon": "",
     *                   "www": "",
     *                   "email": "",
     *                   "phone": "",
     *                   "account_nr": ""
     *               }
     *           },
     *           {
     *               "id": 4312,
     *               "name": "MAGMAR Sp z o.o. ",
     *               "nip": null,
     *               "street": "KOBYLOGÓRSKA 98",
     *               "code": "66-400",
     *               "city": "GORZÓW WIELKOPOLSKI ",
     *               "email": "",
     *               "phone": "",
     *               "contact_people": null,
     *               "lat": 0,
     *               "lng": 0,
     *               "open_time": null,
     *               "close_time": null,
     *               "available_range": [],
     *               "available_brands": [],
     *               "plan_groups": [
     *                   {
     *                       "name": "PN1",
     *                       "conditional_list": false
     *                   },
     *                   {
     *                       "name": "PN2",
     *                       "conditional_list": true
     *                   }
     *               ],
     *               "address": "66-400 GORZÓW WIELKOPOLSKI , KOBYLOGÓRSKA 98",
     *               "company": {
     *                   "name": "MAGMAR Sp z o.o. ",
     *                   "street": "KOBYLOGÓRSKA 98",
     *                   "code": "66-400",
     *                   "city": "GORZÓW WIELKOPOLSKI ",
     *                   "nip": "5993163878",
     *                   "krs": "",
     *                   "regon": "",
     *                   "www": "",
     *                   "email": "",
     *                   "phone": "",
     *                   "account_nr": ""
     *               }
     *           },
     *           {
     *               "id": 4992,
     *               "name": " \"Auto-Błysk\" Andrzej Baranowski ",
     *               "nip": "",
     *               "street": "Młynarska 10 ",
     *               "code": "84-351",
     *               "city": "Nowa Wieś Lęborska",
     *               "email": "",
     *               "phone": "",
     *               "contact_people": "",
     *               "lat": 54.562988,
     *               "lng": 17.735567,
     *               "open_time": "00:00:00",
     *               "close_time": "00:00:00",
     *               "available_range": [
     *                   "blacharsko-lakierniczy (ciężarowe)",
     *                   "blacharsko-lakierniczy (dostawcze do 3,5 t)",
     *                   "blacharsko-lakierniczy (osobowe)",
     *                   "diagnostyka okręgowa (ciężarowe)",
     *                   "diagnostyka okręgowa (osobowe)",
     *                   "diagnostyka podstawowa (ciężarowe)",
     *                   "diagnostyka podstawowa (osobowe)",
     *                   "mechaniczny (ciężarowe)",
     *                   "mechaniczny (osobowe)",
     *                   "wulkanizacyjny (ciężarowe)",
     *                   "wulkanizacyjny (osobowe)"
     *               ],
     *               "available_brands": [
     *                   "Audi",
     *                   "Hyundai"
     *               ],
     *               "plan_groups": [
     *                   {
     *                       "name": "PN1",
     *                       "conditional_list": false
     *                   },
     *                   {
     *                       "name": "PN2",
     *                       "conditional_list": true
     *                   }
     *               ],
     *               "address": "84-351 Nowa Wieś Lęborska, Młynarska 10 ",
     *               "company": {
     *                   "name": " \"Auto-Błysk\" Andrzej Baranowski ",
     *                   "street": "Młynarska 10 ",
     *                   "code": "84-351",
     *                   "city": "Nowa Wieś Lęborska",
     *                   "nip": "",
     *                   "krs": "",
     *                   "regon": "",
     *                   "www": "",
     *                   "email": "",
     *                   "phone": "",
     *                   "account_nr": ""
     *               }
     *           }
     *       ],
     *       "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2lkZWF3LnRlc3RcL2FwaVwvZWFcL2Nhci13b3Jrc2hvcHMiLCJpYXQiOiIxNTcyNDMxMDA4IiwiZXhwIjoiMTU3MjQzNzc4MSIsIm5iZiI6IjE1NzI0MzQxODEiLCJqdGkiOiI4NzZkYzdlNmRjY2U4YmExNzBiODk4NDc2Mzg0NzkyOSJ9.ZDY0Y2IwYWU3YzdjMTI0YjFjMDI5ZWUxMTY5ZWMzZDVlYTFmYzQ4ZGEyNjQyMzQyYjM4NWVkYTVmNjZlZGUxZA"
     *   }
     *
     * @apiErrorExample Brakujący token
     *     HTTP 400
     *     {
     *       "error": "token_not_provided"
     *     }
     *
     * @apiErrorExample Wygasły token
     *     HTTP 401
     *     {
     *       "error": "token_invalid"
     *     }
     *
     * @apiErrorExample Brak przesłanego api key
     *     HTTP 400
     *     {
     *       "error": "api_key_required"
     *     }
     *
     * @apiErrorExample Błędny api key
     *     HTTP 400
     *     {
     *       "error": "api_key_invalid"
     *     }
     *
     * @apiErrorExample Brak przesłanych parametrów
     *     HTTP 400
     *     {
     *        "error": "data_missed"
     *     }
     */
    public function carWorkshops()
    {
        if(
            trim(Input::get('sales_program', '')) == ''
        ){
            return Response::json(['error' => 'data_missed'], 400);
        }

        $haversine = "(6371 * acos(cos(radians(" . Input::get('lat', 0) . ")) * cos(radians(`lat`)) * cos(radians(`lng`) - radians(" . Input::get('lng', 0) . ")) + sin(radians(" . Input::get('lat', 0) . ")) * sin(radians(`lat`))))";

        $workshops = Branch::select('id', DB::raw('short_name AS name'), DB::raw("{$haversine} as distance"), 'company_id', 'nip', 'street', 'code', 'city', 'email', 'phone', 'contact_people', 'lat', 'lng', 'open_time', 'close_time' );

        $workshops = $workshops->where(function ($query) {
                if(Input::get('city')){
                    $query->where('city', 'like', trim(Input::get('city')));
                }

                $sales_program = Input::get('sales_program');

                $query->whereHas('branchPlanGroups', function ($query) use($sales_program){
                    $query->whereHas('planGroup', function($query) use($sales_program){
                        $query->whereHas('plan', function($query) use($sales_program){
                            $query->where('sales_program', $sales_program);
                        });
                    });
                });
            })
            ->with([
                'branchPlanGroups' => function($query){
                    $sales_program = Input::get('sales_program');
                    $query->whereHas('planGroup', function($query) use($sales_program){
                        $query->whereHas('plan', function($query) use($sales_program){
                            $query->where('sales_program', $sales_program);
                        });
                    });
                    $query->with(['planGroup' => function($query){
                        $sales_program = Input::get('sales_program');

                        $query->whereHas('plan', function ($query) use ($sales_program) {
                            $query->where('sales_program', $sales_program);
                        });
                        $query->with('companyGroups');
                    }]);
                },
                'typegarages' ,
                'brands',
                'company' => function($query){
                    $query->select('id', 'name', 'street', 'code', 'city', 'nip', 'krs', 'regon', 'www', 'email', 'phone', 'account_nr');
                }
            ])
            ->limit(100)->get();

        if(Input::get('lat') && Input::get('lng')){
            $workshops = $workshops->filter(function($workshop){
                return $workshop->distance < Input::get('radius', 5);
            });
        }

        foreach ($workshops as $workshop)
        {
            $range_service = $workshop->typegarages->toArray();
            $range_service = array_fetch($range_service, 'name');
            $workshop->available_range = $range_service;
            unset($workshop->typegarages);

            $brands = $workshop->brands->toArray();
            $brands = array_fetch($brands, 'name');
            unset($workshop->brands);
            $workshop->available_brands = $brands;

            $plan_groups = [];
            foreach($workshop->branchPlanGroups as $branch_plan_group)
            {
                $plan_groups[] = [
                    'name' => $branch_plan_group->planGroup->name,
                    'conditional_list' => count($branch_plan_group->planGroup->companyGroups) > 0 ? true : false
                ];
            }
            unset($workshop->branchPlanGroups);
            $workshop->plan_groups = $plan_groups;

            unset($workshop->company_id);
            unset($workshop->company->id);
            unset($workshop->distance);
        }

        $response = compact('workshops');
        $this->logRequest($response);

        $token = JWTAuth::refresh(JWTAuth::getToken());

        return Response::json(compact('workshops', 'token'));
    }

    /**
     * @api {post} ea/register-injury Rejestracja szkody w DLS
     * @apiName Zarejestruj szkodę
     * @apiGroup Metody
     *
     * @apiHeader {String} Authorization Bearer: Token sesji
     *
     * @apiParam {String} api_key Klucz api do modułu
     * @apiParam {Integer} vehicle_id Id pojazdu zwracane z API
     * @apiParam {String} vehicle_type Typ pojazdu zwracane z API
     * @apiParam {String} sales_program Program sprzedaży zwracane z API
     * @apiParam {Integer} [workshop_id] id warsztatu zwracane z API
     * @apiParam {String} [vehicle_vin] vin pojazdu
     * @apiParam {String} [vehicle_registration] nr rejestracyjny pojazdu
     * @apiParam {String} [vehicle_brand] marka pojazdu
     * @apiParam {String} [vehicle_model] model pojazdu
     * @apiParam {String} [vehicle_engine_capacity] pojemność silnika
     * @apiParam {String} [vehicle_year_production] rok produkcji
     * @apiParam {String} [vehicle_first_registration] data pierwszej rejestracja
     * @apiParam {String} [vehicle_mileage] przebieg
     * @apiParam {String} [owner_name] nawa właściciela
     * @apiParam {String} [client_name] nazwa klienta
     * @apiParam {String} [contract_number] nr umowy
     * @apiParam {Date} [contract_end_leasing] data końca leasingu
     * @apiParam {String} [contract_status] status umowy
     * @apiParam {String} [insurance_company_name] polisa - nazwa ZU
     * @apiParam {Date} [insurance_expire_date] polisa - data ważności
     * @apiParam {String} [insurance_policy_number] polisa - nr polisy
     * @apiParam {Integer} [insurance_amount] polisa - suma ubezpieczenia
     * @apiParam {Integer} [insurance_own_contribution] polisa - udział własny
     * @apiParam {Integer} [insurance_net_gross] polisa netto/brutto: 1-netto, 2-brutto, 3-50% VAT
     * @apiParam {Integer} [insurance_assistance] polisa assistance: 1-tak, 0-nie
     * @apiParam {String} [insurance_assistance_name] polisa - nazwa pakietu assistance
     * @apiParam {String} [driver_name] kierowca - imię
     * @apiParam {String} [driver_surname] kierowca - nazwisko
     * @apiParam {String} [driver_phone] kierowca - telefon
     * @apiParam {String} [driver_email] kierowca - email
     * @apiParam {String} [driver_city] kierowca - miasto zamieszkania
     * @apiParam {String} [claimant_name] zgłaszający - imię
     * @apiParam {String} [claimant_surname] zgłaszający - nazwisko
     * @apiParam {String} [claimant_phone] zgłaszający - telefon
     * @apiParam {String} [claimant_email] zgłaszający - imię
     * @apiParam {String} [claimant_city] zgłaszający - miasto zamieszkania
     * @apiParam {Date} [injury_event_date] szkoda - data zdarzenia
     * @apiParam {Time} [injury_event_time] szkoda - godzina zdarzenia
     * @apiParam {String} [injury_event_city] szkoda - miejsce zdarzenia: miasto
     * @apiParam {String} [injury_event_street] szkoda - miejsce zdarzenia: ulica
     * @apiParam {Integer} [injury_type_incident_id] id rodzaju zdarzenia z API
     * @apiParam {String} [injury_event_description] opis okoliczności szkody
     * @apiParam {String} [injury_damage_description] opis uszkodzeń
     * @apiParam {String} [injury_current_location] aktualna pozycja pojazdu
     * @apiParam {String} [injury_reported_insurance_company] szkoda zgłoszona do ZU: 1:tak, 0-nie
     * @apiParam {String} [injury_type] typ szkody
     * @apiParam {String} [injury_number] numer szkody
     * @apiParam {String} [injury_insurance_company] zakład ubezpieczeń
     * @apiParam {String} [injury_police_notified] policja - zawiadomiono: -1:nie ustalono, 0:nie, 1:tak
     * @apiParam {String} [injury_police_number] policja - nr zgłoszenia
     * @apiParam {String} [injury_police_unit] policja - jednostka
     * @apiParam {String} [injury_police_contact] policja - kontakt
     * @apiParam {Integer} [injury_statement] spisano oświadczenia: 1-tak, 0-nie
     * @apiParam {Integer} [injury_taken_registration] zabrano dowód rejestracyjny: 1-tak, 0-nie
     * @apiParam {Integer} [injury_towing] wymaga holowanie: 1-tak, 0-nie
     * @apiParam {Integer} [injury_replacement_vehicle] wymagane auto zastępcze: 1-tak, 0-nie
     * @apiParam {Integer} [injury_vehicle_in_service] samochód znajduje się w warsztacie: 1-tak, 0-nie
     * @apiParam {String} [case_number] numer sprawy
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *           "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjMsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2VhXC9yZWdpc3Rlci1pbmp1cnkiLCJpYXQiOiIxNTc0MTYwNzk2IiwiZXhwIjoiMTU3NDE2NTAxOCIsIm5iZiI6IjE1NzQxNjE0MTgiLCJqdGkiOiI2Mjk2ZDA0MTQ2ZTQ3YTA0YTk0ZTQ2M2U3MDE4OTE0NyJ9.YzFlZmQyZmZiZGVhODQ0MTYzZDkwZjc4ZDM0MTdiOWZiY2JkNDg5OWEzZGQxNWViYWQ2YjZjOWI1MGYxMmY1Nw"
     *      }
     */
    public function registerInjury(){
        if(
            trim(Input::get('vehicle_id', '')) == '' ||
            trim(Input::get('vehicle_type', '')) == ''
        ){
            return Response::json(['error' => 'data_missed'], 400);
        }

        $eaInjury = EaInjury::create(Input::all());

        switch ($eaInjury->vehicle_type){
            case 1:
                $vehicle = VmanageVehicle::findOrFail($eaInjury->vehicle_id);
                $sales_program = $vehicle->salesProgram ? $vehicle->salesProgram->name_key : null;
                $eaInjury->update([
                    'sales_program' => $sales_program
                ]);
                break;
            case 2:
                $vehicle = Vehicles::findOrFail($eaInjury->vehicle_id);
                $sales_program = $vehicle->salesProgram ? $vehicle->salesProgram->name_key : null;
                $eaInjury->update([
                    'sales_program' => $sales_program
                ]);
                break;
            default:
                break;
        }

        $task = Task::create([
            'task_source_id' => 2, //druk online
            'from_email' => $eaInjury->claimant_email,
            'from_name' => $eaInjury->claimant_name.' '.$eaInjury->claimant_surname,
            'subject' => $eaInjury->contract_number.' # '.$eaInjury->vehicle_registration,
            'content' => $eaInjury->description(),
            'task_group_id' => 1,
            'task_date' => $eaInjury->created_at
        ]);

        $eaInjury->tasks()->save($task);

        \Idea\Tasker\Tasker::assign($task);

        $this->logRequest([]);

        $token = JWTAuth::refresh(JWTAuth::getToken());

        return Response::json(compact( 'token'));
    }

    /**
     * @api {post} ea/update-injury Aktualizacja szkody
     * @apiName Zaktualizuj dane szkody
     * @apiGroup Metody
     *
     * @apiHeader {String} Authorization Bearer: Token sesji
     *
     * @apiParam {String} api_key Klucz api do modułu
     * @apiParam {String} case_number Identyfikator szkody EA
     * @apiParam {String} [injury_number] numer szkody
     */
    public function updateInjury(){
        if(
            trim(Input::get('case_number', '')) == ''
        ){
            return Response::json(['error' => 'data_missed'], 400);
        }

        $eaInjury = EaInjury::withTrashed()->where('case_number', Input::get('case_number'))->first();

        if(! $eaInjury){
            return Response::json(['error' => 'injury_not_found'], 400);
        }

        if(Input::has('injury_number')){
            $eaInjury->update(['injury_number' => Input::get('injury_number')]);
            if($eaInjury->injury)
            {
                $eaInjury->injury->update(['injury_number']);
            }
        }

        $this->logRequest([]);

        $token = JWTAuth::refresh(JWTAuth::getToken());

        return Response::json(compact( 'token'));
    }

    /**
     * @api {post} ea/type-incident-list Rodzaje zdarzeń
     * @apiName Lista rodzai zdarzeń
     * @apiGroup Metody
     *
     * @apiHeader {String} Authorization Bearer: Token sesji
     *
     * @apiParam {String} api_key Klucz api do modułu
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *     "typeIncidents": [
     *         {
     *             "id": 1,
     *             "name": "Kolizja z innym pojazdem"
     *         },
     *         {
     *             "id": 2,
     *             "name": "Potrącenie pieszego/ innego uczestnika ruchu"
     *         },
     *         {
     *             "id": 3,
     *             "name": "Kolizja ze zwierzęciem"
     *         }
     *     ],
     *     "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOjMsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9hcGlcL2VhXC90eXBlLWluY2lkZW50LWxpc3QiLCJpYXQiOiIxNTc0MzIxNDE2IiwiZXhwI"
     * }
     */
    public function typeIncidentList(){
        $typeIncidents = Type_incident::orderBy('order')->select( 'id', 'name')->get();

        $this->logRequest($typeIncidents);

        $token = JWTAuth::refresh(JWTAuth::getToken());

        return Response::json(compact('typeIncidents', 'token'));
    }

    private function logRequest($response)
    {
        $api_key = Input::get('api_key');
        $api_module_key = ApiModuleKey::where('api_key', $api_key)->first();
        $user = JWTAuth::parseToken()->authenticate();

        ApiHistory::create([
            'api_module_id' => $api_module_key->api_module_id,
            'api_user_id' => $user ? $user->id : null,
            'request' => json_encode(['url' => Request::url(), 'method' => Request::getMethod(), 'parameters' => Input::all() ]),
            'response' => json_encode($response),
            'ip' => Request::ip()
        ]);
    }
}