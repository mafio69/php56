<?php

class ApiModulesController extends \BaseController {

	public function getIndex()
	{
		$modules = ApiModule::with('apiKeys')->get();

		return View::make('settings.api.modules.index', compact('modules'));
	}

	public function getKeys($module_id)
    {
        $module = ApiModule::findOrFail($module_id);
        $keys = ApiModuleKey::where('api_module_id', $module_id)->withTrashed()->get();

        return View::make('settings.api.modules.keys', compact('module', 'keys'));
    }

    public function getCreateKey($module_id)
    {
        $module = ApiModule::findOrFail($module_id);

        return View::make('settings.api.modules.create-key', compact('module'));
    }

    public function postGenerateKey($module_id)
    {
        do{
            $api_key = Str::random();
            $key = ApiModuleKey::where('api_key', $api_key)->first();
        }while($key);

        ApiModuleKey::create([
            'api_module_id' => $module_id,
            'api_key' => $api_key
        ]);

        return json_encode(['code' => 2, 'error' => 'API KEY: '.$api_key]);
    }

    public function getDisactivateKey($api_module_key_id)
    {
        $apiKey = ApiModuleKey::findOrFail($api_module_key_id);

        return View::make('settings.api.modules.disactivate-key', compact('apiKey'));
    }

    public function postDisactivateKey($api_module_key_id)
    {
        $apiKey = ApiModuleKey::findOrFail($api_module_key_id);
        $apiKey->delete();

        return json_encode(['code' => 0]);
    }

    public function getHistory($module_id)
    {
        $module = ApiModule::find($module_id);
        $query = ApiHistory::where('api_module_id', $module_id)->orderBy('id', 'desc');

        if(Input::has('ids'))
        {
            $ids = explode(',', Input::get('ids'));
            $query->whereIn('id', $ids);
        }

        $histories = $query->paginate(Session::get('search.pagin', '10'));

        return View::make('settings.api.modules.history', compact('histories', 'module'));
    }
}