<?php

class SmsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:bramka_sms#wejscie');

    }

    public function index()
    {
        $templates = SmsTemplates::whereActive(0)->get();
        return View::make('sms.index', compact('templates'));
    }

    public function send()
    {

        $phone_nb = trim(Input::get('phone_number'));
        $phone_nb = str_replace(' ', '', $phone_nb);

        $msg = Input::get('bodySMS').' Pozdrawiam '.Auth::user()->name;

        $result = send_sms($phone_nb, $msg);

        $result = iconv( "iso-8859-2", "utf-8", $result );

        Flash::message($result);
        return Redirect::route('sms.index');

    }

    /**
     * Wysyłanie sms z bramki w ramach danej szkody
     * @param $id - id szkody
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send_i($id)
    {

        $phone_nb = trim(Input::get('phone_number'));
        $phone_nb = str_replace(' ', '', $phone_nb);

        $msg = Input::get('bodySMS').' Pozdrawiam '.Auth::user()->name;

        $result = send_sms($phone_nb, $msg);

        $result = iconv( "iso-8859-2", "utf-8", $result );

        Histories::history($id, 141, Auth::user()->id, '-1', $msg);

        Flash::message($result);
        return Redirect::route('injuries-info', compact('id'));

    }



}
