<?php

class PlansController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:programy#wejscie');
        $this->beforeFilter('permitted:programy#zarzadzaj', ['only' => ['getCreate', 'postStore', 'getDelete', 'postDelete', 'getEdit', 'postUpdate']]);
    }

	public function getIndex()
	{
		$plans = Plan::with('groups.branchPlanGroups')->orderBy('name')->paginate(Session::get('search.pagin', '10'));

		return View::make('plans.index', compact('plans'));
	}

	public function getCreate()
    {
        $sub = DB::table('syjon_programs')->select('name_key');
        $sales_program = DB::table('dls_programs')->select('name_key')->union($sub)->lists('name_key', 'name_key');
        $sales_program = ['' => '--- wybierz ---'] + $sales_program ;
        return View::make('plans.create', compact('sales_program'));
    }

    public function postStore()
    {
        Plan::create(Input::all());

        return Redirect::to('plans');
    }

    public function getDelete($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);

        return View::make('plans.delete', compact('plan'));
    }

    public function postDelete($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);

        $plan->groups()->delete();
        $plan->delete();

        return json_encode(['code' => 0]);
    }

    public function getShow($plan_id)
    {
        $plan = Plan::with('groups.branchPlanGroups', 'groups.companyGroups')->findOrFail($plan_id);

        return View::make('plans.show', compact('plan'));
    }

    public function getEdit($plan_id)
    {
        $plan = Plan::find($plan_id);

        $sub = DB::table('syjon_programs')->select('name_key');
        $sales_program = DB::table('dls_programs')->select('name_key')->union($sub)->lists('name_key', 'name_key');
        $sales_program = ['' => '--- wybierz ---'] + $sales_program ;

        return View::make('plans.edit', compact('plan', 'sales_program'));
    }

    public function postUpdate($plan_id)
    {
        $plan = Plan::find($plan_id);
        $plan->update(Input::all());

        return Redirect::to('plans/show/'.$plan_id);
    }
}