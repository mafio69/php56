<?php

class PlanGroupsController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:programy#wejscie');
        $this->beforeFilter('permitted:programy#zarzadzaj', ['only' => ['getCreate', 'postStore', 'getEdit', 'postUpdate', 'getAddBranch', 'postAttachBranch', 'getDelete', 'postDelete', 'postDeleteBranchPlanGroup', 'postUpdateBranch']]);
    }

	public function getCreate($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);

        $max_ordering = $plan->groups()->max('ordering');
        $ordering = $max_ordering + 1;

        $companyGroups = CompanyGroup::with('companies')->get();

        return View::make('plans.groups.create', compact('plan', 'ordering', 'companyGroups'));
    }

    public function postStore()
    {
        $plan_group = PlanGroup::create(Input::all());

        $plan_group->companyGroups()->sync( Input::get('company_group', []) );

        foreach($plan_group->companyGroups as $companyGroup)
        {
            foreach($companyGroup->companies as $company)
            {
                foreach($company->branches as $branch) {
                    BranchPlanGroup::create([
                        'branch_id' => $branch->id,
                        'plan_group_id' => $plan_group->id
                    ]);
                }
            }
        }

        return Redirect::to(url('plans/show', [$plan_group->plan_id]));
    }

    public function getShow($plan_group_id)
    {
        $plan_group = PlanGroup::findOrFail($plan_group_id);

        return View::make('plans.groups.show', compact('plan_group'));
    }

    public function getEdit($plan_group_id)
    {
        $plan_group = PlanGroup::findOrFail($plan_group_id);
        $companyGroups = CompanyGroup::with('companies')->get();

        return View::make('plans.groups.edit', compact('plan_group', 'companyGroups'));
    }

    public function postUpdate($plan_group_id)
    {
        $plan_group = PlanGroup::findOrFail($plan_group_id);
        $plan_group->update(Input::all());

        foreach(Input::get('remove_branches', []) as $company_group_id)
        {
            $companyGroup = CompanyGroup::find($company_group_id);

            foreach($companyGroup->companies as $company)
            {
                foreach($company->branches as $branch)
                {
                    BranchPlanGroup::where('branch_id', $branch->id)->where('plan_group_id', $plan_group->id)->delete();
                }
            }
        }

        foreach(Input::get('company_group', []) as $company_group_id)
        {
            $companyGroup = CompanyGroup::find($company_group_id);
            foreach($companyGroup->companies as $company)
            {
                foreach($company->branches as $branch) {
                    $branchPlanGroup = BranchPlanGroup::where('branch_id', $branch->id)->where('plan_group_id', $plan_group->id)->first();
                    if(! $branchPlanGroup) {
                        BranchPlanGroup::create([
                            'branch_id' => $branch->id,
                            'plan_group_id' => $plan_group->id
                        ]);
                    }
                }
            }
        }

        $plan_group->companyGroups()->sync( Input::get('company_group', []) );

        return Redirect::to(url('plan/groups/show', [$plan_group->id]));
    }

    public function getAddBranch($plan_group_id)
    {
        $plan_group = PlanGroup::findOrFail($plan_group_id);

        return View::make('plans.groups.add-branch', compact('plan_group'));
    }

    public function getSearchBranch()
    {
        $branches = Branch::where(function($query){
            if(Input::has('name')&& Input::get('name') != '') {
                $query->where('short_name', 'like', '%' . Input::get('name') . '%');
            }
            if(Input::has('nip')&& Input::get('nip') != '') {
                $query->where('nip', 'like', '%' . Input::get('nip') . '%');
            }
            if(Input::has('brand')&& Input::get('brand') != ''){
                $query->whereHas('brands', function ($query){
                    $query->where('name', 'like', '%'.Input::get('brand').'%');
                });
            }
            if(Input::has('aso') && Input::get('aso') < 2)
            {
                $query->whereHas('branchBrands', function ($query){
                    $query->where('authorization', Input::get('aso'));
                });
            }
        })->with('company')->limit(100)->get();

        return $branches->toJson();
    }

    public function getBranchBrands()
    {

        $branch = Branch::findOrFail(Input::get('branch_id'));
        $plan_group = PlanGroup::find(Input::get('plan_group_id'));
        $attached_ids = [];
        foreach($plan_group->branchPlanGroups as $branchPlanGroup){
            foreach($branchPlanGroup->branchBrands as $branchBrand)
            {
                $attached_ids[] = $branchBrand->id;
            }
        }
        $brands = $branch->branchBrands()->where(function($query) use($attached_ids){
            if(Input::has('brand') && Input::get('brand') != ''){
                $query->whereHas('brand', function ($query){
                    $query->where('name', 'like', '%'.Input::get('brand').'%');
                });
            }
            if(Input::has('aso') && Input::get('aso') < 2)
            {
                $query->where('authorization', Input::get('aso'));
            }
            $query->whereNotIn('id', $attached_ids);
        })->get();

        return View::make('plans.groups.creating-brands', compact('brands'));
    }

    public function getAddBrand()
    {
        $branchBrand = BranchBrand::findOrFail(Input::get('branch_brand_id'));

        $id = rand(100000, 999999);

        return View::make('plans.groups.add-brand', compact('branchBrand', 'id'));
    }

    public function postAttachBranch()
    {
        $branch_plan_group = BranchPlanGroup::where('branch_id', Input::get('branch_id'))->where('plan_group_id', Input::get('plan_group_id'))->first();

        if(! $branch_plan_group) {
            $branch_plan_group = BranchPlanGroup::create([
                'branch_id' => Input::get('branch_id'),
                'plan_group_id' => Input::get('plan_group_id'),
            ]);
        }

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

        return json_encode(['status' => 'success']);
    }


    public function getDelete($plan_group_id)
    {
        $plan_group = PlanGroup::findOrFail($plan_group_id);

        return View::make('plans.groups.delete', compact('plan_group'));
    }

    public function postDelete($plan_group_id)
    {
        $plan_group = PlanGroup::findOrFail($plan_group_id);
        $plan_group->delete();

        return json_encode(['code' => '0']);
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

    public function getEditBranch($group_id)
    {
        $group = BranchPlanGroup::with('branchBrands.brand')->findOrFail($group_id);
        $branchBrands =  [];

        foreach($group->branchBrands as $k => $branchBrand){
            $branchBrands[$branchBrand->brand->id]['id'] = $branchBrand->id;
            $branchBrands[$branchBrand->brand->id]['brand'] = $branchBrand->brand;
            $branchBrands[$branchBrand->brand->id]['authorization'] = $branchBrand->authorization;
            if($branchBrand->pivot->if_sold) {
                $branchBrands[$branchBrand->brand->id]['sold_yes'] = 1;
            }
            if(!$branchBrand->pivot->if_sold) {
                $branchBrands[$branchBrand->brand->id]['sold_no'] = 1;
            }
        }
        return View::make('plans.groups.edit-branch', compact('group', 'branchBrands'));
    }

    public function postUpdateBranch($group_id)
    {
        $branch_plan_group = BranchPlanGroup::findOrFail($group_id);
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

        return Redirect::to(url('plan/groups/show', [$branch_plan_group->plan_group_id]));
    }
}