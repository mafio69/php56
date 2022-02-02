<?php

class IdeaController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:edycja_danych_rejestrowych_idea#wejscie');
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
	{

        $settings = Idea_data::all();
        $settingsA = array();
        foreach($settings as $setting)
        {
            $settingsA[$setting->owner_id][$setting->parameter_id] = $setting;
        }
     	$parameters = Idea_parameters::whereActive(0)->orderBy('name', 'asc')->get();
        $owners = Owners::whereActive(0)->get();

        return View::make('settings.idea.index', compact('settingsA', 'parameters', 'owners'));
	}


	public function getEdit($owner_id, $parameter_id)
	{
		$setting = Idea_data::whereOwner_id( $owner_id )->whereParameter_id($parameter_id)->first();

        $parameter = Idea_parameters::find($parameter_id);

        return View::make('settings.idea.edit', compact('setting', 'owner_id', 'parameter_id', 'parameter'));
	}

	public function set($owner_id, $parameter_id)
	{
        $setting = Idea_data::whereOwner_id( $owner_id )->whereParameter_id($parameter_id)->first();
        if($setting) {
            $setting->value = Input::get('value');

            $setting->last_user_edit = Auth::user()->id;

            $setting->save();
        }else{
           Idea_data::create(array(
                'owner_id'   => $owner_id,
                'parameter_id' => $parameter_id,
                'value'     => Input::get('value'),
                'last_user_edit' => Auth::user()->id
            ));
        }

		return '0';

	}

}
