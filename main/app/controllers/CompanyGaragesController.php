<?php

class CompanyGaragesController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:serwisy#wejscie');
        $this->beforeFilter('permitted:serwisy#warsztaty#dodaj_warsztat', ['only' => ['getCreate', 'postStore']]);
        $this->beforeFilter('permitted:serwisy#warsztaty#zarzadzaj', ['only' => ['getEdit', 'postUpdate', 'getDelete', 'postDelete']]);
    }

    public function getIndex($id)
    {
        $garages = Branch::where('active', '=', '0')->where('company_id', '=', $id)->orderBy('short_name')->with('typegarages', 'voivodeship')->paginate(Session::get('search.pagin', '10'));

        $company = Company::find($id);

        return View::make('companies.garages', compact('garages', 'company'));
    }

    public function getShow($branch_id)
    {
        $branch = Branch::with('branchPlanGroups', 'branchPlanGroups.branchBrands', 'branchPlanGroups.planGroup.plan')->findOrFail($branch_id);
        $company = $branch->company;

        return View::make('companies.garages.show', compact('branch', 'company'));
    }

    public function getCreate($id)
    {
        $company = Company::find($id);
        $typegarages = Typegarage::where('active', '=', '0')->orderBy('name')->get();
        $typevehicles = Typevehicles::all();
        $voivodeships = Voivodeship::lists('name', 'id');
        $voivodeships = array_merge(array('0' => '--- wybierz województwo ---'), $voivodeships);

        return View::make('companies.garage-create', compact('company', 'typegarages', 'typevehicles', 'voivodeships'));
    }

    public function postStore($id)
    {
        $input = Input::all();
        $validator = Validator::make($input,
            array(
                'city' => 'required',
                'code' => 'required',
                'street' => 'required'
            )
        );

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            if (Input::get('if_map')) $if_map = 1; else $if_map = 0;
            if (Input::get('suspended')) $suspended = 1; else $suspended = 0;
            if (Input::get('if_map_correct')) $if_map_correct = 1; else $if_map_correct = 0;

            $branch = Branch::create(array(
                'company_id' => $id,
                'short_name' => Input::get('short_name'),
                'city' => Input::get('city'),
                'code' => Input::get('code'),
                'street' => Input::get('street'),
                'voivodeship_id' => (Input::get('voivodeship_id') != 0) ? Input::get('voivodeship_id') : null,
                'email' => Input::get('email'),
                'other_emails' => Input::get('other_emails'),
                'phone' => Input::get('phone'),
                'remarks' => Input::get('remarks'),
                'priority' => Input::get('priority'),
                'lat' => Input::get('lat'),
                'lng' => Input::get('lng'),
                'if_map_correct' => $if_map_correct,
                'if_map' => $if_map,
                'tug' => (Input::has('tug')) ? '1' : '0',
                'tug24h' => (Input::has('tug24h')) ? '1' : '0',
                'suspended' => $suspended,
                'contact_people' => Input::get('contact_people'),
                'tug_remarks' => Input::get('tug_remarks'),
                'delivery_cars' => Input::get('delivery_cars'),
                'open_time' => Input::get('open_time'),
                'close_time' => Input::get('close_time'),
                'priorities' => Input::get('priorities'),
            ));

            if ($branch) {

                $typevehicles = Typevehicles::all();
                foreach ($typevehicles as $k => $v) {
                    if (Input::get('car' . $v->id) != 0) {
                        DB::table('branches_typevehicles')->insert(
                            array('branch_id' => $branch->id, 'typevehicles_id' => $v->id, 'value' => Input::get('car' . $v->id))
                        );
                    }
                }

                if (Input::get('typeGarages_id') != '') {
                    foreach (Input::get('typeGarages_id') as $k => $v) {
                        DB::table('branches_typegarages')->insert(
                            array('branch_id' => $branch->id, 'typegarages_id' => $v)
                        );
                    }
                }

                foreach(Input::get('brand_id', []) as $brand_id)
                {
                    BranchBrand::create([
                        'branch_id' => $branch->id,
                        'brand_id' => $brand_id,
                        'authorization' => isset(Input::get('authorization',[])[$brand_id]) ? Input::get('authorization',[])[$brand_id] : 0
                    ]);
                }

                return Redirect::to(url('companies/show', [$id]));
            } else {
                Flash::error('Wystąpił błąd w trakcie wprowadzania warsztatu.');
                return Redirect::back();
            }
        }

    }

    public function postUpdate($id)
    {
        $input = Input::all();

        $validator = Validator::make($input,
            array(
                'city' => 'required',
                'code' => 'required',
                'street' => 'required'
            )
        );

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {

            if (Input::get('if_map')) $if_map = 1; else $if_map = 0;
            if (Input::get('if_map_correct')) $if_map_correct = 1; else $if_map_correct = 0;
            if (Input::get('suspended')) $suspended = 1; else $suspended = 0;

            $branch = Branch::find($id);
            $branch->short_name = Input::get('short_name');
            $branch->city = Input::get('city');
            $branch->code = Input::get('code');
            $branch->street = Input::get('street');

            if (Input::get('voivodeship_id') != 0)
                $branch->voivodeship_id = Input::get('voivodeship_id');
            else
                $branch->voivodeship_id = null;

            $branch->nip = Input::get('nip');

            $branch->email = Input::get('email');
            $branch->other_emails = Input::get('other_emails');
            $branch->phone = Input::get('phone');
            $branch->remarks = Input::get('remarks');
            $branch->priority = Input::get('priority');
            $branch->lat = Input::get('lat');
            $branch->lng = Input::get('lng');
            $branch->if_map_correct = $if_map_correct;
            $branch->if_map = $if_map;
            $branch->suspended = $suspended;
            if (Input::has('tug')) $branch->tug = 1;
            else $branch->tug = 0;

            if (Input::has('tug24h')) $branch->tug24h = 1;
            else $branch->tug24h = 0;

            $branch->dirty = 1;

            $branch->contact_people = Input::get('contact_people');
            $branch->tug_remarks = Input::get('tug_remarks');
            $branch->delivery_cars = Input::get('delivery_cars');
            $branch->open_time = Input::get('open_time');
            $branch->close_time = Input::get('close_time');
            $branch->priorities = Input::get('priorities');

            if ($branch->save()) {

                DB::table('branches_typevehicles')->where('branch_id', $id)->delete();
                $typevehicles = Typevehicles::all();
                foreach ($typevehicles as $k => $v) {
                    if (Input::get('car' . $v->id) != 0) {
                        DB::table('branches_typevehicles')->insert(
                            array('branch_id' => $branch->id, 'typevehicles_id' => $v->id, 'value' => Input::get('car' . $v->id))
                        );
                    }
                }

                DB::table('branches_typegarages')->where('branch_id', $id)->delete();
                if (Input::get('typeGarages_id') != '') {
                    foreach (Input::get('typeGarages_id') as $k => $v) {
                        if (trim($v) != '') {
                            DB::table('branches_typegarages')->insert(
                                array('branch_id' => $id, 'typegarages_id' => $v)
                            );
                        }
                    }
                }

                return Redirect::to(url('company/garages/show', array($branch->id)));
            } else {
                return Redirect::back()->withErrors('Wystąpił błąd w trakcie wprowadzania zmian. Skontaktuj się z administratorem.');
            }
        }

    }

    public function postUpdateModal($id)
    {
        $input = Input::all();

        $branch = Branch::find($id);
        $branch->short_name = Input::get('short_name');

        $branch->email = Input::get('email');
        $branch->phone = Input::get('phone');
        $branch->remarks = Input::get('remarks');
        $branch->priority = Input::get('priority');

        if (Input::get('suspended')) $suspended = 1; else $suspended = 0;
        $branch->suspended = $suspended;

        if (Input::has('tug')) $branch->tug = 1;
        else $branch->tug = 0;

        if (Input::has('tug24h')) $branch->tug24h = 1;
        else $branch->tug24h = 0;

        $branch->dirty = 1;

        if ($branch->save()) {

            DB::table('branches_typevehicles')->where('branch_id', $id)->delete();
            $typevehicles = Typevehicles::all();
            foreach ($typevehicles as $k => $v) {
                if (Input::get('car' . $v->id) != 0) {
                    DB::table('branches_typevehicles')->insert(
                        array('branch_id' => $branch->id, 'typevehicles_id' => $v->id, 'value' => Input::get('car' . $v->id))
                    );
                }
            }

            DB::table('branches_typegarages')->where('branch_id', $id)->delete();
            if (Input::get('typeGarages_id') != '') {
                foreach (Input::get('typeGarages_id') as $k => $v) {
                    if (trim($v) != '') {
                        DB::table('branches_typegarages')->insert(
                            array('branch_id' => $id, 'typegarages_id' => $v)
                        );
                    }
                }
            }

            DB::table('branches_brands')->where('branch_id', $id)->delete();

            if (Input::get('brands_o') != '') {
                $brands = explode(",", Input::get('brands_o'));
                foreach ($brands as $k => $v) {
                    if (trim($v) != '') {
                        DB::table('branches_brands')->insert(
                            array('branch_id' => $id, 'brand_id' => $v)
                        );
                    }
                }
            }

            if (Input::get('brands_c') != '') {
                $brands = explode(",", Input::get('brands_c'));
                foreach ($brands as $k => $v) {
                    if (trim($v) != '') {
                        DB::table('branches_brands')->insert(
                            array('branch_id' => $branch->id, 'brand_id' => $v)
                        );
                    }
                }
            }

            return json_encode(['code' => '0']);

        } else {
            return json_encode(['code' => '1']);
        }

    }

    public function getDelete($branch_id)
    {
        $branch = Branch::with('injuries')->find($branch_id);

        return View::make('companies.dialog.delete-branch', compact('branch'));
    }

    public function postDelete($id)
    {
        $branch = Branch::find($id);

        /*
        foreach($branch->injuries as $injury)
        {
            $injury->branch_id = '-1';
            $injury->save();
            Histories::history($injury->id, 131, Auth::user()->id, 'usunięcie obsługującego warsztatu');
        }
        */

        $branch->active = '9';

        if ($branch->save()) {
            $branch->delete();
            return json_encode(['code' => '0']);
        }
    }

    public function getEdit($id)
    {
        $branch = Branch::find($id);
        $company = Company::find($branch->company_id);
        $typegarages = Typegarage::where('active', '=', '0')->orderBy('name')->get();
        $typegaragesSel = DB::table('branches_typegarages')->where('branch_id', '=', $id)->get();
        $typegaragesReSel[0] = 0;
        foreach ($typegaragesSel as $v) {
            $typegaragesReSel[$v->typegarages_id] = 1;
        }
        $typevehicles = Typevehicles::all();

        $typevehiclesSel = DB::table('branches_typevehicles')->where('branch_id', '=', $id)->get();
        $typevehiclesReSel = array();
        foreach ($typevehiclesSel as $v) {
            $typevehiclesReSel[$v->typevehicles_id] = $v->value;
        }

        $brands = $branch->brands;

        $authorizations = $branch->authorizations;

        $voivodeships = Voivodeship::lists('name', 'id');
        $voivodeships = array_merge(array('0' => '--- wybierz województwo ---'), $voivodeships);

        return View::make('companies.garages-edit', compact('company', 'branch', 'typegarages', 'typegaragesReSel', 'brands', 'typevehicles', 'typevehiclesReSel', 'voivodeships', 'authorizations'));
    }

    public function getEditModal($id)
    {
        $branch = Branch::find($id);
        $company = Company::find($branch->company_id);
        $typegarages = Typegarage::where('active', '=', '0')->orderBy('name')->get();
        $typegaragesSel = DB::table('branches_typegarages')->where('branch_id', '=', $id)->get();
        $typegaragesReSel[0] = 0;
        foreach ($typegaragesSel as $v) {
            $typegaragesReSel[$v->typegarages_id] = 1;
        }
        $typevehicles = Typevehicles::all();

        $typevehiclesSel = DB::table('branches_typevehicles')->where('branch_id', '=', $id)->get();
        $typevehiclesReSel = array();
        foreach ($typevehiclesSel as $v) {
            $typevehiclesReSel[$v->typevehicles_id] = $v->value;
        }

        $brands = $branch->brands;

        $voivodeships = Voivodeship::lists('name', 'id');
        $voivodeships = array_merge(array('0' => '--- wybierz województwo ---'), $voivodeships);

        return View::make('companies.dialog.garages-edit', compact('company', 'branch', 'typegarages', 'typegaragesReSel', 'brands', 'typevehicles', 'typevehiclesReSel', 'voivodeships'));
    }

    public function getDataToBranch($id)
    {
        $company = Company::find($id);
        $result = array(
            'short_name' => $company->name,
            'street' => $company->street,
            'code' => $company->code,
            'city' => $company->city,
            'phone' => $company->phone,
            'email' => $company->email
        );
        return json_encode($result);
    }

    public function getShowMap($branch_id)
    {
        $branch = Branch::find($branch_id);
        return View::make('companies.dialog.show', compact('branch'));
    }

    public function getCheckVoivodeship()
    {
        $code = Input::get('code');
        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();

        $voivodeship_id = $matcher->match($code);
        if ($voivodeship_id) {
            return Response::json([
                'status' => 'ok',
                'voivodeship_id' => $voivodeship_id
            ]);
        }

        return Response::json([
            'status' => 'not found'
        ]);
    }

    public function getEditBranchBrands($branch_id)
    {
        $branch = Branch::with('branchBrands.brand')->findOrFail($branch_id);
        
        return View::make('companies.garages.edit-branch-brands', compact('branch'));
    }

    public function getSearchBrand()
    {
        $brands = Brands::where(function($query){
            $name = Input::get('term');

            $query->where('name', 'like', $name . '%');
            if(Input::has('typ')){
                $query->where('typ', Input::get('typ'));
            }
            if(Input::has('brands'))
            {
                $query->whereNotIn('id', Input::get('brands', []));
            }
        })->orderBy('if_multibrand','desc')->orderBy('id')->get();

        $result = array();
        foreach ($brands as $k => $v) {
            $result[] = [
                "id" => $v->id,
                "label" => $v->name. ' ' .($v->typ == 1 ? '(osobowe)': '(ciężarowe)'),
                "value" => $v->name. ' ' .($v->typ == 1 ? '(osobowe)': '(ciężarowe)'),
                'name' => $v->name,
                'typ' => ($v->typ == 1 ? 'osobowe': 'ciężarowe'),
                'if_multibrand' => $v->if_multibrand
            ];
        }

        return json_encode($result);
    }

    public function postUpdateBranchBrands($branch_id)
    {
        $branch = Branch::findOrFail($branch_id);

        $brand_ids = $branch->branchBrands->lists('brand_id');
        foreach (Input::get('brand_id', []) as $brand_id)
        {
            if(!in_array($brand_id, $brand_ids)) {
                BranchBrand::create([
                    'branch_id' => $branch_id,
                    'brand_id' => $brand_id,
                    'authorization' => Input::get('authorization')[$brand_id],
                    'if_multibrand' => Input::get('as_multibrand')[$brand_id]
                ]);
            }else{
                $branchBrand = BranchBrand::where('brand_id', $brand_id)->where('branch_id', $branch_id)->first();
                if($branchBrand){
                    $branchBrand->update(['authorization' => Input::get('authorization')[$brand_id]]);
                    $branchBrand->update(['if_multibrand' => Input::get('as_multibrand')[$brand_id]]);
                }
            }
        }

        $branch->branchBrands()->whereNotIn('brand_id', array_keys(Input::get('brand_id', [])))->delete();

        return Redirect::to(url('company/garages/show', [$branch_id]));
    }

    public function getAttachPlan($branch_id)
    {
        $branch = Branch::findOrFail($branch_id);
        $plans = Plan::get();

        return View::make('companies.garages.attach-plan', compact('branch', 'plans'));
    }

    public function getLoadPlanGroups()
    {
        $plan = Plan::findOrFail(Input::get('plan_id'));

        return View::make('companies.garages.plan-groups', compact('plan'));
    }

    public function postAttachPlan($branch_id)
    {
        $branch_plan_group = BranchPlanGroup::create([
            'branch_id' => $branch_id,
            'plan_group_id' => Input::get('plan_group_id'),
        ]);

        foreach(Input::get('branch_brand_id', []) as $k => $branch_brand_id){
            if(isset(Input::get('sold_yes', [])[$k])) {
                $branch_plan_group->branchBrands()
                    ->attach($branch_brand_id, array('if_sold' => 1));
            }
            if(isset(Input::get('sold_no', [])[$k])) {
                $branch_plan_group->branchBrands()
                    ->attach($branch_brand_id, array('if_sold' => 0));
            }
        }

        return Redirect::to(url('company/garages/show', [$branch_id]));
    }

    public function getDeleteBranchPlanGroup($branch_plan_group_id)
    {
        $group = BranchPlanGroup::findOrFail($branch_plan_group_id);

        return View::make('plans.groups.delete-branch-plan-group', compact('group'));
    }

    public function postDeleteBranchPlanGroup($branch_plan_group_id)
    {
        $group = BranchPlanGroup::findOrFail($branch_plan_group_id);
        $group->delete();

        return json_encode(['code' => '0']);
    }

    public function getEditGroup($branch_plan_group_id)
    {
        $group = BranchPlanGroup::findOrFail($branch_plan_group_id);

        return View::make('companies.garages.edit-plan', compact('group'));
    }

    public function postUpdateGroup($branch_plan_group_id)
    {
        $branch_plan_group = BranchPlanGroup::findOrFail($branch_plan_group_id);

        $branch_plan_group->branchBrands()->detach();
        foreach(Input::get('branch_brand_id', []) as $k => $branch_brand_id){
            if(isset(Input::get('sold_yes', [])[$k])) {
                $branch_plan_group->branchBrands()
                    ->attach($branch_brand_id, array('if_sold' => 1));
            }
            if(isset(Input::get('sold_no', [])[$k])) {
                $branch_plan_group->branchBrands()
                    ->attach($branch_brand_id, array('if_sold' => 0));
            }
        }

        return Redirect::to(url('company/garages/show', [$branch_plan_group->branch_id]));
    }

    public function getAddBrands($type)
    {
        return View::make('companies.garages.add-brands', compact('type'));
    }
}
