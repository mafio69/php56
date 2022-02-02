<?php

class SettingsContractorGroupsController extends \BaseController {

    /**
     * SettingsContractorGroupsController constructor.
     */
    public function __construct()
    {
    }

    public function getIndex()
	{
        $contractor_groups = ContractorGroup::all();

        return View::make('settings.contractor-groups.index', compact('contractor_groups'));
	}


  public function getEdit($id)
  {
        $item = ContractorGroup::find($id);

        return View::make('settings.contractor-groups.edit', compact('item'));
  }

	public function postUpdate($id)
	{
        $inputs = Input::all();

        $item = ContractorGroup::find($id);

        $item->update(['name'=>$inputs['name']]);

        $result['code'] = 0;
        return json_encode($result);
	}

	public function getCreate()
	{
        return View::make('settings.contractor-groups.create');
	}

	public function postStore()
	{
        $inputs = Input::all();

        ContractorGroup::create(['name'=>$inputs['name']]);

        $result['code'] = 0;
        return json_encode($result);
	}

    public function getDelete($id)
    {
        $item = ContractorGroup::find($id);

        return View::make('settings.contractor-groups.delete', compact('item'));
    }

    public function postDelete($id)
    {
        $item = ContractorGroup::find($id);

        $item->delete();

        $result['code'] = 0;
        return json_encode($result);
    }
}
