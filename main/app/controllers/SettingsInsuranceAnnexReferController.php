<?php

class SettingsInsuranceAnnexReferController extends \BaseController {

    /**
     * SettingsInsuranceAnnexReferController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:slownik_aneksow_ubezpieczen#wejscie');
    }

    public function index()
	{
        $annex_refers = LeasingAgreementAnnexRefer::all();

        return View::make('insurances.settings.annex_refers.index', compact('annex_refers'));
	}


  public function edit($id)
  {
        $item = LeasingAgreementAnnexRefer::find($id);

        return View::make('insurances.settings.annex_refers.edit', compact('item'));
  }

	public function update($id)
	{
        $inputs = Input::all();

				$item = LeasingAgreementAnnexRefer::find($id);

        $item->update(['name'=>$inputs['name']]);

        $result['code'] = 0;
        return json_encode($result);
	}

	public function create($id)
	{
        return View::make('insurances.settings.annex_refers.create');
	}

	public function store($id)
	{
        $inputs = Input::all();

        $item = LeasingAgreementAnnexRefer::create(['name'=>$inputs['name']]);

        $result['code'] = 0;
        return json_encode($result);
	}


}
