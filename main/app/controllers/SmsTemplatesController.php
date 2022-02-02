<?php

class SmsTemplatesController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:szablony_sms#wejscie');
    }

    public function index()
    {
        $templates = SmsTemplates::whereActive(0)->get();
        return View::make('settings.sms_templates.index', compact('templates'));
    }

    public function create()
    {   
        return View::make('settings.sms_templates.create');
    }

    public function store()
    {
        SmsTemplates::create(array(
            'name' => Input::get('name'),
            'body' => Input::get('body')
        ));

        $response['code'] = 0;

        return json_encode($response);
    }

    public function edit($id)
    {
        $template = SmsTemplates::find($id);
        return View::make('settings.sms_templates.edit', compact('template'));
    }

    public function update($id)
    {
        $template = SmsTemplates::find($id);
        $template->name = Input::get('name');
        $template->body = Input::get('body');
        $template->save();

        $response['code'] = 0;

        return json_encode($response);
    }

    public function show($id)
    {
        $template = SmsTemplates::find($id);
        return View::make('settings.sms_templates.show', compact('template'));
    }

    public function delete($id)
    {
        $template = SmsTemplates::find($id);
        return View::make('settings.sms_templates.delete', compact('template'));

    }

    public function destroy($id)
    {
        $template = SmsTemplates::find($id);
        $template->active = 9;
        $template->save();

        $response['code'] = 0;

        return json_encode($response);
    }

}
