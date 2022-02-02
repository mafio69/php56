<?php

class CompaniesController extends BaseController
{

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
		$this->beforeFilter('permitted:serwisy#wejscie', ['getIndex']);
		$this->beforeFilter('permitted:serwisy#dodaj_firme', ['only' => ['getCreate', 'postStore']]);
		$this->beforeFilter('permitted:serwisy#stworz_grupe', ['only' => ['getCreateGroup', 'postStoreGroup']]);
		$this->beforeFilter('permitted:serwisy#zarzadzaj', ['only' => ['getEditGroup', 'postUpdateGroup', 'getCommissions', 'postCommissions', 'getEdit', 'postUpdate', 'getGroups', 'postGroups', 'getDelete', 'postDelete']]);
		$this->beforeFilter('permitted:mapa_serwisow#wejscie', ['only' => ['getMap']]);
	}

	public function getIndex($group_id = null)
	{
		if (!$group_id) {
			$group = null;
		} else {
			$group = CompanyGroup::with('owners')->findOrFail($group_id);
		}

		$groups = CompanyGroup::lists('name', 'id');
		$groups[0] = 'Wszystkie warsztaty';
		ksort($groups);

		$query = Company::with('branches', 'branches.typegarages', 'contractorGroup');

        if ($group_id) {
            $query->whereHas('groups', function ($query) use ($group_id) {
                $query->where('company_group_id', $group_id);
            });
        }

        if (Session::has('search_company') && Session::get('search_company') != '') {
            $query->where(function ($query) {
                $query->whereHas('branches', function ($query) {
                    $query->where(function ($query) {
                        $term = Session::get('search_company');
                        $query->where('city', 'like', '%' . $term . '%')
                            ->orWhere('short_name', 'like', '%' . $term . '%')
                            ->orWhere('street', 'like', '%' . $term . '%');
                    });
                });
                $term = Session::get('search_company');
                $query->orWhere('name', 'like', '%' . $term . '%');
            });
            Session::forget('search_company');
        }

		$companies = $query->orderBy('name')
            ->with('companyVatCheck')
			->paginate(Session::get('search.pagin', '10'));

		$typegaragesA = array();
		foreach ($companies as $k => $company) {
			$company->hasTug = 0;
			$company->hasTug24h = 0;
			foreach ($company->branches as $k2 => $branch) {

				if ($branch->tug == 1) $company->hasTug = 1;
				if ($branch->tug24h == 1) $company->hasTug24h = 1;

				foreach ($branch->typegarages as $k => $v) {
					$typegaragesA[$company->id][$v->typegarages_id] = 1;
				}
			}
		}

		$owners = Owners::whereActive(0)->lists('name', 'id');
		return View::make('companies.index', compact('companies', 'typegaragesA', 'group', 'groups', 'owners'));
	}

	public function postSearch($group_id = '')
	{
		Session::put('search_company', Input::get('term'));
		return Redirect::to('/companies/index/' . $group_id);
	}

	public function getCreate()
	{
		$groups = CompanyGroup::lists('name', 'id');
        $contractorGroups = ContractorGroup::lists('name', 'id');
        $contractorGroups = ['' => '--- wybierz ---']+ $contractorGroups;
		return View::make('companies.create', compact('groups', 'contractorGroups'));
	}

	public function postStore()
	{
		$input = Input::all();

		$validator = Validator::make($input,
			array(
				'name' => 'required|unique:companies'
			)
		);

		if ($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		} else {
			$company = Company::create($input);
			if (Input::has('groups')) {
				foreach ($input['groups'] as $group_id) {
					$company->groups()->attach($group_id);
				}
			}

			return Redirect::to(url('company/garages/create', array($company->id)));
		}

	}

	public function getEdit($id)
	{
		$company = Company::find($id);
		$groups = CompanyGroup::lists('name', 'id');
        $contractorGroups = ContractorGroup::lists('name', 'id');
        $contractorGroups = ['' => '--- wybierz ---']+ $contractorGroups;
		return View::make('companies.edit', compact('company', 'groups', 'contractorGroups'));
	}

	public function getAssignGuardian($id)
	{
		$company = Company::find($id);
		$users = User::with('logins')->where(function($query) {
            if(Input::has('filter_login') && Input::get('filter_login')) {
                $query->where('login','like', Input::get('filter_login').'%');
            }
            if(Input::has('filter_name') && Input::get('filter_name')) {
                $query->where('name','like', '%'.Input::get('filter_name').'%');
            }
            if(Input::has('filter_email') && Input::get('filter_email')) {
                $query->where('email','like', Input::get('filter_email').'%');
            }
        })->orderBy('name')->paginate(Session::get('search.pagin', '5'));

		return View::make('companies.dialog.assign_guardian', compact('company', 'users'));
	}

	public function postAssignGuardian()
	{
		$company = Company::find(Request::get('company_id'));
		$user = User::find(Request::get('user_id'));
		if(!is_null($user)){			
			$guardian = CompanyGuardian::where('user_id', $user->id)->first();
			if(count($guardian)<1){
				$guardian = new CompanyGuardian();
				$guardian->user_id = $user->id;
				$guardian->save();
			}
			$company->guardian_id = $guardian->id;
			$company->save();
		}
		return Redirect::to(url('companies/show', [$company->id]));
		
	}

	public function getDeleteGuardian($id)
	{	
		$company = Company::find($id);
		return View::make('companies.dialog.delete_guardian', compact('company'));
	}

	public function postDeleteGuardian($id)
	{	
		$company = Company::find($id);
		$company->guardian_id = null;
		$company->save();
		return json_encode(['code'=>1, 'url'=> url('companies/show', [$company->id])]);
	}

	public function postUpdate($id)
	{

		$input = Input::all();

		$validator = Validator::make($input,
			array(
				'name' => 'required|unique:companies,name,' . $id
			)
		);

		if ($validator->fails()) {
			return Redirect::back()->withInput()->withErrors($validator);
		} else {
			$company = Company::find($id);
			$company->name = Input::get('name');
			$company->city = Input::get('city');
			$company->code = Input::get('code');
			$company->street = Input::get('street');
			$company->nip = Input::get('nip');
			$company->regon = Input::get('regon');
			$company->krs = Input::get('krs');
			$company->www = Input::get('www');
			$company->email = Input::get('email');
			$company->remarks = Input::get('remarks');
			$company->phone = Input::get('phone');
			$company->commission = Input::get('commission');
			$company->account_nr = Input::get('account_nr');
			$company->dirty = 1;
			$company->contractor_group_id = Input::get('contractor_group_id');
			$company->service_cession_data = str_replace(['–', '—'], '-', Input::get('service_cession_data'));

			$company->save();

			$groups = $company->groups->lists('id', 'id');

            foreach (Input::get('groups',[]) as $group_id) {
                if(! isset($groups[$group_id])) $company->groups()->attach($group_id);
                if(isset($groups[$group_id])) unset($groups[$group_id]);
            }

			$company = Company::find($id);

			foreach ($groups as $group_id) {
			    $group = CompanyGroup::find($group_id);

                DB::table('company_company_group')
                    ->where('company_id', $company->id)
                    ->where('company_group_id', $group_id)
                    ->update(array('deleted_at' => DB::raw('NOW()')));

                Log::info('usunięto grupę warsztatową: ' . $group->name . ' z warsztatu id: ' . $company->id . ' -> użytkownik id:' . Auth::user()->id);
			}

			return Redirect::to(url('companies/show', [$company->id]));

		}

	}

	public function getDelete($company_id)
	{
        Session::forget('search_company');

		$company = Company::with('branches', 'branches.injuries')->find($company_id);

		return View::make('companies.dialog.delete-company', compact('company'));
	}

	public function postDelete($id)
	{
		$company = Company::with('branches', 'branches.injuries')->find($id);

		/*
		foreach ($company->branches as $branch) {
			foreach ($branch->injuries as $injury) {
				$injury->branch_id = '-1';
				$injury->save();
				Histories::history($injury->id, 131, Auth::user()->id, 'usunięcie obsługującego warsztatu');
			}

			$branch->save();
			$branch->delete();
		}
		*/
		$company->branches()->delete();

		$company->delete();

		return json_encode(['code' => '0']);
	}

	public function getGroups($company_id)
	{
		$company = Company::find($company_id);
		$groups = CompanyGroup::lists('name', 'id');
		return View::make('companies.dialog.groups', compact('company', 'groups'));
	}

	public function postGroups($company_id)
	{
		$company = Company::find($company_id);

        $groups = $company->groups->lists('id', 'id');

        foreach (Input::get('groups',[]) as $group_id) {
            if(! isset($groups[$group_id])) $company->groups()->attach($group_id);
            if(isset($groups[$group_id])) unset($groups[$group_id]);
        }

        $company = Company::find($company_id);

        foreach ($groups as $group_id) {
            $group = CompanyGroup::find($group_id);

            DB::table('company_company_group')
                ->where('company_id', $company->id)
                ->where('company_group_id', $group_id)
                ->update(array('deleted_at' => DB::raw('NOW()')));

            Log::info('usunięto grupę warsztatową: ' . $group->name . ' z warsztatu id: ' . $company->id . ' -> użytkownik id:' . Auth::user()->id);
        }

		$company->update(['dirty' => 1]);

		return json_encode(['code' => '0']);
	}

	public function getMap()
	{
		$typegarages = Typegarage::lists('name', 'id');
		$groups = CompanyGroup::lists('name', 'id');

		return View::make('companies.map.index', compact('typegarages', 'groups'));
	}

	public function postListMapGarages()
	{
        if(Input::has('vehicle_id')) {
            if (Input::get('vehicle_type') == 'vmanage') {
                $vehicle = VmanageVehicle::withTrashed()->where('id', Input::get('vehicle_id'))->with('salesProgram')->first();
            } else {
                $syjonService = new \Idea\SyjonService\SyjonService();
                $vehicle = json_decode($syjonService->loadVehicle(Input::get('vehicle_id'), Input::get('contract_id')))->data;
                $contract = json_decode($syjonService->loadContract(Input::get('contract_id')))->data;
                $syjonProgram = SyjonProgram::find($contract->program_id);
                $vehicle->salesProgram = $syjonProgram;
            }

            $sales_program = $vehicle->salesProgram ? $vehicle->salesProgram->name_key : '';

            $brand = checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand);

            if (Input::get('vehicle_type') == 'vmanage'  && $vehicle->seller) {
                $seller = $vehicle->seller->nip;
            }elseif(!is_null($vehicle->nip_dost)) {
                $seller = preg_replace("/[^0-9]/", "", $vehicle->nip_dost ) ;
            }else{
                $seller = null;
            }
        }else{
            $sales_program = null;
            $brand = null;
            $seller = null;
        }

        $coordinates = [
            'ne_lat' => Input::get('ne_lat'),
            'ne_lng' => Input::get('ne_lng'),
            'sw_lat' => Input::get('sw_lat'),
            'sw_lng' => Input::get('sw_lng')
        ];

		$branches = Branch::with('company.groups', 'typegarages', 'brands', 'typevehicles')->where('if_map', 1)
			->where(function ($query) use($sales_program, $brand, $coordinates, $seller){
				if (Input::has('groups')) {
					$group = Input::get('groups');
					if ($group == 0) {
						$query->where(function ($query) {
							$query->whereHas('company', function ($query) {
								$query->has('groups', 0);
							});
						});
					} else {
						$query->where(function ($query) use ($group) {
							$query->whereHas('company', function ($query) use ($group) {
								$query->whereHas('groups', function ($query) use ($group) {
									$query->where('company_group_id', $group);
								});
							});
						});
					}
				} else {
					$query->where(function ($query) {
						$query->whereHas('company', function ($query) {
							$query->has('groups', -1);
						});
					});
				}
				if (Input::has('typeGarages')) {
					$query->whereHas('typegarages', function ($subquery) {
						$subquery->whereIn('typegarages_id', Input::get('typeGarages'));
					});
				}
				if (Input::has('brands_c')) {
					$query->whereHas('branchBrands', function ($subquery) {
						$brands = explode(",", Input::get('brands_c'));
						$subquery->whereIn('brand_id', $brands);
					});
				}
				if (Input::has('brands_t')) {
					$query->whereHas('branchBrands', function ($subquery) {
						$brands = explode(",", Input::get('brands_t'));
						$subquery->whereIn('brand_id', $brands);
					});
				}
                if (Input::has('authorizations')) {
                    $query->whereHas('branchBrands', function ($subquery) {
                        $brands = explode(",", Input::get('authorizations'));
                        $subquery->whereIn('brand_id', $brands)->where('authorization', 1);
                    });
                }

                $vehicle_id = Input::has('vehicle_id');
                $plan_group_id = Input::get('plan_group_id');
                if($vehicle_id || $plan_group_id) {
                    $query->whereHas('branchPlanGroups', function ($query) use ($sales_program, $brand, $seller, $vehicle_id, $plan_group_id) {
                        if($brand && $brand != '') {
                            $query->whereHas('branchBrands', function($query) use($brand, $seller){
                                $query->where(function($query) use($seller) {
                                    $query->where('if_sold', 0);
                                    $query->orWhere(function($query) use($seller){
                                        $query->where('if_sold', 1);

                                        if($seller){
                                            $query->where(function($query) use($seller){
                                                $query->whereHas('branch', function($query) use($seller){
                                                    $query->where('nip', 'like', $seller);
                                                });
                                            });
                                        }
                                    });

                                });

                                $query->whereHas('brand', function($query) use($brand){
                                    $query->where('name',  'like', $brand.'%');
                                });
                            });
                        }

                        if($plan_group_id){
                            $query->where('plan_group_id', $plan_group_id);
                        }
                        if($vehicle_id) {
                            $query->whereHas('planGroup', function ($query) use ($sales_program) {
                                $query->whereHas('plan', function ($query) use ($sales_program) {
                                    $query->where('sales_program', $sales_program);
                                });
                            });
                            if ($brand && $brand != '') {
                                $query->whereHas('branchBrands', function ($query) use ($brand) {
                                    $query->whereHas('brand', function ($query) use ($brand) {
                                        $query->where('name', 'like', $brand . '%');
                                    });
                                });
                            }
                        }
                    });
                }

                if($coordinates['sw_lat'] && $coordinates['ne_lat']) {
                    $query->whereBetween('lat', [$coordinates['sw_lat'], $coordinates['ne_lat']])
                        ->whereBetween('lng', [$coordinates['sw_lng'], $coordinates['ne_lng']]);
                }
			})
            ->with(['branchPlanGroups' => function($query) use($brand, $sales_program, $seller){
                if($brand && $brand != '') {
                    $query->whereHas('branchBrands', function($query) use($brand, $seller){
                        $query->where(function($query) use($seller) {
                            $query->where('if_sold', 0);
                            $query->orWhere(function($query) use($seller){
                                $query->where('if_sold', 1);

                                if($seller){
                                    $query->where(function($query) use($seller){
                                        $query->whereHas('branch', function($query) use($seller){
                                            $query->where('nip', 'like', $seller);
                                        });
                                    });
                                }
                            });

                        });

                        $query->whereHas('brand', function($query) use($brand){
                            $query->where('name',  'like', $brand.'%');
                        });
                    });
                }

                $query->with(['planGroup' => function ($query) use($sales_program){

                    if (Input::has('vehicle_id') && $sales_program) {
                        $query->whereHas('plan', function ($query) use ($sales_program) {
                            $query->where('sales_program', $sales_program);
                        });
                    }

                    $query->with('companyGroups');
                }]);
                $query->with(['branchBrands' => function($query) use($brand){
                    if($brand && $brand != '') {
                        $query->whereHas('brand', function($query) use($brand){
                            $query->where('name',  'like', $brand.'%');
                        });
                    }
                }]);
            }])
			->orderBy('lat')->orderBy('lng');

        $branches = $branches->get();
		$result = [];

		$markers_input = Input::get('markers');
		$typegarages = Typegarage::lists('type', 'id');

		foreach ($branches as $branch) {
			$marker = '/images/markers/yellow.png';
			$marker_id = 4;

			$garagetype = array(0 => 0, 1 => 0, 2 => 0);
			foreach ($branch->typegarages as $val) {
				if ($val->pivot->typegarages_id && isset($typegarages[$val->pivot->typegarages_id]))
					$garagetype[$typegarages[$val->pivot->typegarages_id]]++;
			}

			if ($garagetype[0] > 0 && $garagetype[1] > 0) {
				$marker = '/images/markers/purple.png';
				$marker_id = 3;
			} elseif ($garagetype[0] > 0) {
				$marker = '/images/markers/blue.png';
				$marker_id = 1;
			} elseif ($garagetype[1] > 0) {
				$marker = '/images/markers/red.png';
				$marker_id = 2;
			}

			if ($garagetype[2] > 0) {
				$marker = '/images/markers/purple.png';
				$marker_id = 3;
			}

			if ($branch->suspended == 1 && $branch->suspended != null) {
				$marker = '/images/markers/black.png';
				$marker_id = 5;
			}

			if (isset($markers_input[$marker_id])) {
				$brands = array(
					1 => array(),
					2 => array(),
				);
				foreach ($branch->brands->sortBy('name') as $brand) {
					$brands[$brand->typ][] = $brand->name;
				}
				foreach ($brands as $key => $brand) {
					$brands[$key] = $brand = implode(', ', $brand);
				}


                $authorizations_s = implode(', ', $branch->authorizations->sortBy('name')->lists('name'));

				$typevehicles = '';
				foreach ($branch->typevehicles as $type_temp) {
					if ($type_temp->typevehicles && $type_temp->value != 0)
						$typevehicles .= $type_temp->typevehicles->name . ': ' . $type_temp->value . ' | ';
				}

				$result[] = [
					'id' => $branch->id,
					'short_name' => $branch->short_name,
					'company_type' => $branch->company->type,
					'company_name' => $branch->company->name,
					'address' => $branch->code . ' ' . $branch->city . ', ' . $branch->street,
					'email' => ($branch->email != '' || $branch->other_emails != '') ? 'email: <b>' . $branch->email.'</b> '.($branch->other_emails ? ' '.$branch->other_emails : '' ): '',
					'phone' => ($branch->phone != '') ? 'telefon: ' . $branch->phone : '',
					'lat' => $branch->lat,
					'lng' => $branch->lng,
					'brands' => $brands,
					'typevehicles' => $typevehicles,
					'marker' => $marker,
					'remarks' => $branch->remarks,
                    'nip' => $branch->nip,
                    'suspended' => $branch->suspended,

                    'open_time' => $branch->open_time ? $branch->open_time : '',
                    'close_time' => $branch->close_time ? $branch->close_time : '',
                    'contact_people' => $branch->contact_people == null ? '' : $branch->contact_people,
                    'priorities' => $branch->priorities == null ? '' : $branch->priorities,
                    'tug_remarks' => $branch->tug_remarks == null ? '' : $branch->tug_remarks,
                    'delivery_cars' => $branch->delivery_cars == null ? '' : $branch->delivery_cars,
                    'commission' => (isset($branch->company) && $branch->company->commissions->first()) ? $branch->company->commissions->first()->commission : 'brak',//company->commissions->first()->commission,
                    'authorizations' => $authorizations_s,
                    'branchPlanGroups' => $branch->branchPlanGroups->toArray()
				];
			}
		}

		return json_encode($result);
	}

	public function postListMapGroups(){
        if(Input::has('vehicle_id')) {
            if (Input::get('vehicle_type') == 'vmanage') {
                $vehicle = VmanageVehicle::withTrashed()->where('id', Input::get('vehicle_id'))->with('salesProgram')->first();
            } else {
                $syjonService = new \Idea\SyjonService\SyjonService();
                $vehicle = json_decode($syjonService->loadVehicle(Input::get('vehicle_id'), Input::get('contract_id')))->data;
                $contract = json_decode($syjonService->loadContract(Input::get('contract_id')))->data;
                $syjonProgram = SyjonProgram::find($contract->program_id);
                $vehicle->salesProgram = $syjonProgram;
            }

            $sales_program = $vehicle->salesProgram ? $vehicle->salesProgram->name_key : '';

            $brand = checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand);

            if (Input::get('vehicle_type') == 'vmanage'  && $vehicle->seller) {
                $seller = $vehicle->seller->nip;
            }elseif(!is_null($vehicle->nip_dost)) {
                $seller = preg_replace("/[^0-9]/", "", $vehicle->nip_dost ) ;
            }else{
                $seller = null;
            }
        }else{
            $sales_program = null;
            $brand = null;
            $seller = null;
        }

        $query = PlanGroup::orderBy('id');

        if (Input::has('vehicle_id') && $sales_program) {
            $query->whereHas('plan', function ($query) use ($sales_program) {
                $query->where('sales_program', $sales_program);
            });
        }
        $coordinates = [
            'ne_lat' => Input::get('ne_lat'),
            'ne_lng' => Input::get('ne_lng'),
            'sw_lat' => Input::get('sw_lat'),
            'sw_lng' => Input::get('sw_lng')
        ];
       // dd($brand, $seller, $coordinates);
        $query->whereHas('branchPlanGroups', function ($query) use($brand, $seller, $coordinates){
            $query->whereHas('branchBrands', function($query) use($brand, $seller){
                if($brand && $brand != '') {
                    $query->whereHas('brand', function($query) use($brand){
                        $query->where('name',  'like', $brand.'%');
                    });
                }

                $query->where(function($query) use($seller) {
                    $query->where('if_sold', 0);
                    $query->orWhere(function($query) use($seller){
                        $query->where('if_sold', 1);

                        if($seller){
                            $query->whereHas('branch', function($query) use($seller){
                                $query->where('nip', 'like', $seller);
                            });
                        }
                    });

                });
            });

            $query->whereHas('branch', function($query) use($coordinates){
                $query->whereBetween('lat', [$coordinates['sw_lat'], $coordinates['ne_lat']])
                    ->whereBetween('lng', [$coordinates['sw_lng'], $coordinates['ne_lng']]);
            });
        });
//        $sql = str_replace(array('?'), array('\'%s\''), $query->toSql());
//        $sql = vsprintf($sql, $query->getBindings());
//        dd($sql);
        $planGroups = $query->get();

        return json_encode($planGroups);
    }

	public function getBrandsList($typ)
	{
		$name = Input::get('q');
		$brands = Brands::select('id', 'name as text')->where('typ', '=', $typ)->where('name', 'like', '%' . $name . '%')->get();

		$result = array();
		foreach ($brands as $k => $v) {
			$result[] = array("id" => $v->id, "text" => $v->text);
		}

		return json_encode($result);
	}

	public function getBrandsListConnect()
	{
		$ids = Input::get('q');
		if (trim($ids) != '') {
			$ids = explode(',', $ids);
			$ids = array_map('trim', $ids);
			$ids = array_unique($ids);

			foreach ($ids as $key => $value) {
				$value = trim($value);
				if ($value != '') {
					$brand = Brands::select('id', 'name as text')->where('id', '=', $value)->first();

					$result[] = array("id" => $value, "text" => $brand->text);
				}
			}

			return json_encode($result);
		}
	}

	public function getCreateGroup()
	{
		$owners = Owners::whereActive(0)->lists('name', 'id');
		$markers = array('red', 'green', 'yellow', 'blue', 'black', 'purple');
		return View::make('companies.dialog.create-group', compact('owners', 'markers'));
	}

	public function postStoreGroup()
	{
		$group = CompanyGroup::create(Input::all());
		if (Input::has('owners')) {
			foreach (Input::get('owners') as $owner_id) {
				$group->owners()->attach($owner_id);
			}
		}
		return json_encode(['code' => '0']);
	}

	public function getEditGroup($group_id)
	{
		$group = CompanyGroup::with('owners')->find($group_id);
		$owners = Owners::whereActive(0)->lists('name', 'id');

		return View::make('companies.dialog.edit-group', compact('group', 'owners'));
	}

	public function postUpdateGroup($group_id)
	{
		$group = CompanyGroup::with('owners')->find($group_id);
		$group->update(Input::all());

		$group->owners()->detach();
		if (Input::has('owners')) {
			foreach (Input::get('owners') as $owner_id) {
				$group->owners()->attach($owner_id);
			}
		}

		return json_encode(['code' => '0']);
	}

	public function getEditGroupMarker($group_id)
	{
		$group = CompanyGroup::find($group_id);
		$markers = array('red', 'green', 'yellow', 'blue', 'black', 'purple');

		return View::make('companies.dialog.edit-group-marker', compact('group', 'markers'));
	}

	public function postUpdateGroupMarker($group_id)
	{
		$group = CompanyGroup::find($group_id);

		$group->update(Input::all());

		return json_encode(['code' => '0']);
	}

	public function getCommissions($company_id)
	{
        Session::forget('search_company');

		$company = Company::with('commissions', 'commissions.brand')->find($company_id);
		$commissionTypes = CommissionType::lists('name', 'id');
		$commissionTypes[null]  = '--- wybierz ---';

		$billingCycles = BillingCycle::lists('name', 'id');
		$billingCycles[null] = '--- wybierz ---';

		if($company->commission_type_id == 4) {
			$brand_ids = [];
			foreach ($company->branches as $branch) {
				$brand_ids = array_merge($brand_ids, $branch->brands->lists('id', 'id'));
			}
			$brands = Brands::whereIn('id', $brand_ids)->whereNotIn('id', $company->commissions->lists('brand_id', 'brand_id'))->lists('name', 'id');
            $brands[0] = 'pozostałe marki';
		}else{
			$brands = [];
		}
		return View::make('companies.commissions.index', compact('company', 'commissionTypes', 'billingCycles', 'brands'));
	}

	public function postCommission()
	{
		$commission_type_id = Request::get('commission_type_id');
		$company_id = Request::get('company_id');

		$readonly = 0;
		switch ($commission_type_id){
			case 1:
				return View::make('companies.commissions.commission-linear', compact('readonly'));
				break;
			case 2:
				return View::make('companies.commissions.commission-threshold-amount', compact('readonly'));
				break;
			case 3:
				return View::make('companies.commissions.commission-threshold-value', compact('readonly'));
				break;
			case 4:
				$company = Company::find($company_id);
				$brand_ids = [];
				foreach($company->branches as $branch)
				{
					$brand_ids = array_merge($brand_ids, $branch->brands->lists('id', 'id'));
				}
				$brands = Brands::whereIn('id', $brand_ids)->lists('name', 'id');
				$brands[0] = 'pozostałe marki';

				return View::make('companies.commissions.commission-brand', compact('brands', 'readonly'));
				break;
			default:
				return '';
		}
	}

	public function postStoreCommissions($company_id)
	{
		$company = Company::find($company_id);

		$company->update([
			'commission_type_id'    =>  Request::get('commission_type_id'),
			'billing_cycle_id'    =>  Request::get('billing_cycle_id'),
		]);

		$company->commissions()->delete();

		switch (Request::get('commission_type_id')){
			case 1:
				CompanyCommission::create([
					'company_id'    => $company_id,
					'commission'    =>  Request::get('commission')
				]);
				break;
			case 2:
				foreach(Request::get('amount', []) as $k => $amount){
					CompanyCommission::create([
						'company_id'    => $company_id,
						'min_amount'    =>  $amount,
						'commission'    =>  Request::get('commission')[$k]
					]);
				}
				break;
			case 3:
				foreach(Request::get('value', []) as $k => $value){
					CompanyCommission::create([
						'company_id'    => $company_id,
						'min_value'    =>  $value,
						'commission'    =>  Request::get('commission')[$k]
					]);
				}
				break;
			case 4:
				foreach(Request::get('brands', []) as $k => $brand_id){
					CompanyCommission::create([
						'company_id'    => $company_id,
						'brand_id'      =>  $brand_id,
						'commission'    =>  Request::get('commission')[$k]
					]);
				}
				break;
			default:
				break;
		}

		Flash::message('Prowizje dla '.$company->name.' zostały zaktualizowane.');

		return Redirect::to('companies/show/'.$company_id);
	}

    public function postUpdateBranchFields($branch_id)
    {
        $branch = Branch::findOrFail($branch_id);
        $branch->update(Input::all());

        return json_encode($branch->toArray());
    }

    public function getShow($company_id){
	    $company = Company::findOrFail($company_id);

        return View::make('companies.show', compact('company'));
    }

    public function getPair($company_id)
    {
        $company = Company::findOrFail($company_id);

        return View::make('companies.pair', compact('company'));
    }

    public function postSearchCompany()
    {
        $companies = Company::where('name', 'like', '%'.Input::get('term').'%')
                ->where('id' , '!=', Input::get('company_id'))
                ->orderBy('name')
                ->limit(20)
                ->get();

        $result = array();

        foreach($companies as $k => $v){
            $result[] = array(
                "id" => $v->id,
                "label" => $v->name,
                "value" => $v->name,
                'address' => $v->address,
                'name' => $v->name
            );
        }

        return json_encode($result);
    }

    public function postPair($company_id)
    {
        $transferred_company = Company::find($company_id);

        $company = Company::find(Input::get('company_id'));

        foreach($transferred_company->branches as $branch)
        {
            $branch->update([
                'company_id' => $company->id,
                'transferred_company_id' => $transferred_company->id,
            ]);
        }

        $transferred_company->delete();

        return Redirect::to(url('companies/show', [$company->id]));
    }
}
