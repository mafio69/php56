<?php

class ApiSyjonController extends \BaseController {

	public function syncPrograms()
    {
        foreach( Input::get('programs', []) as $api_program){
            $program = SyjonProgram::where('syjon_program_id', $api_program['id'])->first();
            if(! $program){
                SyjonProgram::create([
                   'syjon_program_id' => $api_program['id'],
                   'name' => $api_program['name'],
                   'name_key' => $api_program['name_key']
                ]);
            }else{
                $program->update($api_program);
            }
        }

        return Response::json(['success']);
    }

}