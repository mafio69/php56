<?php

class SettingsSalesProgramsController extends \BaseController {

    public function __construct()
    {
    }

    public function getIndex()
	{
        $dls_programs = DlsProgram::get();
        $syjon_programs = SyjonProgram::get();

        return View::make('settings.sales-programs.index', compact('syjon_programs', 'dls_programs'));
	}


      public function getEdit($id)
      {
          $dls_program = DlsProgram::find($id);

            return View::make('settings.sales-programs.edit', compact('dls_program'));
      }

        public function postUpdate($id)
        {
            $inputs = Input::all();

            $dls_program = DlsProgram::find($id);

            $dls_program->update(['name_key'=>$inputs['name_key']]);

            $result['code'] = 0;
            return json_encode($result);
        }

}
