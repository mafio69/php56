<?php
use \Idea\Vat\Vat;
class InjuriesController extends BaseController {

	private $counts;
	private $term;
    private $options = [
        null => 'nie ustalono',
        '' => 'nie ustalono',
        '-1' => 'nie ustalono',
        '0' => 'nie',
        '1' => 'tak',
        '2' => 'tak - lista warunkowa'
    ];

    public function __construct(){
		View::composer('injuries.nav', function($view)
		{
			$company_group_list = CompanyGroup::lists('name', 'id');
			$view->with('company_group_list', $company_group_list);
		});

        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia_(szkody)#wejscie');
        $this->beforeFilter('permitted:zlecenia_(szkody)#wyszukaj_szkode', ['only' => ['getSearch', 'getSearchGlobal', 'getSearchGlobalUnprocessed', 'getSearchGlobalRedirect']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#wgraj_szkody_nieprzetworzone', ['only' => ['uploadUnprocessed', 'proceedUnprocessed']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#szkody_nieprzetworzone', ['only' => ['getIndexUnprocessed', 'getIndexPop']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#szkody_zarejestrowane', ['only' => ['getIndexNew', 'getIndexInprogress', 'getIndexCompleted', 'getIndexRefused']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#szkody_calkowite', ['only' => ['getIndexTotal']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#szkody_anulowane', ['only' => ['getIndexCanceled', 'getIndexDeleted']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#zarzadzaj', ['only' => ['setRestoreCompleted', 'setCancel', 'setRestoreDeleted', 'setCompleteRefused', 'setComplete', 'setRefusal', 'setContractSettled', 'setAgreementSettled', 'setTotal', 'setTotalInjuries', 'setTheft', 'setUnlock', 'setLock', 'setTotalFinished', 'setDiscontinuationInvestigation', 'setDeregistrationVehicle', 'setTransferredDok', 'setNoSignsPunishment', 'setUsurpation', 'setRestoreTotal', 'setResignationClaims', 'setDelete']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#zarzadzaj#przepnij_szkode', ['only' => ['setChangeStatus']]);
        $this->beforeFilter('permitted:kartoteka_szkody#wejscie', ['only' => ['getInfo']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_klienta', ['only' => ['setEditInjuryClient']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_szkody', ['only' => ['setEditInjury']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_polisy_ac', ['only' => ['setEditInjuryInsurance']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_informacje_wewnetrzna', ['only' => ['postEditInjuryInfo']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_pojazdu', ['only' => ['postEditVehicle']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_zglaszajacego', ['only' => ['setEditInjuryNotifier']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_kierowcy', ['only' => ['setEditInjuryDriver']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#zmien_osobe_kontaktowa', ['only' => ['setChangeContact']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_wlasciciela', ['only' => ['postEditVehicleOwner']]);
        $this->beforeFilter('permitted:kartoteka_szkody#uszkodzenia#edytuj_uszkodzenia', ['only' => ['setDamage']]);
        $this->beforeFilter('permitted:kartoteka_szkody#uszkodzenia#edytuj_uwagi_do_uszkodzen', ['only' => ['postEditInjuryRemarks_damage']]);
        $this->beforeFilter('permitted:kartoteka_szkody#rozliczenia_szkody#zarzadzaj', ['only' => ['setInvoice', 'setDeleteInvoice', 'setCompensation', 'setDeleteCompensation', 'setEstimate', 'setDeleteEstimate']]);
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator#zarzadzanie_etapem', ['only' => [ 'setChangeInjuryStep' ]]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_sprawcy', ['only' => ['setEditInjuryOffender']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#zmien_etap_procesowania', ['only' => 'setTotalStatus']);

        if(in_array(Request::path(), [
            'injuries/unprocessed',
            'injuries/ea',
            'injuries/pop',
            'injuries/new',
            'injuries/inprogress',
            'injuries/refused',
            'injuries/completed',
            'injuries/total',
            'injuries/theft',
            'injuries/total-finished',
            'injuries/canceled',
            'injuries/deleted',
            'injuries/search/global'
        ]) ) {
            $res = Injury::where('active', '=', 0)
                ->where(function ($query) {
                    if (Session::get('search.injury_type', '0') != 0)
                        $query->where('injuries_type_id', '=', Session::get('search.injury_type'));

                    if (Session::get('search.user_id', '0') != 0)
                        $query->where('user_id', '=', Session::get('search.user_id'));

                    if (Session::get('search.company_type', '0') != 0) {
                        if (Session::get('search.company_type', '0') == '-1') {
                            $query->whereHas('branch', function ($q) {
                                $q->whereHas('company', function ($q) {
                                    $q->has('groups', 0);
                                });
                            });
                        } else {
                            $query->whereHas('branch', function ($q) {
                                $q->whereHas('company', function ($q) {
                                    $q->whereHas('groups', function ($q) {
                                        $q->where('company_group_id', Session::get('search.company_type', '0'));
                                    });
                                });
                            });
                        }
                    }

                    if (Session::get('search.locked_status', '0') == 1)
                        $query->whereIn('locked_status', array(5, '-5'));

                    //gdy vb user
                    /*
                    if(Auth::user()->checkRole(1,5)){
                        $query->vehicleOwnerGroup(array(2, 3));
                    }
                    */

                    $query->where(function($query){
                        $query->where(function($query){
                            $query->where('vehicle_type', 'Vehicles');
                        })
                            ->orWhere(function($query){
                                $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                    $query->whereHas('company', function($query){
                                        $query->whereHas('guardians', function($query){
                                            $query->where('users.id', Auth::user()->id);
                                        });
                                    });
                                });
                            });
                    });

                })
                ->groupBy('step')->get(array('step', DB::raw('count(*) as cnt')));
            $array = array();
            foreach ($res as $k => $row) {
                $array[$row->step] = $row->cnt;
            }

            $unprocessed = MobileInjury::whereActive(0)->where('source', '!=', 3)->where(function ($query) {
                $query->where('source', 0);
                $query->orWhereIn('injuries_type', [2, 1, 3, 6]);
            })->count();
            $array['-1'] = $unprocessed;

            $pop = MobileInjury::whereActive(0)->where('source', 3)->count();
            $array['-2'] = $pop;

            $ea = EaInjury::count();
            $array['-100'] = $ea;

            $letters = InjuryLetter::whereNull('injury_file_id')->count();
            $array['letters'] = $letters;

            $availableSteps = InjurySteps::lists('id');
            foreach ($availableSteps as $step) {
                if (!isset($array[$step])) $array[$step] = 0;
            }

            $this->counts = $array;
        }
	}

    public function getIndexTasksExpired($user = null)
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')
            ->where(function($query) use($user)
            {
                if(Session::get('search.injury_type', '0') != 0)
                    $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

                if(Session::get('search.locked_status', '0') == 1)
                    $query ->whereIn('locked_status', array(5, '-5'));

                $query->where(function($query2) use($user){
                    $query2->orWhereHas('wreck', function ($q)  use($user){
                        $q->where(function($q2) use ($user){
                            $q2->where(function($wreckQuery) use($user){
                                $wreckQuery->where('alert_repurchase', '!=', '0000-00-00')->where('alert_repurchase', '<', date('Y-m-d'))->where('alert_repurchase_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('alert_repurchase_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('alert_buyer', '!=', '0000-00-00')->where('alert_buyer', '<', date('Y-m-d'))->where('alert_buyer_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('alert_buyer_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('pro_forma_request', '!=', '0000-00-00')->where('pro_forma_request', '<', date('Y-m-d'))->where('pro_forma_request_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('pro_forma_request_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('payment', '!=', '0000-00-00')->where('payment', '<', date('Y-m-d'))->where('payment_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('payment_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('invoice_request', '!=', '0000-00-00')->where('invoice_request', '<', date('Y-m-d'))->where('invoice_request_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('invoice_request_user_id', $user);
                                }
                            });
                        });
                    });

                    $query2->orWhereHas('totalRepair', function ($q) use($user) {
                        $q->where('alert_receive', '!=', '0000-00-00')->where('alert_receive', '<', date('Y-m-d'))->where('alert_receive_confirm', '=', '0000-00-00');
                        if(is_numeric($user)){
                            $q->where('alert_receive_user_id', $user);
                        }
                    });

                    $query2->orWhereHas('theft', function ($q) use($user) {
                        $q->where(function($q2) use ($user){
                            $q2->where(function($theftQuery) use($user){
                                $theftQuery->where('send_zu', '!=', '0000-00-00')->where('send_zu', '<', date('Y-m-d'))->where('send_zu_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $theftQuery->where('send_zu_user_id', $user);
                                }
                            })->orWhere(function($theftQuery) use($user){
                                $theftQuery->where('police_memo', '!=', '0000-00-00')->where('police_memo', '<', date('Y-m-d'))->where('police_memo_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $theftQuery->where('police_memo_user_id', $user);
                                }
                            })->orWhere(function($theftQuery) use($user){
                                $theftQuery->where('redemption_investigation', '!=', '0000-00-00')->where('redemption_investigation', '<', date('Y-m-d'))->where('redemption_investigation_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $theftQuery->where('redemption_investigation_user_id', $user);
                                }
                            })->orWhere(function($theftQuery) use($user){
                                $theftQuery->where('deregistration_vehicle', '!=', '0000-00-00')->where('deregistration_vehicle', '<', date('Y-m-d'))->where('deregistration_vehicle_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $theftQuery->where('deregistration_vehicle_user_id', $user);
                                }
                            })->orWhere(function($theftQuery) use($user){
                                $theftQuery->where('compensation_payment', '!=', '0000-00-00')->where('compensation_payment', '<', date('Y-m-d'))->where('compensation_payment_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $theftQuery->where('compensation_payment_user_id', $user);
                                }
                            })->orWhere(function($theftQuery) use($user){
                                $theftQuery->where('gap', '!=', '0000-00-00')->where('gap', '<', date('Y-m-d'))->where('gap_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $theftQuery->where('gap_user_id', $user);
                                }
                            });
                        });
                    });

                    $query2->orWhereHas('chat', function($q) use($user){
                        $q->where('deadline', '<', date('Y-m-d') );
                        if(is_numeric($user)){
                            $q->where('deadline_user_id', $user);
                        }
                    });
                });

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });
            })
            ->where('step' , '!=', '-10')
            ->with('vehicle', 'injuries_type', 'user', 'chat', 'chat.messages')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = 0;

        return View::make('injuries.tasks-expired', compact('injuries', 'users', 'injuries_type', 'counts', 'step'));
    }

    public function getIndexTasksToday($user = null)
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')
            ->where(function($query) use($user)
            {
                if(Session::get('search.injury_type', '0') != 0)
                    $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

                if(Session::get('search.locked_status', '0') == 1)
                    $query ->whereIn('locked_status', array(5, '-5'));


                $query->where(function($query2) use($user){
                    $query2->orWhereHas('wreck', function ($q) use($user) {
                        $q->where(function($q2) use($user){
                            $q2->where(function($wreckQuery) use($user){
                                $wreckQuery->where('alert_repurchase', '=', date('Y-m-d'))->where('alert_repurchase_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('alert_repurchase_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('alert_buyer', '=', date('Y-m-d'))->where('alert_buyer_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('alert_buyer_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('pro_forma_request', '=', date('Y-m-d'))->where('pro_forma_request_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('pro_forma_request_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('payment', '=', date('Y-m-d'))->where('payment_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('payment_user_id', $user);
                                }
                            })->orWhere(function($wreckQuery) use($user){
                                $wreckQuery->where('invoice_request', '=', date('Y-m-d'))->where('invoice_request_confirm', '=', '0000-00-00');
                                if(is_numeric($user)){
                                    $wreckQuery->where('invoice_request_user_id', $user);
                                }
                            });
                        });
                    });

                    $query2->orWhereHas('totalRepair', function ($q) use($user) {
                        $q->where('alert_receive', '=', date('Y-m-d'))->where('alert_receive_confirm', '=', '0000-00-00');
                        if(is_numeric($user)){
                            $q->where('alert_receive_user_id', $user);
                        }
                    });

                    $query2->orWhereHas('theft', function ($q) use($user) {
                        $q->where(function($q2) use($user) {
                            $q2->where(function ($theftQuery) use ($user) {
                                $theftQuery->where('police_memo', '=', date('Y-m-d'))->where('police_memo_confirm', '=', '0000-00-00');
                                if (is_numeric($user)) {
                                    $theftQuery->where('police_memo_user_id', $user);
                                }
                            })->orWhere(function ($theftQuery) use ($user) {
                                $theftQuery->where('redemption_investigation', '=', date('Y-m-d'))->where('redemption_investigation_confirm', '=', '0000-00-00');
                                if (is_numeric($user)) {
                                    $theftQuery->where('redemption_investigation_user_id', $user);
                                }
                            })->orWhere(function ($theftQuery) use ($user) {
                                $theftQuery->where('deregistration_vehicle', '=', date('Y-m-d'))->where('deregistration_vehicle_confirm', '=', '0000-00-00');
                                if (is_numeric($user)) {
                                    $theftQuery->where('deregistration_vehicle_user_id', $user);
                                }
                            })->orWhere(function ($theftQuery) use ($user) {
                                $theftQuery->where('compensation_payment', '=', date('Y-m-d'))->where('compensation_payment_confirm', '=', '0000-00-00');
                                if (is_numeric($user)) {
                                    $theftQuery->where('compensation_payment_user_id', $user);
                                }
                            })->orWhere(function ($theftQuery) use ($user) {
                                $theftQuery->where('send_zu', '=', date('Y-m-d'))->where('send_zu_confirm', '=', '0000-00-00');
                                if (is_numeric($user)) {
                                    $theftQuery->where('send_zu_user_id', $user);
                                }
                            })->orWhere(function ($theftQuery) use ($user) {
                                $theftQuery->where('gap', '=', date('Y-m-d'))->where('gap_confirm', '=', '0000-00-00');
                                if (is_numeric($user)) {
                                    $theftQuery->where('gap_user_id', $user);
                                }
                            });
                        });
                    });

                    $query2->orWhereHas('chat', function($q) use($user){
                        $q->where('deadline', '=', date('Y-m-d') );
                        if(is_numeric($user)){
                            $q->where('deadline_user_id', $user);
                        }
                    });
                });

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });
            })
            ->where('step' , '!=', '-10')
            ->with('vehicle', 'injuries_type', 'user', 'chat', 'chat.messages')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = 0;

        return View::make('injuries.tasks-today', compact('injuries', 'users', 'injuries_type', 'counts', 'step'));
    }

	public function getSearch(){

		$last =  URL::previous();
		$url = strtok($last, '?');

        $gets = '?';

		if(Input::has('search_term')){

			if(Input::has('case_nr'))
				$gets .= 'case_nr=1&';

			if(Input::has('registration'))
				$gets .= 'registration=1&';

			if(Input::has('injury_nr'))
				$gets .= 'injury_nr=1&';

			if(Input::has('leasing_nr'))
				$gets .= 'leasing_nr=1&';

			if(Input::has('address'))
				$gets .= 'address=1&';

			if(Input::has('global'))
				$gets .= 'global=1&';

			if(Input::has('surname'))
				$gets .= 'surname=1&';

			if(Input::has('client'))
				$gets .= 'client=1&';

            if(Input::has('NIP'))
                $gets .= 'NIP=1&';

            if(Input::has('VIN'))
                $gets .= 'VIN=1&';

            if(Input::has('firmID'))
                $gets .= 'firmID=1&';

            if(Input::has('invoice_number'))
                $gets .= 'invoice_number=1&';

			$gets.='term='.Input::get('search_term');
		}

        if(! Input::has('global')) {
            if (Input::has('garage_in_group'))
                $gets .= 'garage_in_group=1&';
            if (Input::has('garage_without_group'))
                $gets .= 'garage_without_group=1&';
            if (Input::has('proceed_without_garage'))
                $gets .= 'proceed_without_garage=1&';
            if (Input::has('to_settle'))
                $gets .= 'to_settle=1&';
            if (Input::has('if_cfm'))
                $gets .= 'if_cfm=1&';
            if (Input::has('if_vip'))
                $gets .= 'if_vip=1&';
        }

        if(Input::has('document_type_id') && Input::get('document_type_id') != '0'){
            $gets .= 'document_type_id='.Input::get('document_type_id').'&';
        }

		if(Input::has('global')){
			echo URL::route('injuries-search-getAll').$gets;
		}else{
			echo $url.$gets;
		}

	}

    public function getSearchGlobal(){

        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')
            ->where(function($query)
            {
                if(Session::get('search.injury_type', '0') != 0)
                    $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

                if(! Auth::user()->can('zlecenia_(szkody)#szkody_zarejestrowane')){
                    $query->whereNotIn('step', [0,'10', '11', '13', '14', '15', '16', '17', '18', '19', '21', '23', '24', '25', '26', '38', '20', '22']);
                }

                if(! Auth::user()->can('zlecenia_(szkody)#szkody_calkowite')){
                    $query->whereNotIn('step', [30,31,32,33]);
                }

                if(! Auth::user()->can('zlecenia_(szkody)#szkody_anulowane')){
                    $query->whereNotIn('step', ['-10']);
                }

                if(Session::get('search.locked_status', '0') == 1)
                    $query ->whereIn('locked_status', array(5, '-5'));

                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){
                    $this->passingWheres($query);
                }

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });


            })
            ->with('vehicle', 'injuries_type', 'user', 'chat', 'chat.messages', 'status')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));



        if(Input::has('term') && (Input::has('registration') || Input::has('leasing_nr') || Input::has('injury_nr'))) {
            $matchedLetters = InjuryLetter::whereNull('injury_file_id')->where(function ($query) {
                if(Input::has('registration')){
                    $query->orWhere(function ($subquery) {
                        $subquery->whereNotNull('registration')->where('registration', '!=', '')->where('registration', 'like', '%' . Input::get('term') . '%');
                    });
                }

                if(Input::has('leasing_nr')){
                    $query->orWhere(function ($subquery) {
                        $subquery->whereNotNull('nr_contract')->where('nr_contract', '!=', '')->where('nr_contract', 'like', '%' . Input::get('term') . '%');
                    });
                }

                if(Input::has('injury_nr')){
                    $query->orWhere(function ($subquery) {
                        $subquery->whereNotNull('injury_nr')->where('injury_nr', '!=', '')->where('injury_nr', 'like', '%' . Input::get('term') . '%');
                    });
                }
            })->get();
        }else {
            $matchedLetters = new \Illuminate\Database\Eloquent\Collection();
        }

        $counts = $this->counts;

        $step = 0;

        $unprocessed = MobileInjury::where('active', '=', '0')
            ->where(function($query){
                $query->where('source', 0);
                $query->orWhereIn('injuries_type', [2,1,3,6]);
            })
            ->where('source', '!=', 3)
            ->where(function($query)
            {
                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){

                    $query->where(function($query2){

                        if(Input::has('registration')){
                            $query2 -> orWhere('registration', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('leasing_nr')){
                            $query2 -> orWhere('nr_contract', 'like', '%'.Input::get('term').'%');
                        }


                        if(Input::has('address')){
                            $query2 -> orWhere('event_city', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('surname')){
                            $query2 -> orWhere('notifier_surname', 'like', '%'.Input::get('term').'%');
                        }

                    });
                }

            })
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->count();

        return View::make('injuries.search-global', compact('injuries', 'users', 'injuries_type', 'counts', 'step', 'matchedLetters','unprocessed'));
    }

    public function getSearchGlobalUnprocessed(){

        $unprocessed = MobileInjury::where('active', '=', '0')
            ->where(function($query)
            {
                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){

                    $query->where(function($query2){

                        if(Input::has('registration')){
                            $query2 -> orWhere('registration', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('leasing_nr')){
                            $query2 -> orWhere('nr_contract', 'like', '%'.Input::get('term').'%');
                        }

                    });
                }

            })
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        return View::make('injuries.search-global-unprocessed', compact('unprocessed'));
    }

    public function getSearchGlobalRedirect($id){
        Session::put('last_injury', $id);
        $pagination = Session::get('search.pagin', '10');

        Session::put('search.injury_type', 0);
        Session::put('search.user_id', 0);
        Session::put('search.company_type', 0);
        Session::put('search.locked_status', 0);

        $injury = Injury::find($id);
        if(in_array($injury->step , array('15', '17', '19', '20') ))
            $injuriesAll = Injury::where('active', '=', '0')->whereIn('step', array('15', '17', '19', '20') )->where('id', '>=', $id)->get();
        else
            $injuriesAll = Injury::where('active', '=', '0')->where('step', '=', $injury->step)->where('id', '>=', $id)->get();
        $ctInjuriesAll = $injuriesAll->count();

        $page = floor( $ctInjuriesAll /$pagination ) + 1;
        $step = '';
        switch ($injury->step) {
            case '-10':
                $step = 'canceled';
                break;
            case '-7':
                $step = 'total-finished';
                break;
            case '-5':
                $step = 'total';
                break;
            case '-3':
                $step = 'theft';
                break;
            case '0':
                $step = 'new';
                break;
            case '10':
                $step = 'inprogress';
                break;
            case '15':
                $step = 'completed';
                break;
            case '17':
                $step = 'completed';
                break;
            case '19':
                $step = 'completed';
                break;
            case '20':
                $step = 'completed';
                break;
        }
        return Redirect::to('/injuries/'.$step.'?page='.$page);

    }

    public function getIndexUnprocessed()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $query = MobileInjury::where('active', '=', '0')
            ->where(function($query){
                $query->where('source', 0);
                $query->orWhereIn('injuries_type', [2,1,3,6]);
            });
        if(Input::has('term')){
            $query->where(function($query)
            {
                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){

                    $query->where(function($query2){

                        if(Input::has('registration')){
                            $query2 -> orWhere('registration', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('leasing_nr')){
                            $query2 -> orWhere('nr_contract', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('address')){
                            $query2 -> orWhere('event_city', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('surname')){
                            $query2 -> orWhere('notifier_surname', 'like', '%'.Input::get('term').'%');
                        }

                    });
                }

            });
        }

        $query->where('source', '!=', 3)
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc');
        $injuries = $query->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = '-1';

        return View::make('injuries.unprocessed', compact('injuries', 'users', 'counts', 'injuries_type', 'step'));
    }

    public function getIndexPop()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $injuries = MobileInjury::where('active', '=', '0')
            ->where(function($query)
            {
                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){

                    $query->where(function($query2){

                        if(Input::has('registration')){
                            $query2 -> orWhere('registration', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('leasing_nr')){
                            $query2 -> orWhere('nr_contract', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('address')){
                            $query2 -> orWhere('event_city', 'like', '%'.Input::get('term').'%');
                        }

                        if(Input::has('surname')){
                            $query2 -> orWhere('notifier_surname', 'like', '%'.Input::get('term').'%');
                        }

                    });
                }

            })
            ->where('source', 3)
            ->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = '-2';

        return View::make('injuries.pop', compact('injuries', 'users', 'counts', 'injuries_type', 'step'));
    }

    public function getIndexNew()
	{
		$users = User::where('active','=',0)->get();

		$injuries_type = Injuries_type::all();
        $injuries = Injury::where('active', '=', '0')
            ->where('step', '=', '0')
	        ->where(function($query)
	        {
	        	if(Session::get('search.injury_type', '0') != 0)
					$query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

				if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );

				if(Session::get('search.locked_status', '0') == 1)
					$query ->whereIn('locked_status', array(5, '-5'));

				//czy ustawione jest filtrowanie wyszukiwaniem
				if(Input::has('term')){
                    $this->passingWheres($query);
				}

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

	        })
	        ->with('vehicle', 'vehicle.owner', 'injuries_type', 'user', 'chat', 'chat.messages', 'stepStage', 'leader')
            ->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = 0;

        return View::make('injuries.new', compact('injuries', 'users', 'counts', 'injuries_type', 'step'));
	}

	public function getIndexInprogress()
	{
		$users = User::where('active','=',0)->get();

		$injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')->whereIn('step', ['10', '11', '13', '14'])
        	->where(function($query)
	        {
	        	if(Session::get('search.injury_type', '0') != 0)
					$query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

				if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );


                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

				if(Session::get('search.locked_status', '0') == 1)
					$query ->whereIn('locked_status', array(5, '-5'));

				//czy ustawione jest filtrowanie wyszukiwaniem
				if( Input::has('term') ){
                    $this->passingWheres($query);
				}

                if (Input::has('garage_in_group') || Input::has('garage_without_group') || Input::has('proceed_without_garage') || Input::has('to_settle') || Input::has('if_cfm') || Input::has('if_vip')){
                    if (Input::has('to_settle')){
                        $query->where('step', 13);
                    }

                    if (Input::has('garage_in_group') ){
                        $query->whereHas('branch', function($query){
                            $query->whereHas('company', function($query){
                                $query->has('groups');
                            });
                        });
                    }

                    if (Input::has('garage_without_group')){
                        $query->whereHas('branch', function($query){
                            $query->whereHas('company', function($query){
                                $query->has('groups', 0);
                            });
                        });
                    }

                    if (Input::has('proceed_without_garage')){
                        $query->has('branch', 0);
                    }

                    if (Input::has('if_cfm')){
                        $query->vehicleExists('cfm', 1, 'where');
                    }

                    if (Input::has('if_vip')){
                        $query->where('if_vip', 1);
                    }
                }

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

	        })
        	->with('vehicle', 'vehicle.owner', 'injuries_type','branch', 'branch.company', 'branch.company.groups', 'chat', 'chat.messages', 'user',  'documents',  'stepStage','leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = 10;

        return View::make('injuries.inprogress', compact('injuries', 'counts', 'users', 'injuries_type', 'step'));
	}

	public function getIndexRefused(){
        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')->whereIn('step', ['20', '22'])
            ->where(function($query)
            {
                if(Session::get('search.injury_type', '0') != 0)
                    $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );


                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

                if(Session::get('search.locked_status', '0') == 1)
                    $query ->whereIn('locked_status', array(5, '-5'));

                //czy ustawione jest filtrowanie wyszukiwaniem
                if( Input::has('term') ){
                    $this->passingWheres($query);
                }

                if (Input::has('garage_in_group') || Input::has('garage_without_group') || Input::has('proceed_without_garage') || Input::has('to_settle') || Input::has('if_cfm') || Input::has('if_vip')){
                    if (Input::has('to_settle')){
                        $query->where('step', 13);
                    }

                    if (Input::has('garage_in_group') ){
                        $query->whereHas('branch', function($query){
                            $query->whereHas('company', function($query){
                                $query->has('groups');
                            });
                        });
                    }

                    if (Input::has('garage_without_group')){
                        $query->whereHas('branch', function($query){
                            $query->whereHas('company', function($query){
                                $query->has('groups', 0);
                            });
                        });
                    }

                    if (Input::has('proceed_without_garage')){
                        $query->has('branch', 0);
                    }

                    if (Input::has('if_cfm')){
                        $query->vehicleExists('cfm', 1, 'where');
                    }

                    if (Input::has('if_vip')){
                        $query->where('if_vip', 1);
                    }
                }

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

            })
            ->with('vehicle', 'vehicle.owner', 'injuries_type','branch', 'branch.company', 'branch.company.groups', 'chat', 'chat.messages', 'user', 'documents', 'stepStage', 'leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = 20;

        return View::make('injuries.refused', compact('injuries', 'counts', 'users', 'injuries_type', 'step'));
    }

	public function getIndexCompleted()
	{
		$users = User::where('active','=',0)->get();

		$injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')->whereIn('step', array('15', '16', '17', '18', '19', '21', '23', '24', '25', '26', '38') )
			->where(function($query)
	        {
	        	if(Session::get('search.injury_type', '0') != 0)
					$query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

				if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

				if(Session::get('search.locked_status', '0') == 1)
					$query ->whereIn('locked_status', array(5, '-5'));

				//czy ustawione jest filtrowanie wyszukiwaniem
				if(Input::has('term')){
                    $this->passingWheres($query);
				}

                if (Input::has('garage_in_group') || Input::has('garage_without_group') || Input::has('proceed_without_garage') || Input::has('to_settle') || Input::has('if_cfm')){
                    if (Input::has('to_settle')){
                        $query->where('step', 16);
                    }

                    if (Input::has('garage_in_group') ){
                        $query->whereHas('branch', function($query){
                            $query->whereHas('company', function($query){
                                $query->has('groups');
                            });
                        });
                    }

                    if (Input::has('garage_without_group')){
                        $query->whereHas('branch', function($query){
                            $query->whereHas('company', function($query){
                                $query->has('groups', 0);
                            });
                        });
                    }

                    if (Input::has('proceed_without_garage')){
                        $query->has('branch', 0);
                    }

                    if (Input::has('if_cfm')){
                        $query->vehicleExists('cfm', 1, 'where');
                    }
                }

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

            })
			->with('vehicle', 'vehicle.owner', 'injuries_type','branch', 'branch.company', 'branch.company.groups', 'chat', 'chat.messages', 'status', 'stepStage', 'leader')->orderBy('date_end','desc')->limit(30)->get();

        $counts = $this->counts;

        $step = '15';

        return View::make('injuries.completed', compact('injuries', 'counts', 'users', 'injuries_type', 'step'));
	}


	public function getIndexTotal()
	{
		$users = User::where('active','=',0)->get();

		$injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')->whereIn('step', [30,31,32,33])
        	->where(function($query)
	        {
	        	if(Session::get('search.injury_type', '0') != 0)
					$query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

				if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

				if(Session::get('search.locked_status', '0') == 1)
					$query ->whereIn('locked_status', array(5, '-5'));

				//czy ustawione jest filtrowanie wyszukiwaniem
				if(Input::has('term')){
                    $this->passingWheres($query);
				}

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

            })
        	->with('vehicle', 'vehicle.owner', 'injuries_type','branch', 'branch.company', 'branch.company.groups', 'chat', 'chat.messages', 'totalStatus', 'wreck', 'leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        return View::make('injuries.total', compact('injuries', 'counts', 'users', 'injuries_type'));
	}

    public function getIndexTotalFinished()
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')->whereIn('step', [34,35,36,37,44,45,46,47,'-7'])
            ->where(function($query)
            {
                if(Session::get('search.injury_type', '0') != 0)
                    $query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

                if(Session::get('search.user_id', '0') != 0)
                    $query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

                if(Session::get('search.locked_status', '0') == 1)
                    $query ->whereIn('locked_status', array(5, '-5'));

                //czy ustawione jest filtrowanie wyszukiwaniem
                if(Input::has('term')){
                    $this->passingWheres($query);
                }

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

            })
            ->with('vehicle', 'vehicle.owner', 'injuries_type','branch', 'branch.company', 'branch.company.groups', 'chat', 'chat.messages', 'totalStatus', 'wreck', 'leader', 'status')->orderBy('date_end','desc')->limit(30)->get();

        $counts = $this->counts;


        $step = '-7';

        return View::make('injuries.total-finished', compact('injuries', 'counts', 'users', 'injuries_type', 'step'));
    }

	public function getIndexTheft()
	{
		$users = User::where('active','=',0)->get();

		$injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')->whereIn('step', [40,41,42,43])
        	->where(function($query)
	        {
	        	if(Session::get('search.injury_type', '0') != 0)
					$query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

				if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

				if(Session::get('search.locked_status', '0') == 1)
					$query ->whereIn('locked_status', array(5, '-5'));

				//czy ustawione jest filtrowanie wyszukiwaniem
				if(Input::has('term')){
                    $this->passingWheres($query);
				}

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */
                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

            })
        	->with('vehicle', 'vehicle.owner', 'injuries_type','branch', 'branch.company', 'branch.company.groups', 'chat', 'chat.messages', 'leader')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = '-3';

        return View::make('injuries.theft', compact('injuries', 'counts', 'users', 'injuries_type', 'step'));
	}

	public function getIndexCanceled()
	{
		$users = User::where('active','=',0)->get();

		$injuries_type = Injuries_type::all();

        $injuries = Injury::where('active', '=', '0')->where('step', '=', '-10')
        	->where(function($query)
	        {
	        	if(Session::get('search.injury_type', '0') != 0)
					$query ->where('injuries_type_id', '=', Session::get('search.injury_type') );

				if(Session::get('search.user_id', '0') != 0)
					$query ->where('user_id', '=', Session::get('search.user_id') );

                if(Session::get('search.leader_id', '0') != 0)
                    $query ->where('leader_id', '=', Session::get('search.leader_id') );

                if(Session::get('search.company_type', '0') != 0) {
                    if(Session::get('search.company_type', '0') == '-1')
                    {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->has('groups', 0);
                            });
                        });
                    }else {
                        $query->whereHas('branch', function ($q) {
                            $q->whereHas('company', function ($q) {
                                $q->whereHas('groups', function ($q) {
                                    $q->where('company_group_id', Session::get('search.company_type', '0'));
                                });
                            });
                        });
                    }
                }

				if(Session::get('search.locked_status', '0') == 1)
					$query ->whereIn('locked_status', array(5, '-5'));

				//czy ustawione jest filtrowanie wyszukiwaniem
				if(Input::has('term')){
                    $this->passingWheres($query);
				}

                //gdy vb user
                /*
                if(Auth::user()->checkRole(1,5)){
                    $query->vehicleOwnerGroup(array(2, 3));
                }
                */

                $query->where(function($query){
                    $query->where(function($query){
                        $query->where('vehicle_type', 'Vehicles');
                    })
                        ->orWhere(function($query){
                            $query->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function ($query){
                                $query->whereHas('company', function($query){
                                    $query->whereHas('guardians', function($query){
                                        $query->where('users.id', Auth::user()->id);
                                    });
                                });
                            });
                        });
                });

            })
        	->with('vehicle', 'vehicle.owner', 'injuries_type','branch', 'branch.company', 'branch.company.groups', 'chat', 'chat.messages', 'leader')->orderBy('date_end','desc')->limit(30)->get();

        $counts = $this->counts;

        $step = '-10';

        return View::make('injuries.canceled', compact('injuries', 'counts', 'users', 'injuries_type', 'step'));
	}


		public function getIndexDeleted()
		{
			$users = User::where('active','=',0)->get();

			$injuries_type = Injuries_type::all();

			$injuries = MobileInjury::where('active', '=', '9')
					->where(function($query)
					{
							//czy ustawione jest filtrowanie wyszukiwaniem
							if(Input::has('term')){

									$query->where(function($query2){

											if(Input::has('registration')){
													$query2 -> orWhere('registration', 'like', '%'.Input::get('term').'%');
											}

											if(Input::has('leasing_nr')){
													$query2 -> orWhere('nr_contract', 'like', '%'.Input::get('term').'%');
											}

											if(Input::has('address')){
													$query2 -> orWhere('event_city', 'like', '%'.Input::get('term').'%');
											}

											if(Input::has('surname')){
													$query2 -> orWhere('notifier_surname', 'like', '%'.Input::get('term').'%');
											}

									});
							}

					})
					->with('files', 'damages', 'injuries_type')->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

			$counts = $this->counts;

			$step = '-1';

			return View::make('injuries.deleted', compact('injuries', 'users', 'counts', 'injuries_type', 'step'));
		}


	public function getCreate()
	{
		$injuries_type = Injuries_type::whereIf_injury_vehicle(1)->get();
		$receives = Receives::all();
		$invoicereceives = Invoicereceives::all();
		$type_incident = Type_incident::orderBy('order')->get();
		$insurance_companies = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();
		$damage = Damage_type::all();

        return View::make('injuries.create', compact( 'damage', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives'));
	}


	//dodawanie szkody
	public function postCreate(){

		if( Input::has('if_map') ) $if_map = 1; else $if_map = 0;
		if( Input::has('if_map_correct') ) $if_map_correct = 1; else $if_map_correct = 0;
        if( Input::has('contact_person') ) $contact_person = 2; else $contact_person = 1;

		if( Input::has('driver_id') &&  Input::get('driver_id') != ''){
			$driver_id = Input::get('driver_id');
		}else{
            if(Input::get('driver_surname') != '' || Input::get('driver_name') != '' || Input::get('driver_phone') != '') {
                $driver = Drivers::create(array(
                    'client_id' => Input::get('client_id'),
                    'surname' => mb_strtoupper(Input::get('driver_surname'), 'UTF-8'),
                    'name' => mb_strtoupper(Input::get('driver_name'), 'UTF-8'),
                    'phone' => mb_strtoupper(Input::get('driver_phone'), 'UTF-8'),
                    'city' => mb_strtoupper(Input::get('driver_city'), 'UTF-8'),
                    'email' => Input::get('driver_email'),
                ));
                $driver_id = $driver->id;
            }else
                $driver_id = '';
		}

		if( Input::get('info') != ''){
			$insert = Text_contents::create(array(
				'content' => nl2br(Input::get('info'))
			));

			$info_id = $insert->id;
		}else{
			$info_id = '0';
		}

		if( Input::get('remarks') != ''){
			$insert = Text_contents::create(array(
				'content' => nl2br(Input::get('remarks'))
			));

			$remarks_id = $insert->id;
		}else{
			$remarks_id = '0';
		}

		if( Input::has('zdarzenie') && Input::get('zdarzenie') == 12 ) $if_theft = 1; else $if_theft = 0;

		if(Input::get('injuries_type') == '2' || Input::get('injuries_type') == '4' || Input::get('injuries_type') == '5'){
			$offender = Offenders::create(array(
					'surname'	=>	mb_strtoupper( Input::get('offender_surname'), 'UTF-8'),
					'name'		=>	mb_strtoupper( Input::get('offender_name'), 'UTF-8'),
					'post'		=>	mb_strtoupper( Input::get('offender_post'), 'UTF-8'),
					'city'		=>	mb_strtoupper( Input::get('offender_city'), 'UTF-8'),
					'street'	=>	mb_strtoupper( Input::get('offender_street'), 'UTF-8'),
					'registration'	=>	mb_strtoupper( Input::get('offender_registration'), 'UTF-8'),
					'car'		=>	mb_strtoupper( Input::get('offender_car'), 'UTF-8'),
					'oc_nr'		=>	mb_strtoupper( Input::get('offender_oc_nr'), 'UTF-8'),
					'zu'		=>	mb_strtoupper( Input::get('offender_zu'), 'UTF-8'),
					'expire'	=> 	mb_strtoupper( Input::get('offender_expire'), 'UTF-8'),
					'owner'		=>	mb_strtoupper( Input::get('offender_owner'), 'UTF-8'),
					'remarks'	=>	Input::get('offender_remarks')
				));
			$id_offender = $offender->id;
		}else $id_offender = 0;

        if(Input::get('vehicle_type') == 'Vehicles') {
            $vehicle = Vehicles::find(Input::get('vehicle_id'));

            if(Input::get('vin_grp') == 0 ){
                $new_vin = mb_strtoupper(Input::get('vin'), 'UTF-8');
            }else{
                $new_vin = $vehicle->VIN;
            }

            if(Input::get('brand_grp') == 0){
                $new_brand = mb_strtoupper(Input::get('brand'), 'UTF-8');
            }else{
                $new_brand = $vehicle->brand;
            }

            if(Input::get('model_grp') == 0){
                $new_model = mb_strtoupper(Input::get('model'), 'UTF-8');
            }else{
                $new_model = $vehicle->model;
            }

            if (Input::has('engine'))
                $new_engine = mb_strtoupper(Input::get('engine'), 'UTF-8');
            else
                $new_engine = $vehicle->engine;

            if(Input::get('year_production_grp') == 0){
                $new_year_production = mb_strtoupper(Input::get('year_production'), 'UTF-8');
            }else{
                $new_year_production = $vehicle->year_production;
            }

            if (Input::has('first_registration'))
                $new_first_registration = mb_strtoupper(Input::get('first_registration'), 'UTF-8');
            else
                $new_first_registration = $vehicle->first_registration;

            $new_mileage = mb_strtoupper(Input::get('mileage'), 'UTF-8');

            if(Input::get('owner_grp') == 0){
                $new_owner_id = Input::get('owner_id');
            }else{
                $new_owner_id = $vehicle->owner_id;
            }

            if(Input::get('client_grp') == 0){
                $new_client_id = Input::get('client_id');
            }else{
                $new_client_id = $vehicle->client_id;
            }

            if(Input::get('end_leasing_grp') == 0){
                $new_end_leasing = Input::get('end_leasing');
            }else{
                $new_end_leasing = $vehicle->end_leasing;
            }

            if(Input::get('contract_status_grp') == 0){
                $new_contract_status = mb_strtoupper(Input::get('contract_status'), 'UTF-8');
            }else{
                $new_contract_status = $vehicle->contract_status;
            }

            if(Input::get('insurance_company_grp') == 0){
                $new_insurance_company_id = Input::get('insurance_company_id');
            }else{
                $new_insurance_company_id = $vehicle->insurance_company_id;
            }

            $new_insurance_name = $vehicle->insurance_company_name;

            if(Input::get('expire_grp') == 0){
                $new_expire = Input::get('expire');
            }else{
                $new_expire = $vehicle->expire;
            }

            if (Input::has('contribution'))
                $new_contribution = mb_strtoupper(Input::get('contribution'), 'UTF-8');
            else
                $new_contribution = $vehicle->contribution;

            if(Input::get('assistance_grp') == 0){
                $new_assistance = mb_strtoupper(Input::get('assistance'), 'UTF-8');
            }else{
                $new_assistance = $vehicle->assistance;
            }

            if(Input::get('assistance_name_grp') == 0){
                $new_assistance_name = mb_strtoupper(Input::get('assistance_name'), 'UTF-8');
            }else{
                $new_assistance_name = $vehicle->assistance_name;
            }

            if (Input::has('netto_brutto'))
                $new_netto_brutto = Input::get('netto_brutto');
            else
                $new_netto_brutto = $vehicle->netto_brutto;

            if (Input::has('insurance'))
                $new_insurance = mb_strtoupper(Input::get('insurance'), 'UTF-8');
            else
                $new_insurance = $vehicle->insurance;

            if(Input::get('nr_policy_grp') == 0){
                $new_nr_policy = mb_strtoupper(Input::get('nr_policy'), 'UTF-8');
            }else{
                $new_nr_policy = $vehicle->nr_policy;
            }

            $vehicle_new = Vehicles::create(array(
                'owner_id' => $new_owner_id,
                'client_id' => $new_client_id,
                'parent_id' => Input::get('vehicle_id'),
                'registration' => mb_strtoupper(Input::get('registration'), 'UTF-8'),
                'VIN' => mb_strtoupper($new_vin, 'UTF-8'),
                'brand' => mb_strtoupper($new_brand, 'UTF-8'),
                'model' => mb_strtoupper($new_model, 'UTF-8'),
                'engine' => mb_strtoupper($new_engine, 'UTF-8'),
                'nr_contract' => mb_strtoupper(Input::get('nr_contract'), 'UTF-8'),
                'year_production' => $new_year_production,
                'first_registration' => $new_first_registration,
                'mileage' => $new_mileage,
                'expire' => $new_expire,
                'contribution' => mb_strtoupper($new_contribution, 'UTF-8'),
                'assistance' => mb_strtoupper($new_assistance, 'UTF-8'),
                'assistance_name' => mb_strtoupper($new_assistance_name, 'UTF-8'),
                'insurance' => mb_strtoupper($new_insurance, 'UTF-8'),
                'nr_policy' => mb_strtoupper($new_nr_policy, 'UTF-8'),
                'contract_status' => mb_strtoupper($new_contract_status, 'UTF-8'),
                'insurance_company_id' => $new_insurance_company_id,
                'policy_insurance_company_id' => $new_insurance_company_id,
                'insurance_company_name' => mb_strtoupper($new_insurance_name, 'UTF-8'),
                'end_leasing' => $new_end_leasing,
                'netto_brutto' => $new_netto_brutto,
                'gap' => $vehicle->gap,
                'legal_protection' => $vehicle->legal_protection,
                'cfm' => Input::get('cfm'),
                'register_as' => Input::get('register_as')
            ));
        }else{
            $vehicle = VmanageVehicle::find(Input::get('vehicle_id'));

            $vehicle_new = VmanageVehicle::create($vehicle->toArray());
            $vehicle_new->update(Input::all());

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
        }

		if( isset($new_contract_status)
            &&
            ! str_contains(mb_strtoupper($new_contract_status, 'UTF-8'), 'AKTYWNA')
        ) {
            $locked_status = 5;
        }else {
            $locked_status = 0;
        }

		$last_injury = Injury::orderBy('id', 'desc')->limit('1')->get();
		if( isCasActive() ) {
			if (!$last_injury->isEmpty()) {
				$case_nr = $last_injury->first()->case_nr;
				if (strpos($case_nr, 'C') !== false) {
					$case_nr = substr($case_nr, 0, -2);
				}

				if (substr($case_nr, -4) == date('Y')) {
					$case_nr = intval(substr($case_nr, 0, -5));
					$case_nr++;
					$case_nr .= '/' . date('Y').'/C';
				} else {
					$case_nr = '1/' . date('Y').'/C';
				}
			} else {
				$case_nr = '1/' . date('Y').'/C';
			}
		}else{
			if (!$last_injury->isEmpty()) {
				$case_nr = $last_injury->first()->case_nr;
				if (substr($case_nr, -4) == date('Y')) {
					$case_nr = intval(substr($case_nr, 0, -5));
					$case_nr++;
					$case_nr .= '/' . date('Y');
				} else {
					$case_nr = '1/' . date('Y');
				}
			} else {
				$case_nr = '1/' . date('Y');
			}
		}


		$injury = Injury::create(array(
			'user_id' 		=> Auth::user()->id,
			'vehicle_id' 	=> $vehicle_new->id,
            'vehicle_type'  => Input::get('vehicle_type'),
			'client_id' 	=> Input::get('client_id'),
			'driver_id' 	=> $driver_id,
			'notifier_surname' 	=> mb_strtoupper(Input::get('notifier_surname'), 'UTF-8'),
			'notifier_name' 	=> mb_strtoupper(Input::get('notifier_name'), 'UTF-8'),
			'notifier_phone' 	=> mb_strtoupper(Input::get('notifier_phone'), 'UTF-8'),
			'notifier_email' 	=> Input::get('notifier_email'),
			'injuries_type_id' 	=> Input::get('injuries_type'),
			'offender_id'		=>	$id_offender,
			'info' 			=> $info_id,
			'remarks' 		=> $remarks_id,
			'police' 		=> mb_strtoupper(Input::get('police'), 'UTF-8'),
			'police_nr' 	=> mb_strtoupper(Input::get('police_nr'), 'UTF-8'),
			'police_unit'	=> mb_strtoupper(Input::get('police_unit'), 'UTF-8'),
			'police_contact'=> mb_strtoupper(Input::get('police_contact'), 'UTF-8'),
			'injury_nr'		=> mb_strtoupper(Input::get('injury_nr'), 'UTF-8'),
			'case_nr'		=> $case_nr,
			'date_event' 	=> Input::get('date_event'),
			'time_event' 	=> (Input::get('time_event')!='') ? Input::get('time_event') : null,
			//'event_post' 	=> Input::get('event_post'),
			'event_city' 	=> mb_strtoupper(Input::get('event_city'), 'UTF-8'),
			'event_street' 	=> mb_strtoupper(Input::get('event_street'), 'UTF-8'),
			'if_map' 		=> $if_map,
			'if_map_correct' 	=> $if_map_correct,
			'lat' 			=> Input::get('lat'),
			'lng' 			=> Input::get('lng'),
			'receive_id' 	=> Input::get('receives'),
			'invoicereceives_id' 	=> Input::get('invoicereceives'),
			'type_incident_id'	=> Input::get('zdarzenie'),
            'contact_person'    => $contact_person,
			'if_statement'	=> Input::get('if_statement'),
			'if_registration_book'	=> Input::get('if_registration_book'),
			'if_towing'		=> Input::get('if_towing'),
			'if_courtesy_car'	=> Input::get('if_courtesy_car'),
			'if_door2door'	=> Input::get('if_door2door'),
			'if_theft'		=> $if_theft,
			'locked_status'	=> $locked_status,
            'way_of'        => (Input::get('insert_role') == 'adm') ? 1 : 2,
            'settlement_cost_estimate' => (Input::has('settlement_cost_estimate')) ? 1 : 0,
            'if_driver_fault'   => Input::get('if_driver_fault'),
            'if_vip'            => (Input::has('if_vip') ) ? 1 : null,
		    'reported_ic' => (Input::has('reported_ic')&&Input::get('reported_ic')=='1') ? 1 : 0,
			'in_service' 		=> mb_strtoupper(Input::get('in_service'), 'UTF-8'),
            'if_il_repair' 		=> mb_strtoupper(Input::get('if_il_repair'), 'UTF-8'),
            'il_repair_info' 		=> Input::get('il_repair_info'),
            'il_repair_info_description' => Input::get('il_repair_info_description'),
            'source'=>0,
			'is_cas_case' => (isCasActive()) ? 1 : 0
		));

		Histories::history($injury->id, 1, Auth::user()->id);

		if(!Input::has('dont_send_sms') || Input::get('dont_send_sms') != 1 ){
            if($contact_person == 1) {
                $driver = Drivers::find($driver_id);
                if ($driver_id != '' && $driver->phone != '') {
                    $phone_nb = trim($driver->phone);
                    $phone_nb = str_replace(' ', '', $phone_nb);

                    $msg = "Państwa zgłoszenie szkody do pojazdu " . $vehicle_new->registration . " zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy " . $injury->case_nr;

                    send_sms($phone_nb, $msg);

                    Histories::history($injury->id, 137, Auth::user()->id, $phone_nb);
                }
            }else{
                if (Input::get('notifier_phone') != '') {
                    $phone_nb = trim(Input::get('notifier_phone'));
                    $phone_nb = str_replace(' ', '', $phone_nb);

                    $msg = "Państwa zgłoszenie szkody do pojazdu " . $vehicle_new->registration . " zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy " . $injury->case_nr;

                    send_sms($phone_nb, $msg);

                    Histories::history($injury->id, 137, Auth::user()->id, $phone_nb);
                }
            }
		}

        //przypisanie pism do szkody
        if(Input::has('matchedLetters')) {
            foreach(Input::get('matchedLetters') as $letter_id) {
                $letter = InjuryLetter::find($letter_id);
                $file = InjuryFiles::create(array(
                    'injury_id' => $injury->id,

                    'type' => 2,
                    'category' => $letter->category,
                    'document_id' => $letter->category,
                    'document_type' => 'InjuryUploadedDocumentType',

                    'user_id' => Auth::id(),
                    'file' => $letter->file,
                    'name' => $letter->name
                ));

                Histories::history($injury->id, 158, Auth::id(), 'Kategoria ' . $file->document->name . ' - <a target="_blank" href="' . URL::route('routes.get', ['injuries', 'docs', 'downloadDoc',$file->id]) . '">pobierz</a>');

                if ($file->document_id == 3 || $file->document_id == 4) {
                    InjuryInvoices::create(array(
                            'initial_company_vat_check_id' => ($injury->branch && $injury->branch->company->companyVatCheck) ? $injury->branch->company->companyVatCheck->id : null,
                            'injury_id' => $file->injury_id,
                            'injury_files_id' => $file->id,
                            'invoicereceives_id' => $file->injury()->first()->invoicereceives_id,
                            'created_at' => $file->created_at,
                            'updated_at' => $file->updated_at
                        )
                    );
                }
                if ($file->document_id == 6 || $file->document_id == 37) {
                    InjuryCompensation::create(array(
                        'injury_id' => $file->injury_id,
                        'injury_files_id' => $file->id,
                        'user_id' => Auth::user()->id
                    ));
                }

                if($file->document_id  == 2)
                {
                    InjuryEstimate::create(array(
                        'injury_id' => $file->injury_id,
                        'injury_file_id'	=> $file->id,
                        'user_id' => Auth::user()->id
                    ));
                }

                $letter->injury_file_id = $file->id;
                $letter->save();
            }
        }
        $error=false;
        $set_branch=false;
        if($injury){

            //dodanie uszkodzień
            if(Input::has('uszkodzenia')){
                foreach(Input::get('uszkodzenia') as $k => $v){
                    if(Input::has('strona'.$v)){
                        foreach(Input::get('strona'.$v) as $k2 => $v2){
                            InjuryDamage::create(array(
                                'injury_id' => $injury->id,
                                'damage_id' => $v,
                                'param'		=> $v2
                            ));
                        }
                    }else{
                        InjuryDamage::create(array(
                            'injury_id' => $injury->id,
                            'damage_id' => $v,
                            'param'		=> 0
                        ));
                    }
                }
            }
            if(Input::has('branch_id')&&Input::get('branch_id')!=0){
                $set_branch=true;
                if( is_null($injury->prev_step) && $injury->step == 0 && $injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
                    $contract = $injury->vehicle->nr_contract;
                    $issuedate = $injury->date_event;
                    $issuenumber = $injury->case_nr;
                    $issuetype = 'B';
                    $username = substr(Auth::user()->login, 0, 10);

                    $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                    $owner_id = $injury->vehicle->owner_id;

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                    $xml = $webservice->getResponseXML();

                    if ($xml->Error->ErrorCde != 'ERR0000') {
                        if($xml->Error->ErrorCde == 'ERR0006'){
                            $ISSUENUMBER = $injury->case_nr;
                            $COMMENT = Input::get('content');
                            $USERNAME = substr(Auth::user()->login, 0, 10);
                            $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

                            $owner_id = $injury->vehicle->owner_id;

                            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');

                            $xml = $webservice->getResponseXML();
                            if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                if($xml->Error->ErrorCde ==  'ERR0014'){
                                    $data = new Idea\Structures\CHGISSUETYPEInput($ISSUENUMBER, 'B',$USERNAME);
                                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');
                                    $xml = $webservice->getResponseXML();
                                    if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                        $result['code'] = 2;
                                        $result['error'] = $xml->Error->ErrorDes->__toString();
                                        $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                        $error=true;
                                        //return json_encode($result);
                                    }
                                }else {
                                    $result['code'] = 2;
                                    $result['error'] = $xml->Error->ErrorDes->__toString();
                                    $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                    $error=true;
                                    //return json_encode($result);
                                }
                            }
                        }else{
                            $result['code'] = 2;
                            $result['error'] = $xml->Error->ErrorDes->__toString();
                            $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                            $error=true;
                            //return json_encode($result);
                        }
                    }
                }
                if(!$error){
                    $injury->branch_id = Input::get('branch_id');

                    $injury->step = 10;

                    Histories::history($injury->id, 31, Auth::user()->id);

                    if( !Input::has('branch_dont_send_sms') || Input::get('branch_dont_send_sms') != 1 ){
                        $branch = Branch::find($injury->branch_id);

                        if($branch->company->groups->contains(1) || $branch->company->groups->contains(5)) {
                            if ($injury->contact_person == 1) {
                                if ($injury->driver_id != '') {
                                    $driver = Drivers::find($injury->driver_id);
                                    $phone_nb = trim($driver->phone);
                                    $phone_nb = str_replace(' ', '', $phone_nb);
                                } else
                                    $phone_nb = '';
                            } else {
                                $phone_nb = trim($injury->notifier_phone);
                                $phone_nb = str_replace(' ', '', $phone_nb);
                            }

                            if ($phone_nb != '') {
                                $vehicle = $injury->vehicle;

                                $msg = "Informujemy, że likwidację szkody w pojezdzie " . $vehicle->registration . " wykona serwis: " . $branch->short_name . ", ulica " . $branch->street . ", " . $branch->city . ", tel. " . $branch->phone . ". CAS w imieniu ".$vehicle->owner->name;

                                send_sms($phone_nb, $msg);

                                Histories::history($injury->id, 138, Auth::user()->id, $phone_nb);
                            }
                        }
                    }elseif(Input::has('branch_dont_send_sms') && Input::get('branch_dont_send_sms') == 1){
                        Log::info('zrezygnowano z wysyłki sms dla sprawy id: '.$injury->id);
                    }


                    if( !$injury->save() ) {
                        $error=true;
                    }
                    else{
                        Log::info('przypisano warsztat przy towrzeniu zlecenia');
                    }
                }
            }
        }

        if( (Input::has('reported_ic') && Input::get('reported_ic')=='1') || ( $injury->injury_nr && $injury->injury_nr != '' ) ) {
            $injury->update(['injury_step_stage_id' => 2]);
            InjuryStepStageHistory::create([
                'injury_id' =>  $injury->id,
                'injury_step_stage_id' => 2
            ]);
        }else{
            $injury->update(['injury_step_stage_id' => 1]);
            InjuryStepStageHistory::create([
                'injury_id' =>  $injury->id,
                'injury_step_stage_id' => 1
            ]);
        }

        if(Input::get('insert_role') == 'adm'){
            if($injury){
                if($set_branch){
                    if(!$error){
                        return Redirect::route('injuries-info',array($injury->id));
                    }
                    else{
                        return Redirect::route('injuries-new')->withErrors('Zlecenia dodano poprawnie, ale wystąpił błąd w trakcie przypisywania warsztatu . Skontaktuj się z administratorem.');
                    }
                }
                else{
                    return Redirect::route('injuries-new');
                }
            }else{
                return Redirect::route('injuries-create')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }else{
            if($injury){
                if($set_branch){
                    if(!$error){
                        return Redirect::route('injuries-info',array($injury->id));
                    }
                    else{
                        return Redirect::route('home')->withErrors('Zlecenia dodano poprawnie, ale wystąpił błąd w trakcie przypisywania warsztatu . Skontaktuj się z administratorem.');
                    }
                }
                else{
                    return Redirect::route('home');
                }
            }else{
                return Redirect::route('injuries-create-i')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }

	}

    //dodawanie szkody mobilne
    public function postCreateMobile($id){

        if( Input::has('if_map') ) $if_map = 1; else $if_map = 0;
        if( Input::has('if_map_correct') ) $if_map_correct = 1; else $if_map_correct = 0;
        if( Input::has('contact_person') ) $contact_person = 2; else $contact_person = 1;
        if( Input::get('driver_id') != ''){
            $driver_id = Input::get('driver_id');
        }else{
            $driver = Drivers::create(array(
                'client_id' => Input::get('client_id'),
                'surname' => mb_strtoupper(Input::get('driver_surname'), 'UTF-8'),
                'name' => mb_strtoupper(Input::get('driver_name'), 'UTF-8'),
                'phone' => mb_strtoupper(Input::get('driver_phone'), 'UTF-8'),
                'city' => mb_strtoupper(Input::get('driver_city'), 'UTF-8'),
                'email' => Input::get('driver_email'),
            ));
            $driver_id = $driver->id;
        }

        if( Input::get('info') != ''){
            $insert = Text_contents::create(array(
                'content' => nl2br(Input::get('info'))
            ));

            $info_id = $insert->id;
        }else{
            $info_id = '0';
        }

        if( Input::get('remarks') != ''){
            $insert = Text_contents::create(array(
                'content' => nl2br(Input::get('remarks'))
            ));

            $remarks_id = $insert->id;
        }else{
            $remarks_id = '0';
        }

        if( Input::has('zdarzenie') && Input::get('zdarzenie') == 12 ) $if_theft = 1; else $if_theft = 0;

        if(Input::get('injuries_type') == '2' || Input::get('injuries_type') == '4' || Input::get('injuries_type') == '5'){
            $offender = Offenders::create(array(
                'surname'	=>	mb_strtoupper( Input::get('offender_surname'), 'UTF-8'),
                'name'		=>	mb_strtoupper( Input::get('offender_name'), 'UTF-8'),
                'post'		=>	mb_strtoupper( Input::get('offender_post'), 'UTF-8'),
                'city'		=>	mb_strtoupper( Input::get('offender_city'), 'UTF-8'),
                'street'	=>	mb_strtoupper( Input::get('offender_street'), 'UTF-8'),
                'registration'	=>	mb_strtoupper( Input::get('offender_registration'), 'UTF-8'),
                'car'		=>	mb_strtoupper( Input::get('offender_car'), 'UTF-8'),
                'oc_nr'		=>	mb_strtoupper( Input::get('offender_oc_nr'), 'UTF-8'),
                'zu'		=>	mb_strtoupper( Input::get('offender_zu'), 'UTF-8'),
                'expire'	=> 	mb_strtoupper( Input::get('offender_expire'), 'UTF-8'),
                'owner'		=>	mb_strtoupper( Input::get('offender_owner'), 'UTF-8'),
                'remarks'	=>	Input::get('offender_remarks')
            ));
            $id_offender = $offender->id;
        }else $id_offender = 0;

        if(Input::get('vehicle_type') == 'Vehicles') {
            $vehicle = Vehicles::find(Input::get('vehicle_id'));

            if (Input::get('vin_grp') == 0) {
                $new_vin = mb_strtoupper(Input::get('vin'), 'UTF-8');
            } else {
                $new_vin = $vehicle->VIN;
            }

            if (Input::get('brand_grp') == 0) {
                $new_brand = mb_strtoupper(Input::get('brand'), 'UTF-8');
            } else {
                $new_brand = $vehicle->brand;
            }

            if (Input::get('model_grp') == 0) {
                $new_model = mb_strtoupper(Input::get('model'), 'UTF-8');
            } else {
                $new_model = $vehicle->model;
            }

            if (Input::has('engine'))
                $new_engine = mb_strtoupper(Input::get('engine'), 'UTF-8');
            else
                $new_engine = $vehicle->engine;

            if (Input::get('year_production_grp') == 0) {
                $new_year_production = mb_strtoupper(Input::get('year_production'), 'UTF-8');
            } else {
                $new_year_production = $vehicle->year_production;
            }

            if (Input::has('first_registration'))
                $new_first_registration = mb_strtoupper(Input::get('first_registration'), 'UTF-8');
            else
                $new_first_registration = $vehicle->first_registration;

            $new_mileage = mb_strtoupper(Input::get('mileage'), 'UTF-8');

            if (Input::get('owner_grp') == 0) {
                $new_owner_id = Input::get('owner_id');
            } else {
                $new_owner_id = $vehicle->owner_id;
            }

            if (Input::get('client_grp') == 0) {
                $new_client_id = Input::get('client_id');
            } else {
                $new_client_id = $vehicle->client_id;
            }

            if (Input::get('end_leasing_grp') == 0) {
                $new_end_leasing = Input::get('end_leasing');
            } else {
                $new_end_leasing = $vehicle->end_leasing;
            }

            if (Input::get('contract_status_grp') == 0) {
                $new_contract_status = mb_strtoupper(Input::get('contract_status'), 'UTF-8');
            } else {
                $new_contract_status = $vehicle->contract_status;
            }

            if (Input::get('insurance_company_grp') == 0) {
                $new_insurance_company_id = Input::get('insurance_company_id');
            } else {
                $new_insurance_company_id = $vehicle->insurance_company_id;
            }

            $new_insurance_name = $vehicle->insurance_company_name;

            if (Input::get('expire_grp') == 0) {
                $new_expire = Input::get('expire');
            } else {
                $new_expire = $vehicle->expire;
            }

            if (Input::has('contribution'))
                $new_contribution = mb_strtoupper(Input::get('contribution'), 'UTF-8');
            else
                $new_contribution = $vehicle->contribution;

            if (Input::get('assistance_grp') == 0) {
                $new_assistance = mb_strtoupper(Input::get('assistance'), 'UTF-8');
            } else {
                $new_assistance = $vehicle->assistance;
            }

            if (Input::get('assistance_name_grp') == 0) {
                $new_assistance_name = mb_strtoupper(Input::get('assistance_name'), 'UTF-8');
            } else {
                $new_assistance_name = $vehicle->assistance_name;
            }

            if (Input::has('netto_brutto'))
                $new_netto_brutto = Input::get('netto_brutto');
            else
                $new_netto_brutto = $vehicle->netto_brutto;

            if (Input::has('insurance'))
                $new_insurance = mb_strtoupper(Input::get('insurance'), 'UTF-8');
            else
                $new_insurance = $vehicle->insurance;

            if (Input::get('nr_policy_grp') == 0) {
                $new_nr_policy = mb_strtoupper(Input::get('nr_policy'), 'UTF-8');
            } else {
                $new_nr_policy = $vehicle->nr_policy;
            }

            $vehicle_new = Vehicles::create(array(
                'owner_id' => $new_owner_id,
                'client_id' => $new_client_id,
                'parent_id' => Input::get('vehicle_id'),
                'registration' => mb_strtoupper(Input::get('registration'), 'UTF-8'),
                'VIN' => mb_strtoupper($new_vin, 'UTF-8'),
                'brand' => mb_strtoupper($new_brand, 'UTF-8'),
                'model' => mb_strtoupper($new_model, 'UTF-8'),
                'engine' => mb_strtoupper($new_engine, 'UTF-8'),
                'nr_contract' => mb_strtoupper(Input::get('nr_contract'), 'UTF-8'),
                'year_production' => $new_year_production,
                'first_registration' => $new_first_registration,
                'mileage' => $new_mileage,
                'expire' => $new_expire,
                'contribution' => mb_strtoupper($new_contribution, 'UTF-8'),
                'assistance' => mb_strtoupper($new_assistance, 'UTF-8'),
                'assistance_name' => mb_strtoupper($new_assistance_name, 'UTF-8'),
                'insurance' => mb_strtoupper($new_insurance, 'UTF-8'),
                'nr_policy' => mb_strtoupper($new_nr_policy, 'UTF-8'),
                'contract_status' => mb_strtoupper($new_contract_status, 'UTF-8'),
                'insurance_company_id' => $new_insurance_company_id,
                'insurance_company_name' => mb_strtoupper($new_insurance_name, 'UTF-8'),
                'policy_insurance_company_id' => $new_insurance_company_id,
                'end_leasing' => $new_end_leasing,
                'netto_brutto' => $new_netto_brutto,
                'gap' => $vehicle->gap,
                'legal_protection' => $vehicle->legal_protection,
                'cfm' => Input::get('cfm'),
                'register_as' => Input::get('register_as')
            ));
            $vehicle_type = 'Vehicles';
        }else{
            $vehicle = VmanageVehicle::find(Input::get('vehicle_id'));

            $vehicle_new = VmanageVehicle::create($vehicle->toArray());
            $vehicle_new->update(Input::all());

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

            $vehicle_type = 'VmanageVehicle';
        }

        if( isset($new_contract_status)
            &&
            ! str_contains(mb_strtoupper($new_contract_status, 'UTF-8'), 'AKTYWNA')
        ) {
            $locked_status = 5;
        }else {
            $locked_status = 0;
        }

	    $last_injury = Injury::orderBy('id', 'desc')->limit('1')->get();
	    if( isCasActive() ) {
            if (!$last_injury->isEmpty()) {
                $case_nr = $last_injury->first()->case_nr;
                if (strpos($case_nr, 'C') !== false) {
                    $case_nr = substr($case_nr, 0, -2);
                }

                if (substr($case_nr, -4) == date('Y')) {
                    $case_nr = intval(substr($case_nr, 0, -5));
                    $case_nr++;
                    $case_nr .= '/' . date('Y').'/C';
                } else {
                    $case_nr = '1/' . date('Y').'/C';
                }
            } else {
                $case_nr = '1/' . date('Y').'/C';
            }
	    }else{
		    if (!$last_injury->isEmpty()) {
			    $case_nr = $last_injury->first()->case_nr;
			    if (substr($case_nr, -4) == date('Y')) {
				    $case_nr = intval(substr($case_nr, 0, -5));
				    $case_nr++;
				    $case_nr .= '/' . date('Y');
			    } else {
				    $case_nr = '1/' . date('Y');
			    }
		    } else {
			    $case_nr = '1/' . date('Y');
		    }
	    }

        $mobile_injury = MobileInjury::find($id);

        $injury = Injury::create(array(
            'user_id' 		=> Auth::user()->id,
            'vehicle_id' 	=> $vehicle_new->id,
            'vehicle_type'  => $vehicle_type,
            'client_id' 	=> Input::get('client_id'),
            'driver_id' 	=> $driver_id,
            'notifier_surname' 	=> mb_strtoupper(Input::get('notifier_surname'), 'UTF-8'),
            'notifier_name' 	=> mb_strtoupper(Input::get('notifier_name'), 'UTF-8'),
            'notifier_phone' 	=> mb_strtoupper(Input::get('notifier_phone'), 'UTF-8'),
            'notifier_email' 	=> Input::get('notifier_email'),
            'injuries_type_id' 	=> Input::get('injuries_type'),
            'offender_id'		=>	$id_offender,
            'info' 			=> $info_id,
            'remarks' 		=> $remarks_id,
            'police' 		=> mb_strtoupper(Input::get('police'), 'UTF-8'),
            'police_nr' 	=> mb_strtoupper(Input::get('police_nr'), 'UTF-8'),
            'police_unit'	=> mb_strtoupper(Input::get('police_unit'), 'UTF-8'),
            'police_contact'=> mb_strtoupper(Input::get('police_contact'), 'UTF-8'),
            'injury_nr'		=> mb_strtoupper(Input::get('injury_nr'), 'UTF-8'),
            'case_nr'		=> $case_nr,
            'date_event' 	=> Input::get('date_event'),
            'time_event' => Input::get('time_event'),
            //'event_post' 	=> Input::get('event_post'),
            'event_city' 	=> mb_strtoupper(Input::get('event_city'), 'UTF-8'),
            'event_street' 	=> mb_strtoupper(Input::get('event_street'), 'UTF-8'),
            'if_map' 		=> $if_map,
            'if_map_correct' 	=> $if_map_correct,
            'lat' 			=> Input::get('lat'),
            'lng' 			=> Input::get('lng'),
            'receive_id' 	=> Input::get('receives'),
            'invoicereceives_id' 	=> Input::get('invoicereceives'),
            'type_incident_id'	=> Input::get('zdarzenie'),
            'contact_person'    => $contact_person,
            'if_statement'	=> Input::get('if_statement'),
            'if_registration_book'	=> Input::get('if_registration_book'),
            'if_towing'		=> Input::get('if_towing'),
            'if_courtesy_car'	=> Input::get('if_courtesy_car'),
            'if_door2door'	=> Input::get('if_door2door'),
            'if_theft'		=> $if_theft,
            'locked_status'	=> $locked_status,
            'way_of'        => ($mobile_injury->source == 0)? 3 : 4,
            'settlement_cost_estimate' => (Input::has('settlement_cost_estimate')) ? 1 : 0,
            'if_vip'            => (Input::has('if_vip') ) ? 1 : null,
            'reported_ic' => (Input::has('reported_ic')&&Input::get('reported_ic')=='1') ? 1 : 0,
            'in_service' 		=> mb_strtoupper(Input::get('in_service'), 'UTF-8'),
            'if_il_repair' 		=> mb_strtoupper(Input::get('if_il_repair'), 'UTF-8'),
            'il_repair_info' 		=> Input::get('il_repair_info'),
            'il_repair_info_description' => Input::get('il_repair_info_description'),
            'source'=>2,
	        'is_cas_case' => (isCasActive()) ? 1 : 0
        ));

        //$injury->created_at = $mobile_injury->created_at;
        $injury->save();

        Histories::history($injury->id, 1, Auth::user()->id);

//					\Log::info('a');

        if(!Input::has('dont_send_sms') || Input::get('dont_send_sms') != 1 ){
            if ($injury->contact_person == 1) {
                if ($injury->driver_id != '') {
                    $driver = Drivers::find($injury->driver_id);
                    $phone_nb = trim($driver->phone);
                    $phone_nb = str_replace(' ', '', $phone_nb);
                } else
                    $phone_nb = '';
            } else {
                $phone_nb = trim($injury->notifier_phone);
                $phone_nb = str_replace(' ', '', $phone_nb);
            }

            if($phone_nb != ''){
                $msg = "Państwa zgłoszenie szkody do pojazdu ".$vehicle_new->registration." zostało zarejestrowane w systemie Centrum Asysty Szkodowej. Nr sprawy ".$injury->case_nr;

                send_sms($phone_nb, $msg);

                Histories::history($injury->id, 137, Auth::user()->id, $phone_nb);
            }
        }

        $damage = Damage_type::all();

        if(Input::has('uszkodzenia')){
            foreach(Input::get('uszkodzenia') as $k => $v){
                if(Input::has('strona'.$v)){
                    foreach(Input::get('strona'.$v) as $k2 => $v2){
                        InjuryDamage::create(array(
                            'injury_id' => $injury->id,
                            'damage_id' => $v,
                            'param'		=> $v2
                        ));
                    }
                }else{
                    if( $damage->find($v)->param == 0){
                        InjuryDamage::create(array(
                            'injury_id' => $injury->id,
                            'damage_id' => $v,
                            'param'		=> 0
                        ));
                    }
                }

            }
        }

        if(Input::has('pictures')){
            foreach(Input::get('pictures') as $k => $v){
                $picture = MobileInjuryFile::find($v);

                $image =InjuryFiles::create(array(
                    'injury_id' => $injury->id,
                    'type'		=> 1,
                    'category'	=> 1,
                    'user_id'	=> Auth::user()->id,
                    'file'		=> $picture->file,
                ));

                $path       = '/images/full';
                $path_min       = '/images/min';
                $path_thumb       = '/images/thumb';

                $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/mobile'.$path.'/'.$picture->file);
                $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$picture->file);

                $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/mobile'.$path_min.'/'.$picture->file);
                $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_min.'/'.$picture->file);

                $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/mobile'.$path_thumb.'/'.$picture->file);
                $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$picture->file);
            }
        }

        $mobile_injury = MobileInjury::find($id);
        $mobile_injury->active = '-1';
        $mobile_injury->injury_id = $injury->id;
        $mobile_injury->save();

        //przypisanie pism do szkody
        if(Input::has('matchedLetters')) {
            foreach(Input::get('matchedLetters') as $letter_id) {
                $letter = InjuryLetter::find($letter_id);
                $file = InjuryFiles::create(array(
                    'injury_id' => $injury->id,

                    'type' => 2,
                    'category' => $letter->category,
                    'document_id' => $letter->category,
                    'document_type' =>  'InjuryUploadedDocumentType',

                    'user_id' => Auth::id(),
                    'file' => $letter->file,
                    'name' => $letter->name
                ));

                Histories::history($injury->id, 158, Auth::id(), 'Kategoria ' . $file->document->name . ' - <a target="_blank" href="' . URL::route('injuries-downloadDoc', array($file->id)) . '">pobierz</a>');

                if ($file->category == 3 || $file->category == 4) {
                    InjuryInvoices::create(array(
                            'initial_company_vat_check_id' => ($injury->branch && $injury->branch->company->companyVatCheck) ? $injury->branch->company->companyVatCheck->id : null,
                            'injury_id' => $file->injury_id,
                            'injury_files_id' => $file->id,
                            'invoicereceives_id' => $file->injury()->first()->invoicereceives_id,
                            'created_at' => $file->created_at,
                            'updated_at' => $file->updated_at
                        )
                    );
                }
                if ($file->category == 6) {
                    InjuryCompensation::create(array(
                        'injury_id' => $file->injury_id,
                        'injury_files_id' => $file->id,
                        'user_id' => Auth::user()->id
                    ));
                }

                $letter->injury_file_id = $file->id;
                $letter->save();
            }
        }

        $error=false;
        $set_branch=false;
        if($injury){
            //dodanie uszkodzień
            if(Input::has('branch_id')&&Input::get('branch_id')!=0){
                $set_branch=true;
                if( is_null($injury->prev_step) && $injury->step == 0 && $vehicle_new->owner->wsdl != '' && $vehicle_new->register_as == 1) {
                    $contract = $vehicle_new->nr_contract;
                    $issuedate = $injury->date_event;
                    $issuenumber = $injury->case_nr;
                    $issuetype = 'B';
                    $username = substr(Auth::user()->login, 0, 10);

                    $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                    $owner_id = $vehicle_new->owner_id;

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                    $xml = $webservice->getResponseXML();

                    if ($xml->Error->ErrorCde != 'ERR0000') {
                        if($xml->Error->ErrorCde == 'ERR0006'){
                            $ISSUENUMBER = $injury->case_nr;
                            $COMMENT = Input::get('content');
                            $USERNAME = substr(Auth::user()->login, 0, 10);
                            $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

                            $owner_id = $injury->vehicle->owner_id;

                            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');

                            $xml = $webservice->getResponseXML();
                            if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                if($xml->Error->ErrorCde ==  'ERR0014'){
                                    $data = new Idea\Structures\CHGISSUETYPEInput($ISSUENUMBER, 'B',$USERNAME);
                                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');
                                    $xml = $webservice->getResponseXML();
                                    if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                        $result['code'] = 2;
                                        $result['error'] = $xml->Error->ErrorDes->__toString();
                                        $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                        $error=true;
                                        //return json_encode($result);
                                    }
                                }else {
                                    $result['code'] = 2;
                                    $result['error'] = $xml->Error->ErrorDes->__toString();
                                    $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                    $error=true;
                                    //return json_encode($result);
                                }
                            }
                        }else{
                            $result['code'] = 2;
                            $result['error'] = $xml->Error->ErrorDes->__toString();
                            $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                            $error=true;
                            //return json_encode($result);
                        }
                    }
                }
                if(!$error){
                    $injury->branch_id = Input::get('branch_id');

                    $injury->step = 10;

                    Histories::history($injury->id, 31, Auth::user()->id);

                    if( !Input::has('branch_dont_send_sms') || Input::get('branch_dont_send_sms') != 1 ){
                        $branch = Branch::find($injury->branch_id);

                        if($branch->company->groups->contains(1) || $branch->company->groups->contains(5)) {
                            if ($injury->contact_person == 1) {
                                if ($injury->driver_id != '') {
                                    $driver = Drivers::find($injury->driver_id);
                                    $phone_nb = trim($driver->phone);
                                    $phone_nb = str_replace(' ', '', $phone_nb);
                                } else
                                    $phone_nb = '';
                            } else {
                                $phone_nb = trim($injury->notifier_phone);
                                $phone_nb = str_replace(' ', '', $phone_nb);
                            }

                            if ($phone_nb != '') {
                                $vehicle = $vehicle_new;
                                $branch = Branch::find($injury->branch_id);

                                $msg = "Informujemy, że likwidację szkody w pojezdzie " . $vehicle->registration . " wykona serwis: " . $branch->short_name . ", ulica " . $branch->street . ", " . $branch->city . ", tel. " . $branch->phone . ". CAS w imieniu ".$vehicle->owner->name;

                                send_sms($phone_nb, $msg);

                                Histories::history($injury->id, 138, Auth::user()->id, $phone_nb);
                            }
                        }
                    }elseif(Input::has('branch_dont_send_sms')||Input::get('branch_dont_send_sms') == 1){
                        Log::info('zrezygnowano z wysyłki sms dla sprawy id: '.$injury->id);
                    }


                    if( !$injury->save() ) {
                        $error=true;
                        \Log::info('b');
                    }
                    else{
                        Log::info('przypisano warsztat przy towrzeniu zlecenia');
                    }
                }
            }
        }

        if( (Input::has('reported_ic') && Input::get('reported_ic')=='1') || ( $injury->injury_nr && $injury->injury_nr != '' ) ) {
            $injury->update(['injury_step_stage_id' => 2]);
            InjuryStepStageHistory::create([
                'injury_id' =>  $injury->id,
                'injury_step_stage_id' => 2
            ]);
        }else{
            $injury->update(['injury_step_stage_id' => 1]);
            InjuryStepStageHistory::create([
                'injury_id' =>  $injury->id,
                'injury_step_stage_id' => 1
            ]);
        }

        if(Input::get('insert_role') == 'adm'){
            if($injury){
                if($set_branch){
                    if(!$error){
                        return Redirect::route('injuries-info',array($injury->id));
                    }
                    else{
                        return Redirect::route('injuries-new')->withErrors('Zlecenia dodano poprawnie, ale wystąpił błąd w trakcie przypisywania warsztatu . Skontaktuj się z administratorem.');
                    }
                }
                else{
                    return Redirect::route('injuries-new');
                }
            }else{
                return Redirect::route('injuries-create')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }else{
            if($injury){
                if($set_branch){
                    if(!$error){
                        return Redirect::route('injuries-info',array($injury->id));
                    }
                    else{
                        return Redirect::route('home')->withErrors('Zlecenia dodano poprawnie, ale wystąpił błąd w trakcie przypisywania warsztatu . Skontaktuj się z administratorem.');
                    }
                }
                else{
                    return Redirect::route('home');
                }
            }else{
                return Redirect::route('injuries-create-i')->withErrors('Wystąpił błąd w trakcie wprowadzania zlecenia. Skontaktuj się z administratorem.');
            }
        }

    }

	//get lists vehicles
	public function getVehicleRegistrationIsdlList(){

		$registration = Input::get('registration');
		$nr_contract = Input::get('nr_contract');

		$searcher = new \Idea\Searcher\Searcher($registration, $nr_contract);

        if(Input::has('registration')||Input::has('nr_contract')) {
            $vmanageVehicle = $searcher->searchVmanageVehicle();
            if (count($vmanageVehicle) > 0)
                return json_encode($vmanageVehicle);
        }

        $nonAssVehicle = $searcher->searchNonAsVehicle($registration, $nr_contract);
        if (count($nonAssVehicle) > 0)
            return json_encode($nonAssVehicle);

        $assVehicles = $searcher->searchASSVehicle($registration, $nr_contract);
		return json_encode($assVehicles);
	}

	public function getVehicleRegistrationList()
	{
		$term = Input::get('term');

        $vehicles = Vehicles::select('id', 'registration', 'brand', 'model', 'VIN', 'nr_contract', 'engine', 'expire', 'client_id', 'year_production',
        	'first_registration', 'mileage', 'owner_id', 'end_leasing', 'insurance_company_id', 'contribution', 'assistance', 'assistance_name', 'insurance',
        	'nr_policy')
        	->where('registration', 'like', '%'.$term.'%')->groupBy('registration')->orderBy('parent_id', 'desc')->get();

        $result = array();
        foreach($vehicles as $k => $v){

        	$result[] = array(
        		"id"=>$v->id,
        		"label"=>$v->registration.' '.$v->brand.' - '.$v->model,
        		"value" => $v->registration
        	);
        }

        return json_encode($result);

	}


	public function getVehicleContractList()
	{
		$term = Input::get('term');

        $vehicles = Vehicles::select('id', 'registration', 'brand', 'model', 'VIN', 'nr_contract', 'engine', 'expire', 'client_id', 'year_production',
        	'first_registration', 'mileage', 'owner_id', 'end_leasing', 'insurance_company_id', 'contribution', 'assistance', 'assistance_name', 'insurance',
        	'nr_policy')
        	->where('nr_contract', 'like', '%'.$term.'%')->groupBy('registration')->orderBy('parent_id', 'desc')->get();



        $result = array();
        foreach($vehicles as $k => $v){
        	$result[] = array(
        		"id"=>$v->id,
        		"label"=>$v->registration.' '.$v->brand.' - '.$v->model,
        		"value" => $v->nr_contract
        	);
        }

        return json_encode($result);
	}

	public function getVehicleCheckInjuries(){
        \Debugbar::disable();
        $result = array();

        if(Input::has('vehicle_id') && Input::get('vehicle_id') != '') {
            $data = Input::all();
            $vehicle = $data['vehicle_type']::find(Input::get('vehicle_id'));
            if($vehicle->registration != '') {
                $vehiclesVmanageA = VmanageVehicle::whereRegistration($vehicle->registration)->where(function($query){
						$query->whereHas('company', function($query){
                               $query->whereHas('guardians', function($query){
                                   $query->where('users.id', Auth::user()->id);
                               });
                           });
					})->withTrashed()->lists('id');
                $vehiclesA = Vehicles::whereRegistration($vehicle->registration)->lists('id');
            }else {
                $vehiclesVmanageA = VmanageVehicle::where('nr_contract', $vehicle->nr_contract)->where(function($query){
						$query->whereHas('company', function($query){
                               $query->whereHas('guardians', function($query){
                                   $query->where('users.id', Auth::user()->id);
                               });
                           });
					})->withTrashed()->lists('id');
                $vehiclesA = Vehicles::where('nr_contract', $vehicle->nr_contract)->lists('id');
            }


            $injuries = Injury::where(function($query) use($vehiclesVmanageA, $vehiclesA){
                            $query->where(function($query) use($vehiclesVmanageA){
                                $query->where('vehicle_type', 'VmanageVehicle')->whereIn('vehicle_id', $vehiclesVmanageA);
                            })->orWhere(function($query) use($vehiclesA){
                                $query->where('vehicle_type', 'Vehicles')->whereIn('vehicle_id', $vehiclesA);
                            });
                        })->where('active', '=', 0)->with('getInfo', 'status')->get();

            $temp_i = $injuries->toArray();
            if (!is_null($injuries) && !empty($temp_i)) {
                $result['exists'] = 1;
                $result['dataHtml'] = '
				<table class="table table-hover table-condensed">
					<thead>
						<th>data zgłoszenia</th>
						<th>osoba zgłaszająca</th>
						<th>miejsce zdarzenia</th>
						<th>nr sprawy</th>
						<th>nr szkodu (ZU)</th>
						<th>opis zdarzenia</th>
						<th>status</th>
					</thead>';
                foreach ($injuries as $k => $injury) {
                    $result['dataHtml'] .= '
					<tr class="vertical-middle">
						<td>
							' . substr($injury->created_at, 0, -3) . '
						</td>
						<td>
							' . $injury->notifier_surname . ' ' . $injury->notifier_name . '<br>
							tel:' . $injury->notifier_phone . ' email:' . $injury->notifier_email . '
						</td>
						<td>
							' . $injury->event_city . ' ' . $injury->event_street . '
							<br>
							' . $injury->date_event . '
						</td>
						<td>';
                    $result['dataHtml'] .= ' <a type="button" class="btn btn-link" target="_blank" href="' . URL::route('injuries-info', array($injury->id)) . '" >';

                    $result['dataHtml'] .= $injury->case_nr;
                    $result['dataHtml'] .= '</a>';
                    $result['dataHtml'] .= '
						</td>
						<td>
							' . (($injury->injury_nr == '') ? '---' : $injury->injury_nr) . '
						</td>
						<td>
							' . (($injury->info != 0 && !is_null($injury->info) ) ? $injury->getInfo->content : '---') . '
						</td>
						<td>';
                    $result['dataHtml'] .= $injury->status->name;
                    $result['dataHtml'] .= '
						</td>
					</tr>';
                }
                $result['dataHtml'] .= '</table>';
                $result['count'] = $injuries->count();
            } else {
                $result['exists'] = 0;
            }
        }else{
            $result['exists'] = 0;
        }
		return json_encode($result);
	}

	// get list of avaible drivers
	public function getDriversList()
	{
		$term = Input::get('term');
		$this->term = $term;

        $result = array();

        if(Input::has('id_client') && Input::get('id_client') != '') {
            $clients = DB::select(DB::raw('
				SELECT T2.id
				FROM (
				    SELECT
				        @r AS _id,
				        (SELECT @r := parent_id FROM clients WHERE id = _id) AS parent_id,
				        @l := @l + 1 AS lvl
				    FROM
				        (SELECT @r := ' . Input::get('id_client') . ', @l := 0) vars,
				        vehicles h
				    WHERE @r <> 0) T1
				JOIN clients T2
				ON T1._id = T2.id
				ORDER BY T1.lvl DESC
			'));


            $clientsA = array_map(
                function ($oObject) {
                    $aConverted = get_object_vars($oObject);
                    return $aConverted['id'];
                },
                $clients);


            $drivers = Drivers::distinct()->where(function ($query) {
                $query->where('surname', 'like', '%' . $this->term . '%')->orWhere('name', 'like', '%' . $this->term . '%');
            })->whereIn('client_id', $clientsA)->get();


            foreach ($drivers as $k => $v) {
                $result[] = array(
                    "id" => $v->id,
                    "label" => $v->surname . ' ' . $v->name . ' - ' . $v->phone,
                    "value" => (Input::get('ele') == 'driver_surname' || Input::get('ele') == 'notifier_surname') ? $v->surname : $v->name,
                    "name" => $v->name,
                    "surname" => $v->surname,
                    'phone' => $v->phone,
                    'email' => $v->email,
                    'city' => $v->city,
                );
            }
        }

        if(Input::get('vehicle_type') == 'VmanageVehicle'){
            $vehicle = VmanageVehicle::with('user')->find(Input::get('vehicle_id'));
            $user = $vehicle->user;

            $existInResults = false;
            if(count($result) > 0 && $user) {
                foreach ($result as $driver) {
                    if ($driver['name'] == $user->name && $driver['surname'] == $user->surname)
                        $existInResults = true;
                }
            }

            if(!$existInResults && $user) {
                $result[] = array(
                    "id" => '',
                    "label" => $user->surname . ' ' . $user->name . ' - ' . $user->phone,
                    "value" => (Input::get('ele') == 'driver_surname' || Input::get('ele') == 'notifier_surname') ? $user->surname : $user->name,
                    "name" => $user->name,
                    "surname" => $user->surname,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'city' => '',
                );
            }
        }

        return json_encode($result);
	}

	public function setWithoutCompany($id)
	{
		$injury = Injury::find($id);

        if($injury->step == 0 && $injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {

            $contract = $injury->vehicle->nr_contract;
            $issuedate = $injury->date_event;
            $issuenumber = $injury->case_nr;
            $issuetype = 'B';
            $username = substr(Auth::user()->login, 0, 10);

            $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

            $owner_id = $injury->vehicle->owner_id;

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

            $xml = $webservice->getResponseXML();

            if ($xml->Error->ErrorCde != 'ERR0000') {
                if($xml->Error->ErrorCde != 'ERR0006' || ($xml->Error->ErrorCde == 'ERR0006' && is_null($injury->prev_step) ) ) {
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    return json_encode($result);
                }
            }

        }

        $injury->branch_id = '-1';
        $injury->step = 10;

        Histories::history($id, 131, Auth::user()->id);

        if( $injury->save() ) {

            Session::put('last_injury', $id);
            Session::put('last_injury_case_nr', $injury->case_nr);

            $result['code'] = 1;
            $result['url'] = URL::route('injuries-info', array($injury->id));
            return json_encode($result);
        }

	}

	public function setBranchOriginal($id)
	{
		$injury = Injury::find($id);
        $branch_prev =  $injury->originalBranch()->first();
		$injury->original_branch_id = Input::get('id_warsztat');

        $msg = ($branch_prev ? $branch_prev->short_name : '').' -> '.$injury->originalBranch()->first()->short_name;

		Histories::history($id, 31, Auth::user()->id, '-1', $msg);

		if( $injury->save() ) echo 0;
	}

	public function returnBranch($id)
	{
		$injury = Injury::find($id);

		$branch_prev =  $injury->branch()->first();
        $branch = Branch::find($injury->original_branch_id);

        $injury->branch_id = $injury->original_branch_id;
		$injury->original_branch_id = null;
        $msg = ($branch_prev ? $branch_prev->short_name : '').' -> '.$branch->short_name;
		Histories::history($id, 31, Auth::user()->id, '-1', $msg);
		$injury->save();

        foreach($injury->invoices as $invoice)
        {
            if($invoice->relatedCommission)
                $invoice->relatedCommission->update(['company_id' => $branch->company_id]);
        }

		$result['code'] = 0;
		return json_encode($result);
	}

	public function deleteBranch($id)
	{
		$injury = Injury::find($id);
        $branch_prev =  $injury->branch()->first();
		$injury->branch_id = -1;
        $msg = ($branch_prev ? $branch_prev->short_name : '').' -> ';
		Histories::history($id, 31, Auth::user()->id, '-1', $msg);
		$injury->save();

		foreach($injury->invoices as $invoice)
        {
            if($invoice->relatedCommission)
                $invoice->relatedCommission->update(['company_id' => null]);
        }

        if($injury->sap)
        {
            $sap = new Idea\SapService\Sap();
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($injury->id, 217, Auth::user()->id);
            }
        }

        $result = [];
		$result['code'] = 0;
		return json_encode($result);
	}

	public function setBranch($id)
	{
		$injury = Injury::find($id);

        $branch_prev = $injury->branch()->first();

		$injury->branch_id = Input::get('id_warsztat');

        $branch = Branch::with('company', 'company.groups')->find($injury->branch_id);

        $msg = ($branch_prev ? $branch_prev->short_name : '').' -> '.$branch->short_name;
        Histories::history($id, 31, Auth::user()->id, '-1', $msg);


        if(!Input::has('dont_send_sms') || Input::get('dont_send_sms') != 1 ){
            if($branch->company->groups->contains(1) || $branch->company->groups->contains(5)) {
                if ($injury->contact_person == 1) {
                    if ($injury->driver_id != '') {
                        $driver = Drivers::find($injury->driver_id);
                        $phone_nb = trim($driver->phone);
                        $phone_nb = str_replace(' ', '', $phone_nb);
                    } else
                        $phone_nb = '';
                } else {
                    $phone_nb = trim($injury->notifier_phone);
                    $phone_nb = str_replace(' ', '', $phone_nb);
                }

                if ($phone_nb != '') {
                    $vehicle = $injury->vehicle;
                    $branch = Branch::find($injury->branch_id);

                    $msg = "Informujemy, że likwidację szkody w pojezdzie " . $vehicle->registration . " wykona serwis: " . $branch->short_name . ", ulica " . $branch->street . ", " . $branch->city . ", tel. " . $branch->phone . ". CAS w imieniu ".$vehicle->owner->name;

                    send_sms($phone_nb, $msg);

                    Histories::history($injury->id, 138, Auth::user()->id, $phone_nb);
                }
            }
        }

        if($injury->step == 11 || $injury->step == 10) {
            if ( ($branch->company->groups->contains(1) || ($branch->company->groups->contains(5) && $injury->vehicle->cfm == 1)) && $injury->edb()->count() > 0) {
                $injury->step = 11;
            } else {
                $injury->step = 10;
            }
        }

        foreach($injury->invoices as $invoice)
        {
            if($invoice->relatedCommission)
                $invoice->relatedCommission->update(['company_id' => $branch->company_id]);
        }
        $injury->save();
        if($injury->sap)
        {
            $sap = new Idea\SapService\Sap();
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($injury->id, 217, Auth::user()->id);
            }
        }

		 echo 0;
	}


    public function getBranchesList($id=null, $owner_id=null){

        $injury = Injury::find($id);
        if($id==null)
            $owner_id = $injury->vehicle->owner_id;
        else
            $owner_id = $owner_id;

        $start['lat'] = Input::get('slat');
        $start['lng'] = Input::get('slng');

        if(  is_numeric(Input::get('promien')) ) {
            $promien = Input::get('promien');
        } else {
            $promien = 30;
        }

        $start['lat_r'] = $promien / 111.0;
        $start['lng_r'] = $promien / (111.0 * cos($start['lat'] * pi() / 180));


        if( $start['lat'] >= 0 ) {
            $start['lat_lewy'] = $start['lat'] - $start['lat_r'];
            $start['lat_prawy'] = $start['lat'] + $start['lat_r'];
        } else {
            $start['lat_lewy'] = $start['lat'] + $start['lat_r'];
            $start['lat_prawy'] = $start['lat'] - $start['lat_r'];
        }
        if( $start['lng'] >= 0 ) {
            $start['lng_lewy'] = $start['lng'] - $start['lng_r'];
            $start['lng_prawy'] = $start['lng'] + $start['lng_r'];
        } else {
            $start['lng_lewy'] = $start['lng'] + $start['lng_r'];
            $start['lng_prawy'] = $start['lng'] - $start['lng_r'];
        }

        $json = array();
        if(Input::get('type') == 1){
            $branches = Branch::with('typevehicles', 'brand')->distinct()->where('lat', '>', $start['lat_lewy'])->where('lat', '<', $start['lat_prawy'])
                ->where('lng', '>', $start['lng_lewy'])->where('lng', '<', $start['lng_prawy'])
                ->whereHas('company', function($query) use($owner_id){
                    $query->whereHas('groups', function($query) use($owner_id){
                        $query->whereHas('owners', function($query) use($owner_id){
                            $query->where('owner_id', $owner_id);
                        });
                    });
                })
                ->get();
        }else{
            $branches = Branch::with('typevehicles', 'brand')->distinct()->has('company')->where('lat', '>', $start['lat_lewy'])->where('lat', '<', $start['lat_prawy'])
                ->where('lng', '>', $start['lng_lewy'])->where('lng', '<', $start['lng_prawy'])
                ->get();
        }
        $typevehicles = Typevehicles::all();

        foreach ($branches as $k => $branch){
            if(Input::get("onBrand") == 1){
                $pass = 0;

                foreach ($branch->brand as $key => $value) {
                    if( mb_strtoupper($value->name, 'UTF-8') == mb_strtoupper($injury->vehicle->brand) ) $pass=1;
                }

            }else{
                $pass = 1;
            }
            if($pass == 1){
                if($injury->if_courtesy_car == 1){
                    $cars = '';
                    if($branch->typevehicles){
                        foreach ($branch->typevehicles as $k => $v) {
                            $cars.= ' klasy '.$typevehicles->find($v->typevehicles_id)->name.' - '.$v->value.';';
                        }
                    }else{
                        $cars = 'warsztat nie posiada aut zastępczych';
                    }
                }else
                    $cars = '-1';

                if($injury->if_towing == 1){
                    $tug = $branch->tug;
                    $tug24 = $branch->tug24;
                }else{
                    $tug = '-1';
                    $tug24 = '-1';
                }
                $data = '<span class="bt_com_search" id="'.$branch->id.'" id_marker="'.$k.'"><h6>'.$branch->company()->first()->name.' - '.$branch->short_name.'</h6></span>
							<div>
							<h6>'.$branch->company()->first()->name.' - '.$branch->short_name.'</h6>
							<h7>Adres: </h7>'.$branch->code.' '.$branch->city.', '.$branch->street.'<br>
							<h7>Telefon: </h7>'.$branch->phone.'<br><h7>Email: </h7>'.$branch->email;
                if($cars != '-1')
                    $data .= '<br><h7>Auta zastępcze:</h7>'.$cars;

                if($tug!= '-1')
                {
                    $data .= '<br><h7>Holownik:</h7>';
                    if($tug == 1)
                        $data .= ' na stanie';
                    else
                        $data .= ' brak na stanie';
                    if($tug24 == 1)
                        $data .= ' 24h';
                }

                $data .= '</div>';

                $json[] = array(
                    'nazwa'		=> $branch->company()->first()->name.' - '.$branch->short_name,
                    'kod' 		=> $branch->code,
                    'miasto' 	=> $branch->city,
                    'ulica'		=> $branch->street,
                    'id' 		=> $branch->id,
                    'lat'		=> $branch->lat,
                    'lng'		=> $branch->lng,
                    'telefon'	=> $branch->phone,
                    'email'		=> $branch->email,
                    'cars'		=> $cars,
                    'tug'		=> $tug,
                    'tug24'		=> $tug24,
                    'dataText'		=> $data
                );
            }

        }
        return json_encode($json);
    }

    public function getBranchesNameList($id=null,$spec=null, $active_vat_only=null, $invoice_id=null){

        $injury = Injury::find($id);

        $json = array();

        $query = Branch::select('branches.*')->with('company')->leftJoin('companies', function($join){
            $join->on('branches.company_id', '=', 'companies.id');
        })->distinct()->where('branches.active', '=', '0')->where('companies.active', '=', '0');


        if($spec){
            $query->where('companies.nip', 'like', "%".Input::get('term')."%");
        }else{
            $query->where(function($query){
                $query->where('companies.name', 'like', "%".Input::get('term')."%")->orWhere('branches.short_name', 'like', "%".Input::get('term')."%");
            });
        }

        if($active_vat_only){
            $query->where('companies.is_active_vat', 1);
        }

        $branches = $query->with('typevehicles')->get(array('*', 'branches.id as branch_id'));

        $typevehicles = Typevehicles::all();

        foreach ($branches as $k => $branch){
            if($injury!=null&&$injury->if_courtesy_car == 1){
                $cars = '';
                if($branch->typevehicles){
                    foreach ($branch->typevehicles as $k => $v) {
                        $cars.= ' klasy '.$typevehicles->find($v->typevehicles_id)->name.' - '.$v->value.';';
                    }
                }else{
                    $cars = 'warsztat nie posiada aut zastępczych';
                }
            }else
                $cars = '-1';

            if($injury!=null&&$injury->if_towing == 1){
                $tug = $branch->tug;
                $tug24 = $branch->tug24;
            }else{
                $tug = '-1';
                $tug24 = '-1';
            }

            $groups="";
            $color="";
            $color1="";
            $company=$branch->company;
            $groups_all=$company->groups;
            $groups=array_pluck($groups_all->toArray(),'name');
            $groups=implode(", ",$groups);
            foreach($groups_all as $g){
                if($g->name=='EDB'){
                    $color='#551A8B';
                    $color1='#fff';
                    break;
                }
            }

            $is_company_vat_checkable = 0;
            if($company->companyVatCheck) {
                $is_company_vat_checkable = 1;
                if ($company->is_active_vat == 1 && count($company->groups) > 0) {
                    $color='#dff0d8';
                    $color1='black';
                } elseif ($company->is_active_vat == 0) {
                    $color='#f2dede';
                    $color1='black';
                }
            }

            $data = '<span class="bt_com_search" id="'.$branch->id.'" id_marker="'.$k.'" style="background:'.$color.'" data-name="'.$company->name.' - '.$branch->short_name.'" data-address="'.$branch->code.' '.$branch->city.', '.$branch->street.'">
						<h6 style="color:'.$color1.'">'.
                        (($branch->suspended) ? '<span class="label label-danger"><i class="fa fa-exclamation-triangle fa-fw"></i></span>' : '')
						.$company->name.' - '.$branch->short_name.'<p class="pull-right" style="margin:0;margin-right:5px">'.$groups.'</p></h6></span>
						<div>
						<h6>'.$company->name.' - '.$branch->short_name.'</h6>
						<h7>Adres: </h7>'.$branch->code.' '.$branch->city.', '.$branch->street.'<br>
						<h7>Telefon: </h7>'.$branch->phone.'<br><h7>Email: </h7>'.$branch->email;
            if($cars != '-1')
                $data .= '<br><h7>Auta zastępcze:</h7>'.$cars;

            if($tug!= '-1')
            {
                $data .= '<br><h7>Holownik:</h7>';
                if($tug == 1)
                    $data .= ' na stanie';
                else
                    $data .= ' brak na stanie';
                if($tug24 == 1)
                    $data .= ' 24h';
            }

            if ($is_company_vat_checkable) {
                $data .= '<br><h7>Status VAT:</h7>
                      ' . $company->companyVatCheck->status . '
                      <br><h7>Data sprawdzenia:</h7>
                      ' . $company->companyVatCheck->created_at->format("Y-m-d H:i");
            }

            $data .= '</div>';


            //check which company accounts are assigned to invoice
            $assigned_numbers = $company->accountNumbers;
            $raw_companies = true;
            
            if($invoice_id > 0){
                $invoice = InjuryInvoices::find($invoice_id);
                if($invoice->branch){
                    //check if queried branch is assigned to invoice, if not then skip marking accounts as assigned
                    foreach($branches as $branch_flag) {
                        if($branch_flag->id == $invoice->branch->id) $raw_companies = false;
                    }

                    if(!$raw_companies){
                        $temp = $invoice->branch->company->accountNumbers;
                        foreach($temp as $number){
                            foreach($invoice->assignedBankAccountNumbers as $number_invoice){
                                if($number->id == $number_invoice->id){
                                    $number->assigned = true;
                                    break;
                                } else {
                                    $number->assigned = false;
                                }
                            }
                        }
                        $assigned_numbers = $temp;
                    }
                }
            }

            $json[] = array(
                'nazwa' => $company->name . ' - ' . $branch->short_name,
                'kod' => $branch->code,
                'miasto' => $branch->city,
                'ulica' => $branch->street,
                'id' => $branch->id,
                'lat' => $branch->lat,
                'lng' => $branch->lng,
                'telefon' => $branch->phone,
                'email' => $branch->email,
                'cars' => $cars,
                'tug' => $tug,
                'tug24' => $tug24,
                'dataText' => $data,
                'color' => $color,
                'company_vat_check_info' => $branch->company()->first()->companyVatCheck?['is_active' => $branch->company()->first()->is_active_vat, 'vat_check_date' => $branch->company()->first()->companyVatCheck->created_at, 'status' => $branch->company()->first()->companyVatCheck->status]:['is_active' => $branch->company()->first()->is_active_vat],
                'bank_accounts' => $assigned_numbers,
            );
        }
        return json_encode($json);
    }

	public function setAssignBranch($id){
        $injury = Injury::find($id);

        if( is_null($injury->prev_step) && $injury->step == 0 && $injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
            $contract = $injury->vehicle->nr_contract;
            $issuedate = $injury->date_event;
            $issuenumber = $injury->case_nr;
            $issuetype = 'B';
            $username = substr(Auth::user()->login, 0, 10);

            $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

            $owner_id = $injury->vehicle->owner_id;

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

            $xml = $webservice->getResponseXML();

            if ($xml->Error->ErrorCde != 'ERR0000') {
                if($xml->Error->ErrorCde == 'ERR0006'){
                    $ISSUENUMBER = $injury->case_nr;
                    $COMMENT = Input::get('content');
                    $USERNAME = substr(Auth::user()->login, 0, 10);
                    $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

                    $owner_id = $injury->vehicle->owner_id;

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');

                    $xml = $webservice->getResponseXML();
                    if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                        if($xml->Error->ErrorCde ==  'ERR0014'){
                            $data = new Idea\Structures\CHGISSUETYPEInput($ISSUENUMBER, 'B',$USERNAME);
                            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');
                            $xml = $webservice->getResponseXML();
                            if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                $result['code'] = 2;
                                $result['error'] = $xml->Error->ErrorDes->__toString();
                                $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                return json_encode($result);
                            }
                        }else {
                            $result['code'] = 2;
                            $result['error'] = $xml->Error->ErrorDes->__toString();
                            $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                            return json_encode($result);
                        }
                    }
                }else{
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                    return json_encode($result);
                }
            }
        }
        $branch = Branch::find(Input::get('id_warsztat'));
        // $branch->company - accountNumbers;

        if( ($branch->company->groups->contains(1) || ( $branch->company->groups->contains(5) && $injury->vehicle->cfm == 1) ) && $injury->edb()->count() > 0)
        {
            $injury->step = 11;
        }else{
            $injury->step = 10;
        }

        $injury->branch_id = Input::get('id_warsztat');

        $branch = Branch::find(Input::get('id_warsztat'));
        foreach($injury->invoices as $invoice)
        {
            if($invoice->relatedCommission)
                $invoice->relatedCommission->update(['company_id' => $branch->company_id]);
        }

        $msg = ' -> '.$branch->short_name;
        Histories::history($id, 31, Auth::user()->id, '-1', $msg);

        if( !Input::has('dont_send_sms') || Input::get('dont_send_sms') != 1 ){
            if($branch->company->groups->contains(1) || $branch->company->groups->contains(5)) {
                if ($injury->contact_person == 1) {
                    if ($injury->driver_id != '') {
                        $driver = Drivers::find($injury->driver_id);
                        $phone_nb = trim($driver->phone);
                        $phone_nb = str_replace(' ', '', $phone_nb);
                    } else
                        $phone_nb = '';
                } else {
                    $phone_nb = trim($injury->notifier_phone);
                    $phone_nb = str_replace(' ', '', $phone_nb);
                }

                if ($phone_nb != '') {
                    $vehicle = $injury->vehicle;
                    $branch = Branch::find($injury->branch_id);

                    $msg = "Informujemy, że likwidację szkody w pojezdzie " . $vehicle->registration . " wykona serwis: " . $branch->short_name . ", ulica " . $branch->street . ", " . $branch->city . ", tel. " . $branch->phone . ". CAS w imieniu ".$vehicle->owner->name;

                    send_sms($phone_nb, $msg);

                    Histories::history($injury->id, 138, Auth::user()->id, $phone_nb);
                }
            }
        }elseif(Input::has('dont_send_sms')){
            Log::info('zrezygnowano z wysyłki sms dla sprawy id: '.$id);
        }

        $injury->save();
        if($injury->sap)
        {
            $sap = new Idea\SapService\Sap();
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($injury->id, 217, Auth::user()->id);
            }
        }

            //echo 0;
            Session::put('last_injury', $id);
            Session::put('last_injury_case_nr', $injury->case_nr);

            $result['code'] = 1;
            $result['url'] = URL::route('injuries-info', array($injury->id));
            return json_encode($result);


	}

	public function getInfo($id)
	{
	    if($id == 'undefined') {
            return Redirect::to('/injuries/new');
        }
		Session::put('last_injury', $id);
		$url = URL::previous();
		if(	!is_null($url) && $url != '' && isset($url) && isset($_SERVER['HTTP_REFERER']) ){
			$path = parse_url($url);
			$path = $path['path'];
			if($path == '/injuries/new' || $path == '/injuries/inprogress' || $path == '/injuries/total' || $path == '/injuries/theft' ||
				 $path == '/injuries/completed' || $path == '/injuries/canceled' || $path == '/injuries/search/global' )

				Session::put('prev', $url);

		}

		$injury = Injury::with('vehicle', 'compensations', 'compensations.injury_file',
            'compensations.decisionType', 'previousWrecks', 'previousWrecks.buyerInfo',
            'repairStages', 'repairStages.stage', 'estimates', 'estimates.injury_file',
            'totalStepStage', 'insuranceCompany', 'injuryPolicy.insuranceCompany', 'sapPremiums', 'tasks', 'tasks.comments', 'tasks.files')->find($id);

		$info = Text_contents::find($injury->info);
		$remarks = Text_contents::find($injury->remarks);
		$remarks_damage = Text_contents::find($injury->remarks_damage);
		$damage = Damage_type::all();
		$ct_damage = count($damage);
		$damageSet = InjuryDamage::where('injury_id', '=', $id)->get();
		$damageInjury = array();
		$type_incident = Type_incident::all();
        $history = InjuryHistory::where('injury_id', '=', $id)->orderBy('id', 'desc')->with('user', 'history_type', 'injury_history_content')->get();
        $stepHistory = InjuryStepHistory::where('injury_id', '=', $id)->orderBy('created_at', 'desc')->get();

		$imagesBefore = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 1)->where('active', '=', '0')->with('user')->get();
		$imagesInprogress = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 2)->where('active', '=', '0')->with('user')->get();
		$imagesAfter = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 1)->where('category', '=', 3)->where('active', '=', '0')->with('user')->get();

		$chat = InjuryChat::select(DB::raw('injury_chat.*, (select icmj.created_at from injury_chat icj left outer join injury_chat_messages icmj on icj.id = icmj.chat_id where icj.id = injury_chat.id ORDER BY created_at DESC limit 1) as last_message'))
					->where('injury_chat.injury_id', '=', $id)
					->whereIn('injury_chat.active', array(0,5))
                    ->orderBy('last_message', 'desc')
                    ->orderBy('injury_chat.active', 'asc')
					->with('messages', 'user', 'messages.user', 'messages')->get();

        $documentsTypes = $injury->vehicle->owner->group->injuryDocumentTypes()->where(function($query) use ($injury){
            $query->where('cfm', ($injury->vehicle->cfm == 1) ? '1' : '0' )->orWhere('cfm' , 2);
        })->orderBy('chronology')->get();
        $documents = InjuryFiles::where(function($query)
			{
				$query->where('type', '=', 2)->orWhere('type', '=', 3)->orWhere('type', '=', 4);
			})->where('category', '!=', 0)->where('injury_id', '=', $id)->where('active', '=', '0')->with('user', 'document')->orderBy('id', 'desc')->get();

		$invoices = InjuryInvoices::where('injury_id', '=', $id)->where('active','=', '0')->with('injury_files', 'parent', 'invoicereceive', 'base_invoice', 'serviceType', 'status')->get();

        $sum_net = 0;
        $sum_vat = 0;
        $invoicesCorrection = array();
        foreach($invoices as $invoice)
        {
            $child = $invoice->where('active','=', '0')->where('parent_id', $invoice->id)->get();
            if( ! $child->isEmpty() )
            {
                foreach($child as $children) {
                    $invoicesCorrection[$children->id] = [
                        'netto' => $children->netto - $invoice->netto,
                        'vat' => $children->vat - $invoice->vat
                    ];
                }
            }else if($invoice->parent_id == 0){
                $sum_net += $invoice->netto;
                $sum_vat += $invoice->vat;
            }
        }

        $invoicesToSum = InjuryInvoices::where('parent_id', '>', 0)->where('injury_id', '=', $id)->where('active', '0')->get();
        foreach ($invoicesToSum as $invoice)
        {
            if(! $invoice->child) {
                $sum_net += $invoice->netto;
                $sum_vat += $invoice->vat;
            }
        }

        $invoicesSum = ['sum_net' => $sum_net, 'sum_vat' => $sum_vat];

        $invoices = InjuryInvoices::where('injury_id', '=', $id)->where('active','=', '0')->with('injury_files', 'parent', 'invoicereceive')->get();

		$genDocuments = InjuryFiles::where('injury_id', '=', $id)->where('type', '=', 3)->where('active', '=', '0')->with('user')->orderBy('created_at', 'desc')->get();
        $genDocumentsA = array();
		foreach ($genDocuments as $k => $v) {
			$genDocumentsA[$v->category][] = $v;
		}

		foreach ($damageSet as $key => $value) {
			$damageInjury[$value->damage_id][$value->param] = 1;
		}

        $templates = SmsTemplates::whereActive(0)->get();

        $notifier_phone = trim($injury->notifier_phone);

        $driver_phone = ($injury->driver_id != '') ? trim($injury->driver()->first()->phone) : '';

        $phonesSMS = array();

        if($injury->contact_person == 1 && $driver_phone != ''){
            $phonesSMS[] = array(
                'name' => 'tel. kierowcy: '.$driver_phone,
                'value' => $driver_phone
            );
            if($notifier_phone != ''){
                $phonesSMS[] = array(
                    'name' => 'tel. zgłaszającego: '.$notifier_phone,
                    'value' => $notifier_phone
                );
            }
        }else if($injury->contact_person == 2 && $notifier_phone != ''){
            $phonesSMS[] = array(
                'name' => 'tel. zgłaszającego: '.$notifier_phone,
                'value' => $notifier_phone
            );
            if($driver_phone != ''){
                $phonesSMS[] = array(
                    'name' => 'tel. kierowcy: '.$driver_phone,
                    'value' => $driver_phone
                );
            }
        }else{
            if($notifier_phone != ''){
                $phonesSMS[] = array(
                    'name' => 'tel. zgłaszającego: '.$notifier_phone,
                    'value' => $notifier_phone
                );
            }
            if($driver_phone != ''){
                $phonesSMS[] = array(
                    'name' => 'tel. kierowcy: '.$driver_phone,
                    'value' => $driver_phone
                );
            }
        }

        $totalRepairAcceptation = InjuryTotalRepairAcceptationType::active()->get();
        $theftAcceptation = InjuryTheftAcceptationType::active()->get();

        if($injury->vehicle_type == 'Vehicles') {
            $vehicles = DB::select(DB::raw('
				SELECT T2.id
				FROM (
				    SELECT
				        @r AS _id,
				        (SELECT @r := parent_id FROM vehicles WHERE id = _id) AS parent_id,
				        @l := @l + 1 AS lvl
				    FROM
				        (SELECT @r := ' . $injury->vehicle_id . ', @l := 0) vars,
				        vehicles h
				    WHERE @r <> 0) T1
				JOIN vehicles T2
				ON T1._id = T2.id
				ORDER BY T1.lvl DESC
			'));
        }else{
            $vehicles = [];
        }
        $vehicles2 = DB::table(with(new $injury->vehicle_type)->getTable())->select('id')->where('id', '!=', $injury->vehicle_id)->where('registration', '=', $injury->vehicle->registration)->get();

        $vehiclesA = array_map(
            function($oObject){
                $aConverted = get_object_vars($oObject);
                return $aConverted['id'];
            },
            $vehicles);
        $vehiclesA = array_merge($vehiclesA,
            array_map(
            function($oObject){
                $aConverted = get_object_vars($oObject);
                return $aConverted['id'];
            },
            $vehicles2)
        );

        $injuriesExistsOnVehicle = Injury::whereIn('vehicle_id', $vehiclesA)->where('vehicle_type', $injury->vehicle_type)->where('id', '!=', $id)->where('active', '=', 0)->with('getInfo')->get();

        $matchedLetters = InjuryLetter::whereNull('injury_file_id')->where(function($query) use($injury){
                                $query->where(function($subquery) use($injury){
                                    $subquery->whereNotNull('injury_nr')->where('injury_nr', '!=', '')->where('injury_nr', $injury->injury_nr);
                                });
                                $query->orWhere(function($subquery) use($injury){
                                    $subquery->whereNotNull('nr_contract')->where('nr_contract', '!=', '')->where('nr_contract', $injury->vehicle->nr_contract);
                                });
                                $query->orWhere(function($subquery) use($injury){
                                    $subquery->whereNotNull('registration')->where('registration', '!=', '')->where('registration', $injury->vehicle->registration);
                                });
                            })->get();
        if($injury->vehicle_type == 'VmanageVehicle') {
            $csm_types = VmanageCsmType::whereDefault(1)->orWhere('vmanage_company_id', $injury->vehicle->vmanage_company_id)->lists('name', 'id');
        }else
            $csm_types = null;


        $repairStages = TInjuryRepairStage::get();
        if($injury->repairStages->count() == 0) {
            foreach ($repairStages as $repairStage) {
                InjuryRepairStage::create([
                    'injury_id' => $injury->id,
                    't_injury_repair_stage_id' => $repairStage->id
                ]);
            }
            $injury->load('repairStages');
        }
        $injuryRepairStages = [];
        foreach($injury->repairStages as $repairStage)
        {
            $injuryRepairStages[$repairStage->t_injury_repair_stage_id] = $repairStage;
        }

        $cessionAmount = $injury->injuryCessionAmount()->first();

        return View::make('injuries.info', compact('injury', 'info', 'remarks', 'damage', 'ct_damage', 'damageSet', 'damageInjury', 'imagesBefore',
        					'imagesInprogress', 'imagesAfter', 'type_incident', 'history', 'documentsTypes', 'documents', 'genDocumentsA', 'remarks_damage',
                            'chat', 'invoices', 'templates', 'phonesSMS', 'totalRepairAcceptation', 'theftAcceptation','injuriesExistsOnVehicle', 'invoicesSum',
                            'matchedLetters', 'csm_types', 'invoicesCorrection', 'repairStages', 'injuryRepairStages', 'cessionAmount', 'stepHistory'
                        ));
	}

	public function setTotal($id)
	{
        $injury = Injury::find($id);
        $injury->prev_step = $injury->step;
        $injury->step = 30;
        $injury->date_total_theft_register = Carbon\Carbon::now();
        $injury->total_status_id = 11;
        $injury->total_status_source = 0;
        InjuryWreck::create(array(
            'injury_id'  =>  $id
        ));
        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 11,
            'status_type' => 'InjuryTotalStatuses'
        ]);

        Histories::history($id, 30, Auth::user()->id);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
	}

    public function setTotalFinished($id)
    {
        $injury = Injury::find($id);

        if ($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $ISSUENUMBER = $injury->case_nr;
            $USERNAME = substr(Auth::user()->login, 0, 10);
            $owner_id = $injury->vehicle->owner_id;

            $data = new Idea\Structures\CLOSEISSUEInput($ISSUENUMBER, NULL, $USERNAME);
            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');
            $xml = $webservice->getResponseXML();

            if ($xml->Error->ErrorCde != 'ERR0000') {
                $result['code'] = 2;
                $result['error'] = $xml->Error->ErrorDes->__toString();
                return json_encode($result);
            }
        }

        $injury->prev_step = $injury->step;
        $injury->step = '-7';
        $injury->date_end = date('Y-m-d H:i:s');
        $injury->date_end_total = date('Y-m-d H:i:s');
        $injury->save();

        Session::put('last_injury', $id);
        Session::put('last_injury_case_nr', $injury->case_nr);

        $result['code'] = 1;
        $result['url'] = URL::route('injuries-total-finished');

        Histories::history($id, 142, Auth::user()->id);

        return json_encode($result);
    }

    public function setTotalInjuries($id){
        $injury = Injury::find($id);
        $injury->step = 31;
        $injury->save();

        Histories::history($id, 191, Auth::user()->id);
        $result = Idea\AsService\AsService::total($injury->id);
        return json_encode($result);
    }

	public function setTheft($id)
	{
        $injury = Injury::find($id);
        $injury->step = 40;
        $injury->date_total_theft_register = Carbon\Carbon::now();
        $injury->theft_status_id = 3;
        $injury->total_status_source = 1;

        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 3,
            'status_type' => 'InjuryTheftStatuses'
        ]);

        Histories::history($id, 118, Auth::user()->id);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
	}

	public function setInprogress($id)
	{
		$injury = Injury::find($id);
        $injury->prev_step = $injury->step;
		$injury->step = '10';
        Histories::history($id, 174, Auth::user()->id);
		if( $injury->save() ){
			$result['code'] = 0;
			return json_encode($result);
		}
	}

    public function setDiscontinuationInvestigation($id)
    {
        $injury = Injury::find($id);
        $injury->theft_status_id = 5;
        Histories::history($id, 175, Auth::user()->id);
        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 5,
            'status_type' => 'InjuryTheftStatuses'
        ]);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setDeregistrationVehicle($id)
    {
        $injury = Injury::find($id);
        $injury->theft_status_id = 11;
        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 11,
            'status_type' => 'InjuryTheftStatuses'
        ]);
        Histories::history($id, 176, Auth::user()->id);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }
    public function setAgreementSettled($id)
    {
        $injury = Injury::find($id);
        $injury->step = 46;
        $injury->theft_status_id = 14;
        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 14,
            'status_type' => 'InjuryTheftStatuses',
            'date_end' => date('Y-m-d H:i:s')
        ]);
        Histories::history($id, 194, Auth::user()->id);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setTransferredDok($id)
    {
        $injury = Injury::find($id);
        $injury->theft_status_id = 12;
        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 12,
            'status_type' => 'InjuryTheftStatuses'
        ]);
        Histories::history($id, 177, Auth::user()->id);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setNoSignsPunishment($id)
    {
        $injury = Injury::find($id);
        $injury->theft_status_id = 10;
        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => 10,
            'status_type' => 'InjuryTheftStatuses'
        ]);
        Histories::history($id, 114, Auth::user()->id);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setUsurpation($id)
    {
        $injury = Injury::find($id);
        $injury->step = 47;
        $injury->save();
        Histories::history($id, 198, Auth::user()->id);
        Idea\AsService\AsService::theft($injury->id);
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

	public function setComplete($id)
	{
		$injury = Injury::find($id);

        $contract = $injury->vehicle->nr_contract;
        $issuedate = $injury->date_event;
        $issuenumber = $injury->case_nr;
        $issuetype = 'B';
        $username = substr(Auth::user()->login, 0, 10);

        $owner_id = $injury->vehicle->owner_id;

        if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

            $xml = $webservice->getResponseXML();
        }
		if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

			if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){
                $data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

				if( $xml->Error->ErrorCde != 'ERR0000'){
					$result['code'] = 2;
					$result['error'] = $xml->Error->ErrorDes->__toString();
					return json_encode($result);
				}else{
                    $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $xml = $webservice->getResponseXML();

					if( $xml->Error->ErrorCde != 'ERR0000'){
						$result['code'] = 2;
						$result['error'] = $xml->Error->ErrorDes->__toString();
						return json_encode($result);
					}
				}
			}

            $injury->prev_step = $injury->step;
			$injury->step = '15';
			$injury->date_end = date("Y-m-d H:i:s");
            $injury->date_end_normal = date("Y-m-d H:i:s");

			Histories::history($id, 114, Auth::user()->id);

			if( $injury->save() ){
				$result['code'] = 0;
				return json_encode($result);
			}
		}else{
			$result['code'] = 2;
			$result['error'] = $xml->Error->ErrorDes->__toString();
			return json_encode($result);
		}
	}

    public function setCompleteRefused($id)
    {
        $injury = Injury::find($id);

        $contract = $injury->vehicle->nr_contract;
        $issuedate = $injury->date_event;
        $issuenumber = $injury->case_nr;
        $issuetype = 'B';
        $username = substr(Auth::user()->login, 0, 10);

        $owner_id = $injury->vehicle->owner_id;

        if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

            $xml = $webservice->getResponseXML();
        }
        if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

            if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){
                $data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if( $xml->Error->ErrorCde != 'ERR0000'){
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    return json_encode($result);
                }else{
                    $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $xml = $webservice->getResponseXML();

                    if( $xml->Error->ErrorCde != 'ERR0000'){
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        return json_encode($result);
                    }
                }
            }

            $injury->prev_step = $injury->step;
            $injury->step = '24';
            $injury->date_end = date("Y-m-d H:i:s");
            $injury->date_end_normal = date("Y-m-d H:i:s");

            Histories::history($id, 173, Auth::user()->id);

            if( $injury->save() ){
                $result['code'] = 0;
                return json_encode($result);
            }
        }else{
            $result['code'] = 2;
            $result['error'] = $xml->Error->ErrorDes->__toString();
            return json_encode($result);
        }
    }

	public function setRefusal($id)
	{
		$injury = Injury::find($id);

        $contract = $injury->vehicle->nr_contract;
        $issuedate = $injury->date_event;
        $issuenumber = $injury->case_nr;
        $issuetype = 'B';
        $username = substr(Auth::user()->login, 0, 10);
        $closecode = 'O';

        $owner_id = $injury->vehicle->owner_id;

        if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, $closecode, $username);

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

            $xml = $webservice->getResponseXML();
        }

		if($injury->vehicle->owner->wsdl == ''  || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

			if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){
                $data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

				if( $xml->Error->ErrorCde != 'ERR0000'){
					$result['code'] = 2;
					$result['error'] = $xml->Error->ErrorDes->__toString();
					return json_encode($result);
				}else{
                    $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, $closecode, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $xml = $webservice->getResponseXML();

					if( $xml->Error->ErrorCde != 'ERR0000'){
						$result['code'] = 2;
						$result['error'] = $xml->Error->ErrorDes->__toString();
						return json_encode($result);
					}
				}
			}

            $injury->prev_step = $injury->step;
            if(
                $injury->branch_id != '-1' &&
                (
                    $injury->branch->company->groups->contains(1) ||
                    ( $injury->branch->company->groups->contains(5) && $injury->vehicle->cfm == 1 )
                )
            )
            {
                $injury->step = 22;
            }else{
                $injury->step = 20;
            }

			Histories::history($id, 117, Auth::user()->id);

			if( $injury->save() ){
				$result['code'] = 0;
				return json_encode($result);
			}
		}else{
			$result['code'] = 2;
			$result['error'] = $xml->Error->ErrorDes->__toString();
			return json_encode($result);
		}

	}

	public function setDamage($id)
	{
		$damage = Damage_type::all();
		$injury_damage = InjuryDamage::where('injury_id', '=', $id)->get();
		$old_valA = array();
		foreach ($injury_damage as $k => $v) {
			$old_valA[$v->damage_id][$v->param] = 1;
		}

		InjuryDamage::where('injury_id', '=', $id)->delete();

		if(Input::has('uszkodzenia')){
			foreach(Input::get('uszkodzenia') as $k => $v){
				if(Input::has('strona'.$v)){
					foreach(Input::get('strona'.$v) as $k2 => $v2){
						InjuryDamage::create(array(
								'injury_id' => $id,
								'damage_id' => $v,
								'param'		=> $v2
							));

							if( !isset($old_valA[$v][$v2]) ){
								if($v2 == 1) {
									switch($damage->find($v)->param){
									  case 1:
										  $strona = 'lewy';
										  break;
									  case 2:
										  $strona = 'lewe';
										  break;
									  case 3:
										  $strona = 'lewa';
										  break;
									}
								}else{
									switch($damage->find($v)->param){
									  case 1:
										  $strona = 'prawy';
										  break;
									  case 2:
										  $strona = 'prawe';
										  break;
									  case 3:
										  $strona = 'prawa';
										  break;
									}
								}
								Histories::history($id, $damage->find($v)->history_type_id, Auth::user()->id,  $strona.' - tak');
							}else{
								unset($old_valA[$v][$v2]);
							}

					}
				}else{
					if( $damage->find($v)->param == 0){
						InjuryDamage::create(array(
								'injury_id' => $id,
								'damage_id' => $v,
								'param'		=> 0
							));
						if( !isset($old_valA[$v]) ){
							Histories::history($id, $damage->find($v)->history_type_id, Auth::user()->id,  'tak');
						}else{
							unset($old_valA[$v]);
						}
					}else{
						unset($old_valA[$v]);
					}
				}

			}

			foreach( $old_valA as $k => $v){
				if($damage->find($k) ->param == 0){
					Histories::history($id, $damage->find($k)->history_type_id, Auth::user()->id, 'nie');
				}else if(count($v) > 0){
					foreach($v as $k2 => $v2){
						if($k2 == 1) {
							switch($damage->find($k)->param){
							  case 1:
								  $strona = 'lewy';
								  break;
							  case 2:
								  $strona = 'lewe';
								  break;
							  case 3:
								  $strona = 'lewa';
								  break;
							}
						}else{
							switch($damage->find($k)->param){
							  case 1:
								  $strona = 'prawy';
								  break;
							  case 2:
								  $strona = 'prawe';
								  break;
							  case 3:
								  $strona = 'prawa';
								  break;
							}
						}
						Histories::history($id, $damage->find($k)->history_type_id, Auth::user()->id,  $strona.' - nie');
					}
				}
			}


		}

	}

	public function setCancel($id)
	{
		$injury = Injury::find($id);
        $injury->prev_step = $injury->step;
		$injury->step = '-10';
		$injury->date_end = date("Y-m-d H:i:s");
        $injury->date_end_normal = date("Y-m-d H:i:s");

		Histories::history($id, 29, Auth::user()->id);

        //dodanie wątku rozmowy w kartotece
        $status = bindec('100');

        if (get_chat_group() == 1)
            $dos_read = date('Y-m-d H:i:s');
        else
            $dos_read = null;

        if (get_chat_group() == 3)
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        if (get_chat_group() == 2)
            $branch_read = date('Y-m-d H:i:s');
        else
            $branch_read = null;

        if($injury->canceled_chat_id == null) {
            $chat = InjuryChat::create(array(
                    'injury_id' => $id,
                    'user_id' => Auth::user()->id,
                    'topic' => 'Anulowanie szkody',
                    'status' => $status
                )
            );

            $injury->canceled_chat_id = $chat->id;
        }else{
            $chat = InjuryChat::find($injury->canceled_chat_id);
        }

        InjuryChatMessages::create(array(
                'chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> Input::get('content'),
                'status'	=> $status,
                'dos_read'	=> $dos_read,
                'info_read'	=> $info_read,
                'branch_read' => $branch_read
            )
        );


        if( $injury->save() ){
			$result['code'] = 0;
			return json_encode($result);
		}
	}


	public  function setChangeStatus($injury_id)
    {
        $injury = Injury::find($injury_id);

        $injury->prev_step = $injury->step;
        $injury->step = Input::get('step');

        if( in_array(Input::get('step'), array(
            '-10',
            '-7',
            15,
            16,
            17,
            18,
            21,
            23,
            24,
            25,
            26,
            34,
            35,
            36,
            37,
            45,
            44
        )) ) {
            $injury->date_end = date("Y-m-d H:i:s");

            $step = InjurySteps::findOrFail(Input::get('step'));
            switch ($step->injury_group_id){
                case 1:
                    $injury->date_end_normal = date("Y-m-d H:i:s");
                    break;
                case 2:
                    $injury->date_end_total = date("Y-m-d H:i:s");
                    break;
                case 3:
                    $injury->date_end_theft = date("Y-m-d H:i:s");
                    break;
            }
        }else{
            $injury->date_end = null;
        }

        if(in_array(Input::get('step'), [30,31, 32, 33, 34, 35, 36, 37, 40, 41, 42, 43, 44, 45, 46, 47])){
            if(is_null($injury->date_total_theft_register)){
                $injury->date_total_theft_register = Carbon\Carbon::now();
            }
        }
        if(in_array(Input::get('step'), [30,31, 32, 33, 34, 35, 36, 37]) ) {
            if (!$injury->wreck) {
                InjuryWreck::create(array(
                    'injury_id' => $injury->id
                ));
            }
        }
        if(in_array(Input::get('step'), [31, 32, 33, 34, 35, 36, 37]) ){
            Idea\AsService\AsService::total($injury->id);
        }elseif(in_array(Input::get('step'), [41, 42, 43, 44, 45, 46])){
            Idea\AsService\AsService::theft($injury->id);
        } elseif( in_array(Input::get('step'), ['-7', 15, 17, 18, 19, 23, 24]) ) {
            if ($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
                $ISSUENUMBER = $injury->case_nr;
                $USERNAME = substr(Auth::user()->login, 0, 10);
                $owner_id = $injury->vehicle->owner_id;

                $data = new Idea\Structures\CLOSEISSUEInput($ISSUENUMBER, NULL, $USERNAME);
                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');
                $webservice->getResponseXML();
            }
        }elseif(  in_array(Input::get('step'), [0, 10, 11]) ){
            if( $injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
                $contract = $injury->vehicle->nr_contract;
                $issuedate = $injury->date_event;
                $issuenumber = $injury->case_nr;
                $issuetype = 'B';
                $username = substr(Auth::user()->login, 0, 10);

                $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                $owner_id = $injury->vehicle->owner_id;

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if ($xml->Error->ErrorCde != 'ERR0000') {
                    if($xml->Error->ErrorCde == 'ERR0006'){
                        $ISSUENUMBER = $injury->case_nr;
                        $COMMENT = Input::get('content');
                        $USERNAME = substr(Auth::user()->login, 0, 10);
                        $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

                        $owner_id = $injury->vehicle->owner_id;

                        $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');

                        $xml = $webservice->getResponseXML();
                        if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                            if($xml->Error->ErrorCde ==  'ERR0014'){
                                $data = new Idea\Structures\CHGISSUETYPEInput($ISSUENUMBER, 'B',$USERNAME);
                                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');
                                $xml = $webservice->getResponseXML();
                                if ($xml->Error->ErrorCde != 'ERR0000'  ) {
                                    $result['code'] = 2;
                                    $result['error'] = $xml->Error->ErrorDes->__toString();
                                    $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                    $error=true;
                                    //return json_encode($result);
                                }
                            }else {
                                $result['code'] = 2;
                                $result['error'] = $xml->Error->ErrorDes->__toString();
                                $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                                $error=true;
                                //return json_encode($result);
                            }
                        }
                    }else{
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        $result['ErrorCde'] = $xml->Error->ErrorCde->__toString();
                        $error=true;
                        //return json_encode($result);
                    }
                }
            }
        }

        $injury->save();

        Histories::history($injury_id, 213, Auth::user()->id, ' - '.$injury->status->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setChangeInjuryStep($injury_id){
        $injury = Injury::find($injury_id);

        if( in_array($injury->step, [30,31,32,33,34,35,36,37]) ) {
            $injury->total_status_id = Input::get('step');
            InjuryStatusesHistory::create([
                'injury_id' => $injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => Input::get('step'),
                'status_type' => 'InjuryTotalStatuses'
            ]);
            $name = $injury->totalStatus->name;
        }elseif(in_array($injury->step, [40,41,42,43,44,45,46])){
            $injury->theft_status_id = Input::get('step');
            InjuryStatusesHistory::create([
                'injury_id' => $injury->id,
                'user_id'   => Auth::user()->id,
                'status_id' => Input::get('step'),
                'status_type' => 'InjuryTheftStatuses'
            ]);
            $name = $injury->theftStatus->name;
        }

        $injury->save();

        Histories::history($injury_id, 195, Auth::user()->id, 'Na STATUS - '.$name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setRestoreCanceled($id)
    {
        $injury = Injury::find($id);

        $prev_step = $injury->prev_step;

        if( is_null($prev_step) || $prev_step == '' )
        {
            if($injury->branch_id == 0)
                $prev_step = 0;
            else
                $prev_step = 10;
        }

        $injury->step = $prev_step;
        $injury->prev_step = '-10';
        if(in_array($prev_step, array(
            '-10',
            '-7',
            15,
            16,
            17,
            18,
            21,
            23,
            24,
            25,
            26,
            34,
            35,
            36,
            37,
            45,
            44
        ))){
            $injury->date_end = date('Y-m-d H:i:s');

            $step = InjurySteps::findOrFail($prev_step);
            switch ($step->injury_group_id){
                case 1:
                    $injury->date_end_normal = date("Y-m-d H:i:s");
                    break;
                case 2:
                    $injury->date_end_total = date("Y-m-d H:i:s");
                    break;
                case 3:
                    $injury->date_end_theft = date("Y-m-d H:i:s");
                    break;
            }
        }

        Histories::history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

        //dodanie wątku rozmowy w kartotece
        $status = bindec('100');

        $chat = InjuryChat::find($injury->canceled_chat_id);

        if (get_chat_group() == 1)
            $dos_read = date('Y-m-d H:i:s');
        else
            $dos_read = null;

        if (get_chat_group() == 3)
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        if (get_chat_group() == 2)
            $branch_read = date('Y-m-d H:i:s');
        else
            $branch_read = null;

        InjuryChatMessages::create(array(
                'chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> Input::get('content'),
                'status'	=> $status,
                'dos_read'	=> $dos_read,
                'info_read'	=> $info_read,
                'branch_read' => $branch_read
            )
        );

        $injury->skip_in_ending_report = null;

        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

		public function setRestoreDeleted($id)
		{
				$injury = MobileInjury::find($id);
				$injury->active = 0;
				$result = array();
				if( $injury->save() ){
						$result['code'] = 1;
						$result['url'] = '/injuries/unprocessed';
 						return json_encode($result);
				}

		}

    public function setRestoreCompleted($id)
    {
        $injury = Injury::find($id);

        if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $ISSUENUMBER = $injury->case_nr;
            $COMMENT = Input::get('content');
            $USERNAME = substr(Auth::user()->login, 0, 10);
            $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

            $owner_id = $injury->vehicle->owner_id;

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');

            $xml = $webservice->getResponseXML();
        }

        if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ){

            if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010'){

                $contract = $injury->vehicle->nr_contract;
                $issuedate = $injury->date_event;
                $issuetype = 'B';

                $data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $ISSUENUMBER, $issuetype, $USERNAME);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if( $xml->Error->ErrorCde != 'ERR0000'){
                    $result['code'] = 2;
                    $result['error'] = $xml->Error->ErrorDes->__toString();
                    return json_encode($result);
                }else{
                    $data = new Idea\Structures\CLOSEISSUEInput($ISSUENUMBER, NULL, $USERNAME);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $xml = $webservice->getResponseXML();

                    if( $xml->Error->ErrorCde != 'ERR0000'){
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        return json_encode($result);
                    }
                }
            }

            $injury->prev_step = $injury->step;
            if($injury->step == 21){
                $injury->step = 11;
            }else{
                $injury->step = 10;

            }

            Histories::history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

            $injury->skip_in_ending_report = null;

            if( $injury->save() ){
                $result['code'] = 0;
                return json_encode($result);
            }

        }else{

            $result['code'] = 2;
            $result['error'] = $xml->Error->ErrorDes->__toString();
            return json_encode($result);

        }

    }

    public function setRestoreTotal($id)
    {
        $injury = Injury::find($id);
        if(is_null($injury->prev_step)){

            $prev_step = $injury->prev_step;
            $injury->prev_step = '0';

            $injury->step = $prev_step;
            if(in_array($prev_step, array(
                '-10',
                '-7',
                15,
                16,
                17,
                18,
                21,
                23,
                24,
                25,
                26,
                34,
                35,
                36,
                37,
                45,
                44
            ))){
                $injury->date_end = date('Y-m-d H:i:s');

                $step = InjurySteps::findOrFail($prev_step);
                switch ($step->injury_group_id){
                    case 1:
                        $injury->date_end_normal = date("Y-m-d H:i:s");
                        break;
                    case 2:
                        $injury->date_end_total = date("Y-m-d H:i:s");
                        break;
                    case 3:
                        $injury->date_end_theft = date("Y-m-d H:i:s");
                        break;
                }
            }

            Histories::history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:' . Input::get('content'));
            $injury->skip_in_ending_report = null;
            if ($injury->save()) {
                $result['code'] = 0;
                return json_encode($result);
            }
        }else {
            if ($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
                $ISSUENUMBER = $injury->case_nr;
                $COMMENT = Input::get('content');
                $USERNAME = substr(Auth::user()->login, 0, 10);
                $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);

                $owner_id = $injury->vehicle->owner_id;

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');

                $xml = $webservice->getResponseXML();

                if($xml->Error->ErrorCde == 'ERR0014'){
                    $data = new Idea\Structures\CLOSEISSUEInput($ISSUENUMBER, NULL, $USERNAME);
                    Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $data = new Idea\Structures\REOPENISSUEInput($ISSUENUMBER, $COMMENT, $USERNAME);
                    $owner_id = $injury->vehicle->owner_id;
                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reopenissue');
                    $xml = $webservice->getResponseXML();
                }
            }

            if ($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010' ) {

                if ($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010') {

                    $contract = $injury->vehicle->nr_contract;
                    $issuedate = $injury->date_event;
                    $issuetype = 'B';

                    $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $ISSUENUMBER, $issuetype, $USERNAME);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                    $xml = $webservice->getResponseXML();

                    if ($xml->Error->ErrorCde != 'ERR0000') {
                        $result['code'] = 2;
                        $result['error'] = $xml->Error->ErrorDes->__toString();
                        return json_encode($result);
                    } else{
                        $data = new Idea\Structures\CLOSEISSUEInput($ISSUENUMBER, NULL, $USERNAME);

                        $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                        $xml = $webservice->getResponseXML();

                        if ($xml->Error->ErrorCde != 'ERR0000') {
                            $result['code'] = 2;
                            $result['error'] = $xml->Error->ErrorDes->__toString();
                            return json_encode($result);
                        }
                    }
                }
                $prev_step = $injury->prev_step;
                $injury->prev_step = $injury->step;

                $injury->step = $prev_step;
                if(in_array($prev_step, array(
                    '-10',
                    '-7',
                    15,
                    16,
                    17,
                    18,
                    21,
                    23,
                    24,
                    25,
                    26,
                    34,
                    35,
                    36,
                    37,
                    45,
                    44
                ))){
                    $injury->date_end = date('Y-m-d H:i:s');

                    $step = InjurySteps::findOrFail($prev_step);
                    switch ($step->injury_group_id){
                        case 1:
                            $injury->date_end_normal = date("Y-m-d H:i:s");
                            break;
                        case 2:
                            $injury->date_end_total = date("Y-m-d H:i:s");
                            break;
                        case 3:
                            $injury->date_end_theft = date("Y-m-d H:i:s");
                            break;
                    }
                }

                Histories::history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:' . Input::get('content'));
                $injury->skip_in_ending_report = null;
                if ($injury->save()) {
                    $result['code'] = 0;
                    return json_encode($result);
                }

            } else {

                $result['code'] = 2;
                $result['error'] = $xml->Error->ErrorDes->__toString();
                return json_encode($result);

            }
        }

    }

    public function setResignationClaims($id)
    {
        $injury = Injury::find($id);
        $injury->step = 36;
        $injury->date_end = date('Y-m-d H:i:s');
        $injury->date_end_total = date('Y-m-d H:i:s');
        $injury->skip_in_ending_report = null;
        $injury->save();
        Histories::history($id, 182, Auth::user()->id);
        $result['code'] = 0;
        return json_encode($result);
    }

    public function setContractSettled($id)
    {
        $injury = Injury::find($id);
        $injury->step = 37;
        $injury->date_end = date('Y-m-d H:i:s');
        $injury->date_end_total = date('Y-m-d H:i:s');
        $injury->save();
        Histories::history($id, 183, Auth::user()->id);
        $result['code'] = 0;
        return json_encode($result);
    }

    public function setRestore($id)
    {
        $injury = Injury::find($id);

        if($injury->vehicle->owner->wsdl != ''  && $injury->vehicle->register_as == 1) {
            $issuenumber = $injury->case_nr;
            $username = substr(Auth::user()->login, 0, 10);
            $issuetype = 'B';

            $data = new Idea\Structures\CHGISSUETYPEInput($issuenumber, $issuetype, $username);

            $owner_id = $injury->vehicle->owner_id;

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chgissuetype');

            $xml = $webservice->getResponseXML();
        }

        if($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000'  || $xml->Error->ErrorCde == 'ERR0010' ){
            $injury->prev_step = $injury->step;
            $injury->step = Input::get('step');
            if(Input::get('step') == 0){
                $injury->branch_id = 0;
            }
            if(in_array(Input::get('step'), array(
                '-10',
                '-7',
                15,
                16,
                17,
                18,
                21,
                23,
                24,
                25,
                26,
                34,
                35,
                36,
                37,
                45,
                44
            ))){
                $injury->date_end = date("Y-m-d H:i:s");

                $step = InjurySteps::findOrFail(Input::get('step'));
                switch ($step->injury_group_id){
                    case 1:
                        $injury->date_end_normal = date("Y-m-d H:i:s");
                        break;
                    case 2:
                        $injury->date_end_total = date("Y-m-d H:i:s");
                        break;
                    case 3:
                        $injury->date_end_theft = date("Y-m-d H:i:s");
                        break;
                }
            }


            Histories::history($id, 140, Auth::user()->id, 'Przyczyna przywrócenia:'.Input::get('content'));

            if( $injury->save() ){
                $result['code'] = 0;
                return json_encode($result);
            }
        }else{
            $result['code'] = 2;
            $result['error'] = $xml->Error->ErrorDes->__toString();
            return json_encode($result);
        }
    }

	public function setUnlock($id)
	{
		$injury = Injury::find($id);
		$injury->locked_status = '-5';

		Histories::history($id, 112 , Auth::user()->id);

		if( $injury->save() ){
			$result['code'] = 0;
			return json_encode($result);
		}
	}



	public function setLock($id)
	{
		$injury = Injury::find($id);
		$injury->locked_status = '5';

		Histories::history($id, 113 , Auth::user()->id);

		if( $injury->save() ){
			$result['code'] = 0;
			return json_encode($result);
		}
	}

	public function setTask()
	{
		$id_injury = Input::get('id_injury');
		$task = Input::get('task');
		$injury = Injury::find($id_injury);
		$injury->$task = Input::get('val');

		$id_hist = '';
		switch (Input::get('task')) {
			case 'task_inspection':
				$id_hist = 33;
				break;
			case 'task_orderedParts':
				$id_hist = 36;
				break;
			case 'task_pickup':
				$id_hist = 110;
				break;
			case 'task_authorization':
				$id_hist = 111;
				break;
		}

		Histories::history($id_injury, $id_hist, Auth::user()->id, Input::get('val')==1 ? 'tak' : 'nie');
		$injury->save();

        return 0;
	}

    public function setDelete($id)
    {
        $injury = MobileInjury::find($id);
        $injury->active = 9;
        $result = array();
        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function setDeleteEa($id)
    {
        $injury = EaInjury::find($id);
        $injury->delete();

        $result['code'] = 0;
        return json_encode($result);
    }

	public function setInvoice($id){
        $invoice = InjuryInvoices::find($id);
        $old_receiver = $invoice->invoicereceives_id;
		if($invoice->injury_files()->first()->category == 4){
            $parent_id = Input::get('parent_id');
            if ($parent_id > 0) {
                $parent = InjuryInvoices::find($parent_id);

                $invoice->parent_id = Input::get('parent_id');
                $invoice->base_invoice_id = ($parent->base_invoice_id) ? $parent->base_invoice_id : $parent->id;
            }
        }
		$invoice->invoicereceives_id 	= Input::get('invoicereceives_id');
		$invoice->invoice_nr			= Input::get('invoice_nr');
		$invoice->invoice_date			= Input::get('invoice_date');
		$invoice->payment_date			= Input::get('payment_date');
		$invoice->netto 				= Input::get('netto');
		$invoice->vat 					= Input::get('vat');
		$invoice->vat_rate_id           = Input::get('vat_rate_id');
		$invoice->commission			= Input::get('commission');
		$invoice->base_netto			= Input::get('base_netto');
        $invoice->injury_invoice_service_type_id = (Input::get('injury_invoice_service_type_id') == 0) ? null : Input::get('injury_invoice_service_type_id');

        if(Input::has('companies_matched')){
            $branch = Branch::find(Input::get('companies_matched'));
            if(!is_null($branch->company->companyVatCheck)){
                $invoice->company_vat_check_id = $branch->company->companyVatCheck->id;
            }
            $invoice->branch_id = $branch->id;
        }

		if($invoice->injury&&Input::has('branch_id')){

			$injury=$invoice->injury;
			if(!$invoice->injury->original_branch_id){
				$injury->original_branch_id=$injury->branch_id;
				$injury->branch_id=Input::get('branch_id');
			}
			else{
				$injury->branch_id=Input::get('branch_id');
			}
			$injury->save();
		}

		$invoice->save();

		if(Input::get('commission') == 1 && ! count($invoice->relatedCommission) ) {
            $commission = new Commission;
            $commission->injury_invoice_id = $invoice->id;
            $commission->company_id = ($invoice->injury->branch) ? $invoice->injury->branch->company_id : null;
            $commission->commission_step_id = 1;
            $commission->invoice_date = $invoice->invoice_date;
            $commission->created_at = $invoice->created_at;
            $commission->save();
        }

        $assigned = Input::has('assigned')?Input::get('assigned'):[];
        $result = $invoice->assignedBankAccountNumbers()->sync($assigned);
        if (count($result['attached']) > 0 || count($result['detached'])) Histories::history($invoice->injury_id, 215, Auth::user()->id, ' Dodano: ' . count($result['attached']) . ' Usunięto: ' . count($result['detached']) . '.');
        if ($old_receiver != $invoice->invoicereceives_id) Histories::history($invoice->injury_id, 124, Auth::user()->id);
        echo 0;
    }

	public function getInvoiceCommission($id){
		$invoice = InjuryInvoices::with('injury_files', 'parent', 'invoicereceive', 'base_invoice', 'serviceType')->find($id);
		if($invoice->injury_files()->first()->category == 4){
            $parent_id = Input::get('parent_id');
            if($parent_id > 0) {
                $parent = InjuryInvoices::find($parent_id);

                $invoice->parent_id = Input::get('parent_id');
                $invoice->base_invoice_id = ($parent->base_invoice_id) ? $parent->base_invoice_id : $parent->id;
            }
		}
		$invoice->invoicereceives_id 	= Input::get('invoicereceives_id');
		$invoice->invoice_nr			= Input::get('invoice_nr');
		$invoice->invoice_date			= Input::get('invoice_date');
		$invoice->payment_date			= Input::get('payment_date');
		$invoice->netto 				= Input::get('netto');
		$invoice->vat 					= Input::get('vat');
		$invoice->commission			= Input::get('commission');
		$invoice->base_netto			= Input::get('base_netto');
		$invoice->injury_invoice_service_type_id = (Input::get('injury_invoice_service_type_id') == 0) ? null : Input::get('injury_invoice_service_type_id');

		$result['value'] = $invoice->valueOfCommission();
		$result['code'] = 0;
		return json_encode($result);

	}

	public function setDeleteInvoice($id)
    {
        $invoice = InjuryInvoices::find($id);

        $invoice->update(['active' => 1]);

        $invoice->injury_files()->update(['active' => 1]);

        Histories::history($invoice->injury_id, 170 , Auth::user()->id, '<a target="_blank" href="'.URL::route('injuries-downloadDoc', array($invoice->injury_files()->first()->id)).'">pobierz</a>');

        if($invoice->note)
        {
            $sap = new Idea\SapService\Sap();
            $notesToRemove[0] = $invoice->note;
            $result = $sap->szkodaNotKasuj($invoice->injury, $notesToRemove);
            if(isset($result['ftNotatkaKeys'])){
                foreach($result['ftNotatkaKeys'] as $notatkaKey){
                    InjuryNote::where('injury_id', $invoice->injury->id)->where('roknotatki', $notatkaKey['roknotatki'])->where('nrnotatki', $notatkaKey['nrnotatki'])->delete();
                }
            }else{
                Flash::error('Wystąpił błąd w trakcie usuwania notatek.');
            }
        }

        $result['code'] = 0;
        return json_encode($result);
    }

    public function setDeleteCompensation($id)
    {
        $compensation = InjuryCompensation::with('injury_file')->find($id);
        if($compensation->injury_file  )
            $compensation->injury_file->update(['active' => 1]);

        Histories::history($compensation->injury_id, 171 , Auth::user()->id, ( $compensation->injury_file )?'<a target="_blank" href="'.URL::route('injuries-downloadDoc', array($compensation->injury_file->id)).'">pobierz</a>' : '');

        if($compensation->note)
        {
            $sap = new Idea\SapService\Sap();
            $notesToRemove[0] = $compensation->note;
            $result = $sap->szkodaNotKasuj($compensation->injury, $notesToRemove);
            if(isset($result['ftNotatkaKeys'])){
                foreach($result['ftNotatkaKeys'] as $notatkaKey){
                    InjuryNote::where('injury_id', $compensation->injury->id)->where('roknotatki', $notatkaKey['roknotatki'])->where('nrnotatki', $notatkaKey['nrnotatki'])->delete();
                }
            }else{
                Flash::error('Wystąpił błąd w trakcie usuwania notatek.');
            }
        }

        if(Input::get('premium') == 1){
            $sap = new Idea\SapService\Sap();

            if($compensation->premium)
            {
                $compensation->premium->delete();
                $compensation->delete();
                $sap->szkoda($compensation->injury);
            }elseif($compensation->mode == 1){
                $compensation_value = $compensation->compensation;

                $compensation->update(['compensation' => 0]);
                $sap->szkoda($compensation->injury);

                $compensation->update(['compensation' => $compensation_value]);
                $compensation->delete();
            }
        }else {
            $compensation->delete();
        }

        $result['code'] = 0;
        return json_encode($result);
    }

		public function setDeleteEstimate($id)
		{
				$estimate = InjuryEstimate::with('injury_file')->find($id);
				if($estimate->injury_file  )
						$estimate->injury_file->update(['active' => 1]);

				Histories::history($estimate->injury_id, 193 , Auth::user()->id, ( $estimate->injury_file )?'<a target="_blank" href="'.URL::route('injuries-downloadDoc', array($estimate->injury_file->id)).'">pobierz</a>' : '');

				$estimate->delete();

				$result['code'] = 0;
				return json_encode($result);
		}

	public function setEditInjury($id)
	{
		$injury = Injury::find($id);

		if(Input::get('injuries_type') == '2' || Input::get('injuries_type') == '4' || Input::get('injuries_type') == '5'){
			if($injury->offender_id == 0){
				$offender = Offenders::create(array(
						'surname'	=>	mb_strtoupper(Input::get('offender_surname'), 'UTF-8'),
						'name'		=>	mb_strtoupper(Input::get('offender_name'), 'UTF-8'),
						'post'		=>	mb_strtoupper(Input::get('offender_post'), 'UTF-8'),
						'city'		=>	mb_strtoupper(Input::get('offender_city'), 'UTF-8'),
						'street'	=>	mb_strtoupper(Input::get('offender_street'), 'UTF-8'),
						'registration'	=>	mb_strtoupper(Input::get('offender_registration'), 'UTF-8'),
						'car'		=>	mb_strtoupper(Input::get('offender_car'), 'UTF-8'),
						'oc_nr'		=>	mb_strtoupper(Input::get('offender_oc_nr'), 'UTF-8'),
						'zu'		=>	mb_strtoupper(Input::get('offender_zu'), 'UTF-8'),
						'expire'	=> 	mb_strtoupper(Input::get('offender_expire'), 'UTF-8'),
						'owner'		=>	mb_strtoupper(Input::get('offender_owner'), 'UTF-8'),
						'remarks'	=>	Input::get('offender_remarks')
					));
			}else{
				$offender = Offenders::find($injury->offender_id);

				$offender->surname	=	mb_strtoupper(Input::get('offender_surname'), 'UTF-8');
				$offender->name		=	mb_strtoupper(Input::get('offender_name'), 'UTF-8');
				$offender->post		=	mb_strtoupper(Input::get('offender_post'), 'UTF-8');
				$offender->city		=	mb_strtoupper(Input::get('offender_city'), 'UTF-8');
				$offender->street	=	mb_strtoupper(Input::get('offender_street'), 'UTF-8');
				$offender->registration	=	mb_strtoupper(Input::get('offender_registration'), 'UTF-8');
				$offender->car		=	mb_strtoupper(Input::get('offender_car'), 'UTF-8');
				$offender->oc_nr	=	mb_strtoupper(Input::get('offender_oc_nr'), 'UTF-8');
				$offender->zu		=	mb_strtoupper(Input::get('offender_zu'), 'UTF-8');
				$offender->expire	= 	mb_strtoupper(Input::get('offender_expire'), 'UTF-8');
				$offender->owner	=	mb_strtoupper(Input::get('offender_owner'), 'UTF-8');
				$offender->remarks	=	Input::get('offender_remarks');

				$offender->save();

			}
			$id_offender = $offender->id;
		}else $id_offender = 0;



		$vehicle = $injury->vehicle;
		if($vehicle->insurance_company_id != Input::get('insurance_company_id')){
		    $prev_insurance_company = $vehicle->insurance_company()->first() ? $vehicle->insurance_company()->first()->name : '';
			$vehicle->insurance_company_id = Input::get('insurance_company_id');
			$vehicle->save();
            $vehicle = $injury->vehicle;
			Histories::history($id, 127, Auth::user()->id, $prev_insurance_company.' -> '.$vehicle->insurance_company()->first()->name);
		}

		if($injury->injuries_type_id != Input::get('injuries_type') ) {
            $new_injuries_type = Injuries_type::find(Input::get('injuries_type'));
            Histories::history($id, 107, Auth::user()->id, $injury->injuries_type()->first()->name. ' -> '.$new_injuries_type->name);

            $injury->injuries_type_id = Input::get('injuries_type');
        }

		$injury->offender_id = $id_offender;

		if($injury->receive_id != Input::get('receives') ) {
		    $new_receive = Receives::find(Input::get('receives'));
            Histories::history($id, 108, Auth::user()->id, ($injury->receive()->first() ? $injury->receive()->first()->name : '') .' -> '.$new_receive->name);

            $injury->receive_id = Input::get('receives');
        }

		if($injury->invoicereceives_id != Input::get('invoicereceives') ) {
		    $new_invoicereceive = Invoicereceives::find(Input::get('invoicereceives'));
            Histories::history($id, 124, Auth::user()->id, ($injury->invoicereceive()->first() ? $injury->invoicereceive()->first()->name : '').' -> '.$new_invoicereceive->name);
            $injury->invoicereceives_id = Input::get('invoicereceives');
        }

		if($injury->date_event != Input::get('date_event') ) {
            Histories::history($id, 109, Auth::user()->id, $injury->date_event.' -> '.Input::get('date_event'));
            $injury->date_event = Input::get('date_event');
        }

		if($injury->injury_nr != Input::get('injury_nr') ) {
            Histories::history($id, 2, Auth::user()->id, $injury->injury_nr.' -> '.mb_strtoupper(Input::get('injury_nr'), 'UTF-8'));
        }

		if( (! $injury->injury_nr || $injury->injury_nr == '') && Input::get('injury_nr') != '' && ($injury->injury_step_stage_id < 2  || is_null($injury->injury_step_stage_id)) )
        {
            if($injury->injury_step_stage_id < 2) {
                $injury->injury_step_stage_id = 2;

                InjuryStepStageHistory::create([
                    'injury_id' => $injury->id,
                    'injury_step_stage_id' => 2
                ]);
            }
        }elseif( ( $injury->injury_nr || (! $injury->injury_nr && $injury->injury_nr != '') ) && Input::get('injury_nr') == ''&& ($injury->injury_step_stage_id < 2  || is_null($injury->injury_step_stage_id)) ){
            if($injury->injury_step_stage_id <= 2) {
                $injury->injury_step_stage_id = 1;

                InjuryStepStageHistory::create([
                    'injury_id' => $injury->id,
                    'injury_step_stage_id' => 1
                ]);
            }
        }

		$injury->injury_nr = mb_strtoupper(Input::get('injury_nr'), 'UTF-8');

		if($injury->if_statement != Input::get('if_statement') ) {
            Histories::history($id, 8, Auth::user()->id, Input::get('if_statement') == 1 ? 'tak' : 'nie');
            $injury->if_statement = Input::get('if_statement');
        }

		if($injury->if_registration_book != Input::get('if_registration_book') ) {
            Histories::history($id, 9, Auth::user()->id, Input::get('if_registration_book') == 1 ? 'tak' : 'nie');
            $injury->if_registration_book = Input::get('if_registration_book');
        }

		if($injury->if_towing != Input::get('if_towing') ) {
            Histories::history($id, 77, Auth::user()->id, Input::get('if_towing') == 1 ? 'tak' : 'nie');
            $injury->if_towing = Input::get('if_towing');
        }

		if($injury->if_courtesy_car != Input::get('if_courtesy_car') ) {
            Histories::history($id, 78, Auth::user()->id, Input::get('if_courtesy_car') == 1 ? 'tak' : 'nie');
            $injury->if_courtesy_car = Input::get('if_courtesy_car');
        }

		if($injury->if_door2door != Input::get('if_door2door') ) {
            Histories::history($id, 125, Auth::user()->id, Input::get('if_door2door') == 1 ? 'tak' : 'nie');
            $injury->if_door2door = Input::get('if_door2door');
        }

		if($injury->police != Input::get('police') ) {
            Histories::history($id, 7, Auth::user()->id, $this->options[$injury->police].' -> '.$this->options[Input::get('police')]);
            $injury->police = Input::get('police');
        }


        if( Input::has('settlement_cost_estimate')){
            if($injury->settlement_cost_estimate != 1)
                Histories::history($id, 157, Auth::user()->id, 'tak' );

            $injury->settlement_cost_estimate = Input::get('settlement_cost_estimate');

        }else{
            if($injury->settlement_cost_estimate != 0)
                Histories::history($id, 157, Auth::user()->id, 'nie' );

            $injury->settlement_cost_estimate = '0';
        }

		$injury->police_nr = mb_strtoupper(Input::get('police_nr'), 'UTF-8');
		$injury->police_unit = mb_strtoupper(Input::get('police_unit'), 'UTF-8');
		$injury->police_contact = mb_strtoupper(Input::get('police_contact'), 'UTF-8');

		if($injury->type_incident_id != Input::get('zdarzenie') ){
			$id_hist = '';
			switch(Input::get('zdarzenie'))
			{
				case '1':
					$id_hist = 10;
					break;
				case '2':
					$id_hist = 11;
					break;
				case '3':
					$id_hist = 12;
					break;
				case '4':
					$id_hist = 13;
					break;
				case '5':
					$id_hist = 14;
					break;
				case '6':
					$id_hist = 15;
					break;
				case '7':
					$id_hist = 16;
					break;
				case '8':
					$id_hist = 17;
					break;
				case '9':
					$id_hist = 18;
					break;
				case '10':
					$id_hist = 132;
					break;
				case '11':
					$id_hist = 19;
					break;
				case '12':
					$id_hist = 118;
					break;
				case '13':
					$id_hist = 139;
					break;
			}
            $new_type_incident = Type_incident::find(Input::get('zdarzenie'));

			Histories::history($id, $id_hist, Auth::user()->id, ($injury->type_incident()->first() ? $injury->type_incident()->first()->name : '').' -> '.$new_type_incident->name);
            $injury->type_incident_id = Input::get('zdarzenie');
        }

		if( Input::has('zdarzenie') && Input::get('zdarzenie') == 12 ) $injury->if_theft = 1; else $injury->if_theft = 0;

        if($injury->if_driver_fault != Input::get('if_driver_fault') ) {
            Histories::history($id, 166, Auth::user()->id,$this->options[$injury->if_driver_fault].' -> '.$this->options[Input::get('if_driver_fault')]);

            $injury->if_driver_fault = Input::get('if_driver_fault');
        }

        if( (Input::get('reported_ic')=='1') || ( Input::get('injury_nr') != '' ) ) {
            if($injury->step == 0 && $injury->injury_step_stage_id == 1) {
                $injury->fill(['injury_step_stage_id' => 2]);
                InjuryStepStageHistory::create([
                    'injury_id' => $injury->id,
                    'injury_step_stage_id' => 2
                ]);
            }
        }else{
            if($injury->step == 0 && $injury->injury_step_stage_id ==2) {
                $injury->fill(['injury_step_stage_id' => 1]);
                InjuryStepStageHistory::create([
                    'injury_id' => $injury->id,
                    'injury_step_stage_id' => 1
                ]);
            }
        }
        $inputs = Input::all();
        if(isset($inputs['sap_rodzszk']) && $inputs['sap_rodzszk'] == '0' && !in_array($injury->sap_rodzszk, ['TOT', 'KRA'])){
            $inputs['sap_rodzszk'] = null;
        }
        $injury->fill($inputs);

        if( Input::has('dsp_notification')) {
            $injury->dsp_notification = 1;
        }else{
            $injury->dsp_notification = 0;
        }

        if( Input::has('vindication')) {
            $injury->vindication = 1;
        }else{
            $injury->vindication = 0;
        }

        $changes = $injury->getDirty();
        $info = [];
        foreach($changes as $changed_field => $changed_value) {
            switch ($changed_field) {
                case 'time_event':
                    $info[] = 'Czas zdarzenia: '.$injury->getOriginal('time_event'). ' -> '. $changed_value;
                    break;
                case 'police_nr':
                    $info[] = 'nr zgłoszenia policji: '.$injury->getOriginal('police_nr'). ' -> '. $changed_value;
                    break;
                case 'police_unit':
                    $info[] = 'jednostka policji: '.$injury->getOriginal('police_unit'). ' -> '. $changed_value;
                    break;
                case 'police_contact':
                    $info[] = 'kontakt z policją: '.$injury->getOriginal('police_contact'). ' -> '. $changed_value;
                    break;
                case 'reported_ic':
                    $info[] = 'szkoda zgłoszona do TU: '.$this->options[$injury->getOriginal('reported_ic')].' -> '.$this->options[Input::get('reported_ic', 0)];
                    break;
                case 'in_service':
                    $info[] = 'samochód znajduje się w serwisie: '.$this->options[$injury->getOriginal('in_service')].' -> '.$this->options[Input::get('in_service', 0)];
                    break;
                case 'if_il_repair':
                    $info[] = 'naprawa w sieci IL: '.$this->options[$injury->getOriginal('if_il_repair')].' -> '.$this->options[Input::get('if_il_repair', 0)];
                    break;
                case 'il_repair_info':
                    if(Input::get('il_repair_info') > 0 && $injury->if_il_repair == 0) {
                        $info[] = 'przyczyna naprawy poza siecią IL: ' . ($injury->getOriginal('il_repair_info') > 0 ? RepairInformation::find($injury->getOriginal('il_repair_info'))->name : '') . ' -> ' . RepairInformation::find(Input::get('il_repair_info', 0))->name;
                    }
                    break;
                case 'dsp_notification':
                    $info[] = 'zgłoszenie DSP: '.$this->options[$injury->getOriginal('dsp_notification')].' -> '.$this->options[Input::get('dsp_notification', 0)];
                    break;
                case 'vindication':
                    $info[] = 'windykacja: '.$this->options[$injury->getOriginal('vindication')].' -> '.$this->options[Input::get('vindication', 0)];
                    break;
                case 'cas_offer_agreement':
                    $info[] = 'zgoda na ofertę CAS: '.$this->options[$injury->getOriginal('cas_offer_agreement')].' -> '.$this->options[Input::get('cas_offer_agreement', 0)];
                    break;
            }
        }
        if(count($info) > 0) {
            Histories::history($id, 212, Auth::user()->id, '-1', implode('; ', $info));
        }

        if($injury->sap_rodzszk != Input::get('sap_rodzszk')){
            Histories::history($id, 212, Auth::user()->id, 'SAP rodzszk => '.Input::get('sap_rodzszk') );
        }

        $injury->save();

        if($injury->sap) {
            $sap = new \Idea\SapService\Sap();
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($id, 217, Auth::user()->id);
            }
        }

        return json_encode(['code' => 0]);
	}

    public function setEditInjuryInsurance($id)
    {
        $injury = Injury::find($id);
        $policy = $injury->injuryPolicy;
        $policy->insurance_company_id = Input::get('insurance_company_id');
        $policy->insurance = Input::get('insurance');
        $policy->contribution = Input::get('contribution');
        $policy->netto_brutto = Input::get('netto_brutto');
        $policy->gap = Input::get('gap');
        $policy->nr_policy = Input::get('nr_policy');
        $policy->legal_protection = Input::get('legal_protection');
        $policy->risks = Input::get('risks');

        $changes = $policy->getDirty();
        $info = [];
        foreach($changes as $changed_field => $changed_value) {
            switch ($changed_field) {
                case 'insurance_company_id':
                    $info[] = 'AC TU: '.(Insurance_companies::find($policy->getOriginal('insurance_company_id')) ? Insurance_companies::find($policy->getOriginal('insurance_company_id'))->name : ''). ' -> '. ($policy->insuranceCompany ? $policy->insuranceCompany->name : '');
                    break;
                case 'insurance':
                    $info[] = 'AC suma ubezpieczenia: '.$policy->getOriginal('insurance'). ' -> '. $changed_value;
                    break;
                case 'contribution':
                    $info[] = 'AC wkład własny: '.$policy->getOriginal('contribution'). ' -> '. $changed_value;
                    break;
                case 'netto_brutto':
                    $info[] = 'AC netto/brutto: '.( Config::get('definition.compensationsNetGross')[$policy->getOriginal('netto_brutto')]  ). ' -> '. ( Config::get('definition.compensationsNetGross')[$changed_value] );
                    break;
                case 'gap':
                    $info[] = 'AC gap: '.Config::get('definition.insurance_options_definition')[$policy->getOriginal('gap')]. ' -> '. $changed_value;
                    break;
                case 'nr_policy':
                    $info[] = 'AC nr polisy: '.$policy->getOriginal('nr_policy'). ' -> '. $changed_value;
                    break;
                case 'legal_protection':
                    $info[] = 'AC ochrona prawna: '.Config::get('definition.insurance_options_definition')[$policy->getOriginal('legal_protection')]. ' -> '. Config::get('definition.insurance_options_definition')[$changed_value];
                    break;
                case 'risks':
                    $info[] = 'AC zakres ubezpieczenia: '.$policy->getOriginal('risks'). ' -> '. $changed_value;
                    break;
            }
        }
        Histories::history($id, 212, Auth::user()->id, '-1', implode('; ', $info));

        $policy->save();

        if(! $injury->injuryGap){
            InjuryGap::create(['injury_id' => $injury->id, 'insurance_company_id' => Input::get('gap_insurance_company_id')]);
        }

        $vehicle = $injury->vehicle;
        $vehicle->update([
            'policy_insurance_company_id' => $policy->insurance_company_id,
            'expire' => $policy->expire,
            'nr_policy' => $policy->nr_policy,
            'insurance' => $policy->insurance,
            'contribution' => $policy->contribution,
            'netto_brutto' => $policy->netto_brutto,
            'assistance' => $policy->assistance,
            'assistance_name' => $policy->assistance_name,
            'risks' => $policy->risks,
            'gap' => $policy->gap,
            'legal_protection' => $policy->legal_protection
        ]);

        echo 0;
    }

    public function setEditInjuryGap($id)
    {
        $injury = Injury::find($id);

        $injuryGap = $injury->injuryGap;
        if(! $injuryGap){
            $injuryGap = InjuryGap::create(['injury_id' => $injury->id]);
        }

        $injuryGap->update(Input::all());

        echo json_encode(['code' => 0]);
    }

	public function setEditInjuryDriver($id)
	{
		if( Input::has('driver_id')  && Input::get('driver_id') != '' ){
			$injury = Injury::find($id);
			$driver = Drivers::find(Input::get('driver_id'));
            $info = '';
            if($injury->driver){
                $info .= 'imię: '.$injury->driver->name.' -> '.$driver->name;
                $info .= ';nazwisko: '.$injury->driver->surname.' -> '.$driver->surname;
                $info .= ';telefon: '.$injury->driver->phone.' -> '.$driver->phone;
                $info .= ';email: '.$injury->driver->email.' -> '.$driver->email;
                $info .= ';miasto: '.$injury->driver->city.' -> '.$driver->city;

            }
			$injury -> driver_id = Input::get('driver_id');

			Histories::history($id, 119, Auth::user()->id, '-1', $info);

			if( $injury->save() ) echo 0;

		}else{
			$injury = Injury::find($id);

			$driver = Drivers::create(array(
					'client_id'	=> $injury->client_id,
					'surname' 	=> mb_strtoupper(Input::get('surname'), 'UTF-8'),
					'name'		=> mb_strtoupper(Input::get('name'), 'UTF-8'),
					'phone'		=> mb_strtoupper(Input::get('phone'), 'UTF-8'),
					'email'		=> Input::get('email'),
					'city'		=> mb_strtoupper(Input::get('city'), 'UTF-8')
				));

			$info = '';
			if($injury->driver){
			    $info .= 'imię: '.$injury->driver->name.' -> '.$driver->name;
                $info .= ';nazwisko: '.$injury->driver->surname.' -> '.$driver->surname;
                $info .= ';telefon: '.$injury->driver->phone.' -> '.$driver->phone;
                $info .= ';email: '.$injury->driver->email.' -> '.$driver->email;
                $info .= ';miasto: '.$injury->driver->city.' -> '.$driver->city;

            }
			$injury -> driver_id = $driver->id;
			Histories::history($id, 119, Auth::user()->id, '-1', $info);

			if( $injury->save() ) echo 0;

		}
	}

	public function setEditInjuryOffender($id)
	{
		$injury = Injury::find($id);

		$post_data = Input::all();
		$offender = Offenders::find($injury->offender_id);
		$offender->fill($post_data);

        $changes = $offender->getDirty();
        $info = [];
        foreach($changes as $changed_field => $changed_value) {
            switch ($changed_field) {
                case 'surname':
                    $info[] = 'nazwisko: ' . $offender->getOriginal('surname') . ' -> ' . $changed_value;
                    break;
                case 'name':
                    $info[] = 'imię: ' . $offender->getOriginal('name') . ' -> ' . $changed_value;
                    break;
                case 'post':
                    $info[] = 'kod pocztowy: ' . $offender->getOriginal('post') . ' -> ' . $changed_value;
                    break;
                case 'city':
                    $info[] = 'miasto: ' . $offender->getOriginal('city') . ' -> ' . $changed_value;
                    break;
                case 'street':
                    $info[] = 'ulica: ' . $offender->getOriginal('street') . ' -> ' . $changed_value;
                    break;
                case 'registration':
                    $info[] = 'nr rejestracyjny: ' . $offender->getOriginal('registration') . ' -> ' . $changed_value;
                    break;
                case 'car':
                    $info[] = 'marka i model pojazdu: ' . $offender->getOriginal('car') . ' -> ' . $changed_value;
                    break;
                case 'oc_nr':
                    $info[] = 'nr polisy OC: ' . $offender->getOriginal('oc_nr') . ' -> ' . $changed_value;
                    break;
                case 'zu':
                    $info[] = 'nazwa ZU: ' . $offender->getOriginal('zu') . ' -> ' . $changed_value;
                    break;
                case 'expire':
                    $info[] = 'data ważności polisy: ' . $offender->getOriginal('expire') . ' -> ' . $changed_value;
                    break;
                case 'owner':
                    $info[] = 'Sprawca jest właścicielem pojazdu: ' . ($offender->getOriginal('owner') == 1 ? 'tak' : 'nie') . ' -> ' . ($changed_value == 1 ? 'tak' : 'nie');
                    break;
                case 'remarks':
                    $info[] = 'uwagi: ' . $offender->getOriginal('remarks') . ' -> ' . $changed_value;
                    break;
            }
        }

		if( $offender->save() ){
			Histories::history($id, 135, Auth::user()->id, '-1', implode('; ', $info));
			echo 0;
		}
	}


	public function setEditInjuryNotifier($id)
	{

		$injury = Injury::find($id);
		$injury->notifier_surname = mb_strtoupper(Input::get('surname'), 'UTF-8');
		$injury->notifier_name = mb_strtoupper(Input::get('name'), 'UTF-8');
		$injury->notifier_phone = mb_strtoupper(Input::get('phone'), 'UTF-8');
		$injury->notifier_email = Input::get('email');

        $changes = $injury->getDirty();
        $info = [];
        foreach($changes as $changed_field => $changed_value) {
            switch ($changed_field) {
                case 'notifier_surname':
                    $info[] = 'nazwisko: ' . $injury->getOriginal('notifier_surname') . ' -> ' . $changed_value;
                    break;
                case 'notifier_name':
                    $info[] = 'imię: ' . $injury->getOriginal('notifier_name') . ' -> ' . $changed_value;
                    break;
                case 'notifier_phone':
                    $info[] = 'telefon: ' . $injury->getOriginal('notifier_phone') . ' -> ' . $changed_value;
                    break;
                case 'notifier_email':
                    $info[] = 'email: ' . $injury->getOriginal('notifier_email') . ' -> ' . $changed_value;
                    break;
            }
        }
		Histories::history($id, 120, Auth::user()->id, '-1', implode('; ', $info));

		if( $injury->save() ) echo 0;
	}

	public function setEditInjuryClientContact($id)
	{

		$injury = Injury::find($id);

		$old_client = Clients::find($injury->client_id);

		if( $old_client->correspond_post != Input::get('correspond_post') || $old_client->correspond_street != Input::get('correspond_post') ||
			$old_client->correspond_city != Input::get('correspond_city') || $old_client->phone != Input::get('phone') || $old_client->email != Input::get('email')){

            $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
            $registry_post = $old_client->registry_post;
            if(strlen($registry_post) == 6)
            {
                $registry_voivodeship_id = $matcher->match($registry_post);
            }else{
                $registry_voivodeship_id = null;
            }

            $correspond_post = Input::get('correspond_post');
            if(strlen($correspond_post) == 6)
            {
                $correspond_voivodeship_id = $matcher->match($correspond_post);
            }else{
                $correspond_voivodeship_id = null;
            }

			$client = Clients::create(array(
					'parent_id' => $old_client->id,
                    'firmID'    => $old_client->firmID,
					'name' 		=> $old_client->name,
					'NIP'		=> $old_client->NIP,
					'REGON'		=> $old_client->REGON,
					'registry_post'		=> $old_client->registry_post,
					'registry_city'		=> $old_client->registry_city,
					'registry_street'	=> $old_client->registry_street,
                    'registry_voivodeship_id' => $registry_voivodeship_id,
					'correspond_post'	=> Input::get('correspond_post'),
					'correspond_city'	=> Input::get('correspond_city'),
					'correspond_street'	=> Input::get('correspond_street'),
                    'correspond_voivodeship_id' => $correspond_voivodeship_id,
					'phone'				=> Input::get('phone'),
					'email'				=> Input::get('email')
				));
			$injury->client_id = $client->id;
            $old_client->update(['active' => 1]);
			Histories::history($id, 121, Auth::user()->id);
			if( $injury->save() ) echo 0;

		}else{
			echo 0;
		}

	}

    public function setEditInjuryClient($id)
    {
        $injury = Injury::find($id);

        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

        $registry_post = Input::get('registry_post');
        if(strlen($registry_post) == 6)
        {
            $registry_voivodeship_id = $matcher->match($registry_post);
        }else{
            $registry_voivodeship_id = null;
        }

        $correspond_post = Input::get('correspond_post');
        if(strlen($correspond_post) == 6)
        {
            $correspond_voivodeship_id = $matcher->match($correspond_post);
        }else{
            $correspond_voivodeship_id = null;
        }
        $old_client = Clients::find($injury->client_id);
        $client = Clients::create(array(
            'parent_id' => $injury->client_id,
            'firmID'    => Input::get('firmID'),
            'name' 		=> Input::get('name'),
            'NIP'		=> trim(str_replace('-', '', Input::get('NIP'))),
            'REGON'		=> Input::get('REGON'),
            'registry_post'		=> Input::get('registry_post'),
            'registry_city'		=> Input::get('registry_city'),
            'registry_street'	=> Input::get('registry_street'),
            'registry_voivodeship_id' => $registry_voivodeship_id,
            'correspond_post'	=> Input::get('correspond_post'),
            'correspond_city'	=> Input::get('correspond_city'),
            'correspond_street'	=> Input::get('correspond_street'),
            'correspond_voivodeship_id' => $correspond_voivodeship_id,
            'phone'				=> Input::get('phone'),
            'email'				=> Input::get('email')
        ));
        $injury->client_id = $client->id;
        $old_client->update(['active' => 1]);
        $injury->save();

        $info = [];
        foreach( array_diff($client->toArray(), $old_client->toArray()) as $changed_field => $changed_value){
            switch ($changed_field){
                case 'firmID':
                    $info[] = 'kod klienta: '.$old_client->firmID.' -> '.$changed_value;
                    break;
                case 'name':
                    $info[] = 'nazwa: ' . $old_client->name . ' -> ' . $changed_value;
                    break;
                case 'NIPNIP':
                    $info[] = 'NIP: ' . $old_client->NIP . ' -> ' . $changed_value;
                    break;
                case 'REGON':
                    $info[] = 'REGON: ' . $old_client->REGON . ' -> ' . $changed_value;
                    break;
                case 'registry_post':
                    $info[] = 'rej. kod pocztowy: '.$old_client->registry_post.' -> '.$changed_value;
                    break;
                case 'registry_city':
                    $info[] = 'rej. miasto: '.$old_client->registry_city.' -> '.$changed_value;
                    break;
                case 'registry_street':
                    $info[] = 'rej. ulica: '.$old_client->registry_street.' -> '.$changed_value;
                    break;
                case 'correspond_post':
                    $info[] = 'kont. kod pocztowy: '.$old_client->correspond_post.' -> '.$changed_value;
                    break;
                case 'correspond_city':
                    $info[] = 'kont. miasto: '.$old_client->correspond_city.' -> '.$changed_value;
                    break;
                case 'correspond_street':
                    $info[] = 'kont. ulica: '.$old_client->correspond_street.' -> '.$changed_value;
                    break;
                case 'phone':
                    $info[] = 'telefon: '.$old_client->phone.' -> '.$changed_value;
                    break;
                case 'email':
                    $info[] = 'email: '.$old_client->email.' -> '.$changed_value;
                    break;
                default: break;
            }
        }

        Histories::history($id, 154, Auth::user()->id, '-1', implode('; ', $info) );


        return json_encode(['code' => 0]);
    }

	public function setEditInjuryMap($id)
	{
		$injury = Injury::find($id);

		if( Input::get('if_map') ) $if_map = 1; else $if_map = 0;
		if( Input::get('if_map_correct') ) $if_map_correct = 1; else $if_map_correct = 0;

		$injury->if_map = $if_map;
		$injury->if_map_correct = $if_map_correct;
		$injury->lat = Input::get('lat');
		$injury->lng = Input::get('lng');

		$injury->event_city = Input::get('event_city');
		$injury->event_street = Input::get('event_street');

		Histories::history($id, 122, Auth::user()->id, Input::get('event_city').' '.Input::get('event_street'));

		if( $injury->save() ) echo 0;


	}


	public function setDateAdmission($id)
	{
		$injury = Injury::find($id);

		$injury->date_admission = Input::get('date_admission');
		$injury->task_pickup = 0;


		Histories::history($id, 123, Auth::user()->id, Input::get('date_admission'));

		if( $injury->save() ) echo 0;

	}

	public function setChangeContact($id)
	{
		$injury = Injury::find($id);
		if($injury->contact_person == 1)
			$injury->contact_person = 2;
		else
			$injury->contact_person = 1;


		if( $injury->save() ){
			Histories::history($id, 136, Auth::user()->id);
			echo 0;
		}
	}


	public function createHistory($id)
	{
		Histories::history($id, 128, Auth::user()->id, '<b>'.Input::get('content').'</b>');
		echo 0;
	}


	public function postEditInjuryInfo($id)
	{
		if( Input::get('info') != ''){
			$insert = Text_contents::create(array(
				'content' => Input::get('info')
			));

			$info_id = $insert->id;
		}else{
			$info_id = '0';
		}
		$injury = Injury::find($id);
		$injury->info = $info_id;
		$injury->save();

		Histories::history($id, 129, Auth::user()->id);
		echo 0;
	}



	public function postEditInjuryRemarks_damage($id)
	{
		if( Input::get('info') != ''){
			$insert = Text_contents::create(array(
				'content' => Input::get('info')
			));

			$info_id = $insert->id;
		}else{
			$info_id = '0';
		}
		$injury = Injury::find($id);
		$injury->remarks_damage = $info_id;
		$injury->save();

		Histories::history($id, 129, Auth::user()->id);
		echo 0;
	}

    public function getAccept($id)
    {
        $injuries_type = Injuries_type::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = Type_incident::orderBy('order')->get();
        $insurance_companies = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();

        $damages = MobileInjuryDamage::whereMobile_injury_id($id)->get();
        $damagesA = array();
        foreach($damages as $damage)
        {
            $damagesA[$damage->mobile_damage_type_id] = 1;
        }

        $damages_type = MobileDamageType::all();
        $damages_typeA = array();
        foreach($damages_type as $damage_type)
        {
            $damages_typeA[$damage_type->id] = $damage_type;
        }

        $pictures = MobileInjuryFile::whereMobile_injury_id($id)->get();

        $injury = MobileInjury::find($id);

        $damage = Damage_type::all();
        $ct_damage = count($damage);

        $description = preg_replace('/\<br(\s*)?\/?\>/i','&#13;&#10;',$injury->desc_event);

        $description .= '&#13;&#10;&#13;&#10;Dane klienta: '.$injury->name_client.'; '.$injury->code_client.' '.$injury->city_client.', '.$injury->adres_client;
        if($injury->nip != '')
            $description .= '; NIP:'.$injury->nip;

        if($injury->company != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Warsztat: '.$injury->company;
        }
        if($injury->injuries_type > 0)
        {
            $description .= '&#13;&#10;&#13;&#10;Typ szkody: ';
            switch ($injury->injuries_type){
                case 2:
                    $description .='komunikacyjna OC';
                    break;
                case 1:
                    $description .='komunikacyjna AC';
                    break;
                case 3:
                    $description .='komunikacyjna kradzież';
                    break;
                case 4:
                    $description .='majątkowa';
                    break;
                case 5:
                    $description .='majątkowa kradzież';
                    break;
            }

        }

        if($injury->name_zu != '')
        {
            $description .= '&#13;&#10;&#13;&#10;Ubezpieczyciel: '.$injury->name_zu;
        }

        if($injury->police_unit != '' || $injury->nr_case != '' || $injury->policeman_phone)
        {
            $police = 1;
        }else{
            $police = -1;
        }

        return View::make('injuries.create-mobile', compact('police', 'description','damage', 'ct_damage','injury', 'injuries_type', 'receives', 'type_incident', 'insurance_companies', 'invoicereceives', 'damagesA', 'damages_typeA', 'pictures'));
    }

    public function setTotalStatus($id, $status_id)
    {
        $injury = Injury::find($id);
        $injury->total_status_id = $status_id;

        InjuryStatusesHistory::create([
            'injury_id' => $injury->id,
            'user_id'   => Auth::user()->id,
            'status_id' => $status_id,
            'status_type' => 'InjuryTotalStatuses'
        ]);


        if( $injury->save() ){
            $result['code'] = 0;
            return json_encode($result);
        }
    }

    public function getLiquidationCard()
    {
        \Debugbar::disable();
        $result = array();
        if(Input::has('vehicle_id') && Input::get('vehicle_id') != '') {
            $vehicles = DB::select(DB::raw('
				SELECT T2.id
				FROM (
				    SELECT
				        @r AS _id,
				        (SELECT @r := parent_id FROM vehicles WHERE id = _id) AS parent_id,
				        @l := @l + 1 AS lvl
				    FROM
				        (SELECT @r := ' . Input::get('vehicle_id') . ', @l := 0) vars,
				        vehicles h
				    WHERE @r <> 0) T1
				JOIN vehicles T2
				ON T1._id = T2.id
				ORDER BY T1.lvl DESC
			'));


            $vehiclesA = array_map(
                function ($oObject) {
                    $aConverted = get_object_vars($oObject);
                    return $aConverted['id'];
                },
                $vehicles);


            $cardsA = LiquidationCards::whereIn('vehicle_id', $vehiclesA)->get();


            if (!$cardsA->isEmpty()) {
                $result['exists'] = 1;
                $result['dataHtml'] = 'pojazd posiada kartę o nr <b>' . $cardsA->first()->number . '</b> ważną do dnia <b';

                $expiration_date = new DateTime($cardsA->first()->expiration_date);
                $now = new DateTime(date('Y-m-d'));

                if ($expiration_date < $now)
                    $result['dataHtml'] .= ' style="color:red;"';

                $result['dataHtml'] .= '>' . $cardsA->first()->expiration_date . '</b>';
            }
        }
        return json_encode($result);

    }

    public function postEditVehicle($id)
    {
        $vehicle = Injury::find($id)->vehicle()->first();
        if( Input::has('cfm')){
            $vehicle->cfm = Input::get('cfm');
        }else{
            $vehicle->cfm = '0';
        }

        $vehicle->registration = Input::get('registration');
        $vehicle->nr_contract = Input::get('nr_contract');
        $vehicle->VIN = Input::get('VIN');
        $vehicle->brand = Input::get('brand');
        $vehicle->model = Input::get('model');
        $vehicle->year_production = Input::get('year_production');
        $vehicle->engine = Input::get('engine');
        $vehicle->mileage = Input::get('mileage');
        $vehicle->first_registration = Input::get('first_registration');
        
        if(Input::has('register_as')) {
            $vehicle->register_as = Input::get('register_as');
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
                case 'nr_contract':
                    $info[] = 'numer umowy: '.$vehicle->getOriginal('nr_contract').' -> '.$changed_value;
                    break;
                case 'VIN':
                    $info[] = 'VIN: '.$vehicle->getOriginal('VIN').' -> '.$changed_value;
                    break;
                case 'brand':
                    $info[] = 'marka: '.$vehicle->getOriginal('brand').' -> '.$changed_value;
                    break;
                case 'model':
                    $info[] = 'model: '.$vehicle->getOriginal('model').' -> '.$changed_value;
                    break;
                case 'year_production':
                    $info[] = 'rok produkcji: '.$vehicle->getOriginal('year_production').' -> '.$changed_value;
                    break;
                case 'engine':
                    $info[] = 'silnik: '.$vehicle->getOriginal('engine').' -> '.$changed_value;
                    break;
                case 'mileage':
                    $info[] = 'przebieg: '.$vehicle->getOriginal('mileage').' -> '.$changed_value;
                    break;
                case 'first_registration':
                    $info[] = 'data pierwszej rejestracji: '.$vehicle->getOriginal('first_registration').' -> '.$changed_value;
                    break;
                case 'register_as':
                    $info[] = 'rejestrowany w as: '.($vehicle->getOriginal('register_as') == 0 ? 'nie' : 'tak').' -> '.($changed_value == 0 ? 'nie' : 'tak');
                    break;
                default: break;
            }
        }

        $vehicle->save();

        Histories::history($id, 153, Auth::user()->id, '-1', implode('; ', $info) );
		Flash::success('Dane pojazdu zostały zaktualizowane pomyślnie.');
        echo 0;
    }

	public function postEditVehicleOwner($id)
    {
        $vehicle = Injury::find($id)->vehicle()->first();

        $owner = $vehicle->owner;

        $vehicle->owner_id = Input::get('owner_id');
        $vehicle->register_as = Input::get('register_as');

        $changes = $vehicle->getDirty();
        $info = [];
        foreach($changes as $changed_field => $changed_value) {
            switch ($changed_field) {
                case 'owner_id':
                    $info[] = 'właściciel: ' . $owner->name . ' -> ' . $vehicle->owner->name;
                    break;
                case 'register_as':
                    $info[] = 'ejestrowany w AS: ' . ($vehicle->getOriginal('register_as') == 1 ? 'tak' : 'nie') . ' -> ' . ($changed_value == 1 ? 'tak' : 'nie');
                    break;
            }
        }

        $vehicle->save();

        Histories::history($id, 160, Auth::user()->id, '-1', implode('; ', $info));

		Flash::success('Właściciela pojazdu zmieniono pomyślnie.');
        echo 0;
    }

    public function setCompensation($id)
    {
        $compensation = InjuryCompensation::find($id);
        $injury = Injury::find($compensation->injury_id);

        $dec_date = Input::get('date_decision');
        Input::merge(['date_decision' => empty($dec_date)?null:$dec_date]);
        
        $compensation->update(Input::all());
        $compensation->save();

        if($injury->sap) {
            $sap = new Idea\SapService\Sap();

            $sap_stanszk = false;
            if (!in_array($injury->sap_rodzszk, ['TOT', 'KRA']) && (int)$injury->sap_stanszk < 2) {
                $sap_stanszk = true;
                $injury->update([
                   'sap_stanszk' => 2
                ]);
                $injury = Injury::find($compensation->injury_id);
            }

            if($compensation->injury_compensation_decision_type_id == 7) {
                $compensation_value = abs($compensation->compensation) * -1;
            }else{
                $compensation_value = $compensation->compensation;
            }

            if ($compensation->decisionType && $compensation_value != 0) {
                if($compensation->note ){
                    $notesToRemove[0] = $compensation->note;
                    $result = $sap->szkodaNotKasuj($injury, $notesToRemove);
                    if(isset($result['ftNotatkaKeys'])){
                        foreach($result['ftNotatkaKeys'] as $notatkaKey){
                            InjuryNote::where('injury_id', $injury->id)->where('roknotatki', $notatkaKey['roknotatki'])->where('nrnotatki', $notatkaKey['nrnotatki'])->delete();
                        }
                    }else{
                        Flash::error('Wystąpił błąd w trakcie usuwania notatek.');
                    }
                }

                $notes[0] = $compensation->decisionType->short_name . ';odb. odszk.:'.checkIfEmpty('name', $compensation->receive()->get()).';kwota: '.number_format($compensation_value, 2, ",", " ").' '.checkIfEmpty(Config::get('definition.compensationsNetGross.'.$compensation->net_gross)).'; Data dec.: '.(is_null($compensation->date_decision)?null:Carbon\Carbon::createFromFormat('Y-m-d', $compensation->date_decision)->format('Y-m-d'));

                $result = $sap->szkodaNotUtworz($injury, $notes);

                $errors = [];
                if(isset($result['ftReturn']) && is_array($result['ftReturn'])){
                    foreach($result['ftReturn'] as $ftReturn){
                        if($ftReturn['typ'] =='E'){
                            $errors[] = $ftReturn;
                        }
                    }
                }

                if(count($errors) > 0){
                    Flash::error('Wystąpił błąd w trakcie wysyłki notatek.');
                }else{
                    foreach($result['ftNotatkaN'] as $note_item => $note){
                        $injuryNote = InjuryNote::create([
                            'referenceable_id' => $compensation->id,
                            'referenceable_type' => 'InjuryCompensation',
                            'injury_id' => $injury->id,
                            'user_id' => Auth::user()->id,
                            'roknotatki' => $note['roknotatki'],
                            'nrnotatki'=> $note['nrnotatki'],
                            'obiekt'=> $note['obiekt'],
                            'temat'=> $note['temat'],
                            'data'=> $note['data'],
                            'uzeit'=> $note['uzeit'],
                        ]);

                        $compensation->note()->associate($injuryNote);
                        $compensation->save();
                    }
                }
            }

            if($compensation->mode == 1 || $sap_stanszk || ($compensation->decisionType && $compensation_value != 0)){
                $result = $sap->szkoda($injury);
                if($result['status'] == 200){
                    Flash::message('Szkoda zaktualizowana w SAP');
                }else {
                    Session::flash('show.modal.in.the.next.request', $result['msg']);
                }

                if(in_array( $result['status'], [200, 300])){
                    Histories::history($injury->id, 217, Auth::user()->id);
                }
            }
        }else{
            $compensation->update(['is_premiumable' => 1]);
        }

        if($injury->wreck) {
            $wreck = $injury->wreck;
            if (Input::get('injury_compensation_decision_type_id') == 2 && $wreck->value_compensation == 0) {
                $sum_compensation = InjuryCompensation::where('injury_id', $compensation->injury_id)->where('injury_compensation_decision_type_id', 2)->sum('compensation');
                $wreck->value_compensation = $sum_compensation;
                $wreck->compensation_description = 1;
                $wreck->compensation_description_date = $compensation->date_decision;
                $wreck->save();
            } elseif (Input::get('injury_compensation_decision_type_id') == 3 && $wreck->extra_charge_ic == 0) {
                $sum_compensation = InjuryCompensation::where('injury_id', $compensation->injury_id)->where('injury_compensation_decision_type_id', 3)->sum('compensation');
                $wreck->extra_charge_ic = $sum_compensation;
                $wreck->extra_charge_ic_description = 1;
                $wreck->extra_charge_ic_description_date = $compensation->date_decision;
                $wreck->save();
            }
        }


        // Histories::history($compensation->injury_id, 155, Auth::user()->id);
        Flash::success("Dane odszkodowania zostały zaktualizowane");

        $result['code'] = 0;
        return json_encode($result);
    }

		public function setEstimate($id)
		{
				$estimate = InjuryEstimate::find($id);
				$estimate->update(Input::all());
				$estimate->save();

				if($estimate->report){
					InjuryEstimate::where('injury_id',$estimate->injury_id)->where('report',1)->where('id','!=',$estimate->id)->update(['report'=>0]);
				}

				Histories::history($estimate->injury_id, 192, Auth::user()->id);
				Flash::success("Dane kosztorysu zostały zaktualizowane");

				$result['code'] = 0;
				return json_encode($result);
		}


    public function getMatchedLetters()
    {
        \Debugbar::disable();
        $result = array();

        if(Input::has('vehicle_id') && Input::get('vehicle_id') != '') {
            $data = Input::all();
            $vehicle = $data['vehicle_type']::find(Input::get('vehicle_id'));

            $letters = InjuryLetter::whereNull('injury_file_id')->where(function($query) use ($vehicle){
                if($vehicle->nr_contract != '') {
                    $query->orWhere(function ($subquery) use ($vehicle) {
                        $subquery->whereNotNull('nr_contract')->where('nr_contract', $vehicle->nr_contract);
                    });
                }
                if($vehicle->registration != '') {
                    $query->orWhere(function ($subquery) use ($vehicle) {
                        $subquery->whereNotNull('registration')->where('registration', $vehicle->registration);
                    });
                }
            })->get();

            if(! $letters->isEmpty())
            {
                $result['matched'] = 1;
                $result['dataHtml'] = '
				<table class="table table-hover table-condensed">
					<thead>
					    <Th></Th>
					    <Th></Th>
						<Th>typ dokumentu</Th>
                        <th>nazwa pisma</th>
                        <th>nr szkody</th>
                        <th>nr umowy</th>
                        <th>nr rejestracyjny</th>
						<th></th>
					</thead>';
                foreach($letters as $letter)
                {
                    $result['dataHtml'] .= '
					<tr class="vertical-middle">
					    <td><a href="'.URL::route('routes.get', ['injuries', 'letters', 'download', $letter->id]).'" class="btn btn-sm btn-success " off-disable><i class="fa fa-download"></i> pobierz</a> </td>
					    <td>';
                            if( trim($letter->description) != '')
                                $result['dataHtml'] .= '<a tabindex="0" class="btn btn-sm btn-info btn-popover" role="button" data-toggle="popover" data-trigger="focus" title="Opis pisma" data-content="'.$letter->description.'"><i class="fa fa-info-circle"></i> opis</a>';

                    $result['dataHtml'] .= '</td>
						<td>'.$letter->uploadedDocumentType->name.'</td>
                        <td>'.$letter->name.'</td>
                        <td>'.$letter->injury_nr.'</td>
                        <td>'.$letter->nr_contract.'</td>
                        <td>'.$letter->registration.'</td>
                        <td class="text-center">
                            <label>
                              przypisz do zgłoszenia <input type="checkbox" name="matchedLetters[]" value="'.$letter->id.'">
                            </label>
                        </td>
                    </tr>';
                }
                $result['dataHtml'] .= '</table>';
                $result['count'] = $letters->count();
            }else{
                $result['matched'] = 0;
            }
        }else{
            $result['matched'] = 0;
        }
        return json_encode($result);

    }

    public function letters(){
        $letters = InjuryLetter::whereNull('injury_file_id')
            ->where(function($query){
                if(Request::has('document_type_id'))
                {
                    $query->where('category', Request::get('document_type_id'));
                }
            })
            ->with('user', 'uploadedDocumentType')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;
        $injuries_type = Injuries_type::all();
        $users = User::where('active','=',0)->get();
        $step = 0;

        $uploadedDocumentTypes = InjuryUploadedDocumentType::with('subtypes')->orderBy('ordering')->get();
        return View::make('injuries.letters', compact('letters', 'counts', 'injuries_type', 'users', 'step', 'uploadedDocumentTypes'));
    }

    private function passingWheres(&$query)
    {
        $query->where(function($query2){

            if(Input::has('registration')){
                $query2 ->vehicleExists('registration', Input::get('term'));
            }

            if(Input::has('leasing_nr')){
                $query2 ->vehicleExists('nr_contract', Input::get('term'));
            }

            if(Input::has('VIN')){
                $query2 ->vehicleExists('VIN', Input::get('term'));
            }

            if(Input::has('case_nr')){
                $query2 -> orWhere('case_nr', 'like', '%'.Input::get('term').'%');
            }

            if(Input::has('injury_nr')){
                $query2 -> orWhere('injury_nr', 'like', '%'.Input::get('term').'%');

                $query2 -> orWhereHas('injuryGap', function($query){
                    $query->where('injury_number', 'like', '%'.Input::get('term').'%');
                });
            }


            if(Input::has('address')){
                $query2 -> orWhere('event_city', 'like', '%'.Input::get('term').'%');
                $query2 -> orWhere('event_post', 'like', '%'.Input::get('term').'%');
                $query2 -> orWhere('event_street', 'like', '%'.Input::get('term').'%');
            }

            if(Input::has('surname')){
                $query2 -> orWhere('notifier_surname', 'like', '%'.Input::get('term').'%');

                $query2 -> orWhereHas('driver', function($q)
                {
                    $q -> where('surname', 'like', '%'.Input::get('term').'%');
                });
            }

            if(Input::has('client')){
                $query2 -> orWhereHas('client', function($q)
                {
                    $q -> where('name', 'like', '%'.Input::get('term').'%');
                });
            }
            if(Input::has('NIP')){
                $query2 -> orWhereHas('client', function($q)
                {
                    $q -> where('NIP', 'like', '%'.Input::get('term').'%');
                });
            }

            if(Input::has('firmID')){
                $query2 -> orWhereHas('client', function($q)
                {
                    $q -> where('firmID', 'like', '%'.Input::get('term').'%');
                });
            }

            if(Input::has('invoice_number')){
                $query2->orWhereHas('invoices', function($query){
                    $query->where('invoice_nr',  Input::get('term'));
                });
            }
        });
    }

    private function compareClients($client, $xml)
    {
        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

        $registry_post = $xml->customer->address->postalCode->__toString();
        if(strlen($registry_post) == 6)
        {
            $registry_voivodeship_id = $matcher->match($registry_post);
        }else{
            $registry_voivodeship_id = null;
        }

        $correspond_post = $xml->customer->mailAddress->postalCode->__toString();
        if(strlen($correspond_post) == 6)
        {
            $correspond_voivodeship_id = $matcher->match($correspond_post);
        }else{
            $correspond_voivodeship_id = null;
        }

        if(
            $client->firmID != $xml->customer->firmID->__toString() ||
            $client->name != $xml->customer->name->__toString() ||
            $client->NIP != trim(str_replace('-', '', $xml->customer->NIP->__toString())) ||
            $client->REGON != trim($xml->customer->REGON->__toString()) ||
            $client->registry_post != $xml->customer->address->postalCode->__toString() ||
            $client->registry_city != $xml->customer->address->city->__toString() ||
            $client->registry_street != $xml->customer->address->street->__toString() ||
            $client->registry_voivodeship_id != $registry_voivodeship_id ||
            $client->correspond_post != $xml->customer->mailAddress->postalCode->__toString() ||
            $client->correspond_city != $xml->customer->mailAddress->city->__toString() ||
            $client->correspond_street != $xml->customer->mailAddress->street->__toString() ||
            $client->correspond_voivodeship_id != $correspond_voivodeship_id ||
            $client->phone != $xml->customer->phone->__toString() ||
            $client->email != $xml->customer->email->__toString()
        ){
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
        }

        return $client->id;
    }

    public function uploadUnprocessed()
    {
        return View::make('injuries.upload-unprocessed');
    }

    public function proceedUnprocessed()
    {
        $file = Input::file('file');
        $extension = $file->getClientOriginalExtension();

        $filename = time().'.'.$extension;
        $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/imports/injuries/";
        $file->move($destinationPath, $filename);

        $file = $destinationPath.$filename;

        $reader = Excel::load($file);
        $worksheet = $reader->getSheet(0);
        $highest_row = $worksheet->getHighestRow();
        $highest_column = $worksheet->getHighestColumn();
        $rowData = $worksheet->rangeToArray('A2:' . $highest_column.$highest_row ,
            NULL,
            TRUE,
            FALSE);
        $i = 0;
        foreach($rowData as $row)
        {
            $notifier = explode(' ', $row[4]);
            $injuries_type_id = '';
            if($row[9] == 'AUTO-CASCO'){
                $injuries_type_id = 1;
            }elseif($row[9] == 'OC SPRAWCY'){
                $injuries_type_id = 2;
            }elseif($row[9] == 'AC/REGRES'){
                $injuries_type_id = 4;
            }
            if($row[6] || $row[23]) {
                $mobileInjury = MobileInjury::create([
                    'registration' => $row[6] ? $row[6] : '',
                    'nr_contract' => $row[23] ? $row[23] : '',
                    'notifier_surname' => isset($notifier[1]) ? $notifier[1] : '',
                    'notifier_name' => isset($notifier[0]) ? $notifier[0] : '',
                    'notifier_phone' => $row[2] ? $row[2] : '',
                    'notifier_email' => $row[3] ? $row[3] : '',
                    'injuries_type_id' => $injuries_type_id,
                    'marka' => $row[5] ? $row[5] : '',
                    'model' => $row[5] ? $row[5] : '',
                    'name_zu' => $row[9] ? $row[9] : '',
                    'date_event' => date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($row[4])),
                    'event_city' => $row[7] ? $row[7] : '',
                    'nr_injurie' => $row[12] ? $row[12] : '',
                    'desc_event' => $row[8] . '; ' . $row[13] . '; nr polisy:' . $row[20] . '; nr polisy sprawcy: ' . $row[21],
                    'company' => $row[15] . ' ' . $row[16],
                    'active' => 0,
                    'source' => 3,
                    'if_on_as_server' => 0,
                    'created_at' => date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($row[4]))
                ]);

                $group_name = '';
                if( ($mobileInjury->source == 0 || $mobileInjury->source == 3)  && $mobileInjury->injuries_type()->first()) {
                    $group_name = $mobileInjury->injuries_type()->first()->name;
                }else {
                    if ($mobileInjury->injuries_type == 2)
                        $group_name = 'komunikacyjna OC';
                    elseif($mobileInjury->injuries_type == 1)
                        $group_name = 'komunikacyjna AC';
                    elseif($mobileInjury->injuries_type == 3)
                        $group_name = 'komunikacyjna kradzież';
                    elseif($mobileInjury->injuries_type == 4)
                        $group_name = 'majątkowa';
                    elseif($mobileInjury->injuries_type == 5)
                        $group_name = 'majątkowa kradzież';
                    elseif($mobileInjury->injuries_type == 6)
                        $group_name = 'komunikacyjna AC - Regres';
                   }

                if (strpos($group_name, 'kradzież') !== false) {
                    $task_group_id = 3;
                }else{
                    $task_group_id = 1;
                }

                $task = Task::create([
                    'task_source_id' => 2, //druk online
                    'from_email' => $mobileInjury->notifier_email,
                    'from_name' => $mobileInjury->notifier_name.' '.$mobileInjury->notifier_surname,
                    'subject' => $mobileInjury->nr_contract.' # '.$mobileInjury->registration,
                    'content' => $mobileInjury->description(),
                    'task_group_id' => $task_group_id,
                    'task_date' => $mobileInjury->created_at
                ]);

                $mobileInjury->tasks()->save($task);

                \Idea\Tasker\Tasker::assign($task);
            }
            $i++;
        }

        Flash::success('Przetworzono '.$i.' spraw');
        return Response::json(['status' => 'success', 'rows' => $i]);
    }

    public function setInvoiceForward($invoice_id)
    {   
        $invoice = InjuryInvoices::with('injury_files')->find($invoice_id);
        $forward_confirmed = Input::get('forward_confirmed');

        if(Input::get('allow_confirmed') == 1 || Input::get('allow_confirmed') == 'true'){
            $this->forwardInvoice($invoice);
            return Response::json(['status'=>200, 'message'=>'ok'], 200);
        }

        $verified = false;
        $vat_check_company = $invoice->branch;
        
        if(!is_null($vat_check_company) ){
            $vat = new \Idea\Vat\Vat();

            $accounts_local = $invoice->assignedBankAccountNumbers;

            $accounts_response = json_decode($vat->checkClient($vat_check_company->company->nip),true);
            $status_response = json_decode($vat->checkNip($vat_check_company->company->nip),true);
            if($status_response['code'] == 'C'){
                
                if($forward_confirmed){
                    $this->forwardInvoice($invoice); 
                    return Response::json(['status'=>200, 'message'=>'ok'], 200);
                }

                if(!isset($accounts_response['data'])){
                    $context = json_encode([$accounts_response, $status_response, $invoice_id]);
                    $body = View::make('emails.errors.error_simple', compact('context'))->render();
                    $mailer = new \Idea\Mail\Mailer();
                    $mailer->addAddress('przemek@webwizards.pl');
                    $mailer->setSubject('[IdeaLeasing] Error Invoice Forwards.');
                    $mailer->setBody($body);
                    $mailer->setTimeout(5);
                    $mailer->send();

                    return Response::json(['status'=>500, 'message'=> 'Wystąpił błąd w systemie sprawdzania kont bankowych.'] ,200);
                }
                $data=$accounts_response['data'];
                $accounts_local_arr = [];

                foreach($accounts_local as $object) {
                       $accounts_local_arr[] = $object->account_number;
                }
                
                if(count(array_intersect($accounts_local_arr, $data[0]['accountNumbers']))== count($accounts_local_arr)){
                    Log::info('bank accounts verfied');
                    $verified = true;
                }

                if($verified){
                    $this->forwardInvoice($invoice);                  
                    return Response::json(['status'=>200, 'message'=>$status_response['message']],200);
                } else {
                    return Response::json(['status'=>500, 'message'=>'Wybrano rachunek z poza białej listy. Czy na pewno chcesz przekazać FV?',
                    'accounts_local'=>$accounts_local_arr,
                    'accounts_white_list'=>$data[0]['accountNumbers']],
                    200);
                }
            } else {
                return Response::json(['status'=>500, 'message'=>$status_response['message']],200);
            }
        }elseif($invoice->injury->branch_id > 0 && $invoice->injury->branch->company->groups->count() == 0){
            if($forward_confirmed){
                $this->forwardInvoice($invoice);
                return Response::json(['status'=>200, 'message'=>'ok'], 200);
            }
        }
        return Response::json(['status'=>500, 'message'=>'Dla serwisu przypisanego do faktury status płatnika nie został zweryfikowany.'],200);
    }

    private function forwardInvoice($invoice){
        $documentTypes = Input::get('document_types');
            if($invoice->injury_files->category == 3 && Input::get('document_types')) {
                foreach ($documentTypes as $dT) {
                    InjuryInvoiceForwardDocument::create([
                        'injury_invoice_id' => $invoice->id,
                        'injury_invoice_forward_document_type_id' => $dT
                    ]);
                }
                if(in_array(2, $documentTypes)) $invoice->compensations()->sync(Input::get('compensations',[]));
                else $invoice->compensations()->detach();  
            }
            $invoice->update([
                'injury_invoice_status_id' => 1,
                // 'company_vat_check_id' => $invoice->injury->branch ? $invoice->injury->branch->company->company_vat_check_id : null,
                'forward_date' => date('Y-m-d H:i:s')
            ]);       

            Histories::history($invoice->injury_id, 208, Auth::user()->id, '-1', '<a href="'.URL::route("injuries-downloadDoc", array($invoice->injury_files_id)) .'" target="_blank">pobierz</a>');
    }

    public function setInvoiceReturn($invoice_id)
    {
        $invoice = InjuryInvoices::find($invoice_id);

        $invoice->update(['injury_invoice_status_id' => 2]);

        Histories::history($invoice->injury_id, 209, Auth::user()->id, '-1', '<a href="'.URL::route("injuries-downloadDoc", array($invoice->injury_files_id)) .'" target="_blank">pobierz</a>');

        echo 0;
    }

    public function setInvoiceForwardAgain($invoice_id)
    {
        $invoice = InjuryInvoices::with('injury_files')->find($invoice_id);

        if ($invoice->injury_files->category == 3 && Input::get('document_types')){
            $dbInvoiceDocumentTypes = $invoice->injuryInvoiceForwardDocuments->lists('injury_invoice_forward_document_type_id');
            $documentTypes = Input::get('document_types');
            foreach ($documentTypes as $dT) {
                if (!in_array($dT, $dbInvoiceDocumentTypes)) {
                    InjuryInvoiceForwardDocument::create([
                        'injury_invoice_id' => $invoice_id,
                        'injury_invoice_forward_document_type_id' => $dT]);
                }
            }
            foreach ($dbInvoiceDocumentTypes as $db) {
                if (!in_array($db, $documentTypes)) {
                    $invoiceDocument = InjuryInvoiceForwardDocument::where('injury_invoice_id', $invoice_id)
                        ->where('injury_invoice_forward_document_type_id', $db)->first();
                    $invoiceDocument->delete();
                }
            }
            
            if(in_array(2, $documentTypes)) $invoice->compensations()->sync(Input::get('compensations',[]));
            else $invoice->compensations()->detach();
        }

        $invoice->update(['injury_invoice_status_id' => 3, 
        // 'company_vat_check_id' => $invoice->injury->branch ? $invoice->injury->branch->company->company_vat_check_id : null,
        'forward_again_date' => date('Y-m-d H:i:s')]);

        Histories::history($invoice->injury_id, 210, Auth::user()->id, '-1', '<a href="'.URL::route("injuries-downloadDoc", array($invoice->injury_files_id)) .'" target="_blank">pobierz</a>');

        echo 0;
    }

    public function getResetFilters()
    {
        Session::forget('search');

        return Redirect::back();
    }

    public function storeCessionAmounts($id){
        $cessionAmount = new InjuryCessionAmount();
        $cessionAmount->injury_id = $id;
        $cessionAmount->paid_amount = Input::get('paid_amount');
        $cessionAmount->net_gross = Input::get('net_gross');
        $cessionAmount->fv_amount = Input::get('fv_amount');
        $cessionAmount->save();

        Flash::success("Kwoty do cesji zostały przypisane");

        $result['code'] = 0;
        return json_encode($result);
    }

    public function updateCessionAmounts($id){
        $cessionAmount = InjuryCessionAmount::find($id);
        $cessionAmount->paid_amount = Input::get('paid_amount');
        $cessionAmount->net_gross = Input::get('net_gross');
        $cessionAmount->fv_amount = Input::get('fv_amount');
        $cessionAmount->save();

        Flash::success("Kwoty do cesji zostały zmienione");

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getIndexEa($name_key = null)
    {
        $users = User::where('active','=',0)->get();

        $injuries_type = Injuries_type::all();

        $query = EaInjury::orderBy('id','desc');

        if(Input::has('term')){

            $query->where(function($query2){

                if(Input::has('registration')){
                    $query2 -> orWhere('vehicle_registration', 'like', '%'.Input::get('term').'%');
                }

                if(Input::has('leasing_nr')){
                    $query2 -> orWhere('contract_number', 'like', '%'.Input::get('term').'%');
                }

                if(Input::has('address')){
                    $query2 -> orWhere('injury_event_city', 'like', '%'.Input::get('term').'%');
                }

                if(Input::has('surname')){
                    $query2 -> orWhere('claimant_surname', 'like', '%'.Input::get('term').'%');
                }

            });
        }


        if(! $name_key){
            $query->whereNull('sales_program');
        }else {
            $query->where('sales_program', $name_key);
        }

        $injuries = $query->paginate(Session::get('search.pagin', '10'));

        $counts = $this->counts;

        $step = '-100';

        $sales_programs = DB::table('ea_injuries')
                                ->select('sales_program', DB::raw('count(*) as total'))
                                ->groupBy('sales_program')
                                ->lists('total','sales_program');

        return View::make('injuries.ea', compact('injuries', 'users', 'counts', 'step', 'injuries_type', 'sales_programs'));
    }

    public function getInvoiceBankAccounts($invoiceId, $withTrashed = 0){
        $invoice = InjuryInvoices::findOrFail($invoiceId);
        if($withTrashed) return $invoice->assignedBankAccountNumbersWithTrashed;
        return $invoice->assignedBankAccountNumbers;
    }

    public function setRemovePremium($id)
    {
        $premium = InjurySapPremium::find($id);
        $premium->delete();

       //todo dodać do historii

        $sap = new Idea\SapService\Sap();
        $sap->szkoda($premium->injury);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function postInjuryBranchesHistory($injury_id)
    {
        $injury = Injury::find($injury_id);

        $injuryBranch = new InjuryBranch();

        $injuryBranch->injury_id = $injury->id;
        $injuryBranch->branch_id = Input::get('branch_id');
        $injuryBranch->user_id = Auth::user()->id;
        $injuryBranch->created_at = Input::get('created_at') . ' 00:00:00';
        $injuryBranch->updated_at = Input::get('created_at') . ' 00:00:00';

        $injuryBranch->save(['timestamps' => false]);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getInvoiceForwardDocuments($invoiceId){
        $invoice = InjuryInvoices::with('injuryInvoiceForwardDocuments.type', 'compensations')->findOrFail($invoiceId);
        foreach ($invoice->injuryInvoiceForwardDocuments as $doc) {
            if ($doc->injury_invoice_forward_document_type_id == 2) $doc->compensations = $invoice->compensations;
        }
        return $invoice->injuryInvoiceForwardDocuments;
    }

    public function updateSyjonClient($injury_id)
    {
        $injury = Injury::find($injury_id);
        $vehicle = $injury->vehicle;
        $syjonService = new \Idea\SyjonService\SyjonService();
        $syjon_contract = json_decode( $syjonService->loadContract($vehicle->syjon_contract_id) )->data;

        $object_user = $syjon_contract->object_user;
        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

        $nip = $object_user->contractor_nip;
        $regon = $object_user->contractor_regon;

        $registry_post = $object_user->contractor_office_post_code;
        $registry_voivodeship_id = $matcher->match($registry_post);

        $correspond_post = $object_user->contractor_office_correspondence_post_code;
        $correspond_voivodeship_id = $matcher->match($correspond_post);

        $phones = implode(',', (array) $object_user->contractor_office_phone);

        $client = $injury->client()->first();

        $client->update([
            'name' => $object_user->contractor_name ,
            'firmID' => $object_user->contractor_code_client,
            'NIP' => $nip,
            'REGON' => $regon,
            'registry_post' => $registry_post,
            'registry_city' => $object_user->contractor_office_city,
            'registry_street' => $object_user->contractor_office_street,
            'registry_voivodeship_id' => $registry_voivodeship_id,
            'correspond_post' => $correspond_post,
            'correspond_city' => $object_user->contractor_office_correspondence_city,
            'correspond_street' => $object_user->contractor_office_correspondence_street,
            'correspond_voivodeship_id' => $correspond_voivodeship_id,
            'phone' => $phones,
            'email' => $object_user->contractor_office_email,
        ]);

        return Redirect::back();
    }
}
