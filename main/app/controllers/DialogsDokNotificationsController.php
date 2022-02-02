<?php

class DialogsDokNotificationsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
    }

	public function getCancel($id)
	{
        return View::make('dok.notifications.dialog.cancel', compact('id'));
	}

	public function getInprogress($id)
	{
        return View::make('dok.notifications.dialog.inprogress', compact('id'));
	}

	public function getComplete($id)
	{
        return View::make('dok.notifications.dialog.complete', compact('id'));
	}
	
	public function getDocumentSet()
	{
		$input = Input::get('files');		
        return View::make('dok.notifications.dialog.document', compact('input'));
	}

	public function getDelDoc($id)
	{
        return View::make('dok.notifications.dialog.docDelete', compact('id'));
	}

	public function getDelDocConf($id)
	{
        return View::make('dok.notifications.dialog.docDeleteConf', compact('id'));
	}

    /**
     * @param $id - id zgloszenia
     * @return dialog z wyborem typu procesu dla zgłoszenia
     */
    public function getChangeProcess($id)
    {
        $notification = DokNotifications::find($id);
        $processes = DokProcesses::where('active', '=', '0')->where('parent_id', '=', 0)->get();
        return View::make('dok.notifications.dialog.changeProcess', compact('notification', 'processes'));
    }

    /**
     * Zapisanie zmiany w typie procesu
     * @param $id - id zgłoszenia
     */
    public function setChangeProcess($id)
    {
        $notification = DokNotifications::find($id);

        $old_process = DokProcesses::find($notification->process_id)->name;
        $new_process = DokProcesses::find(Input::get('process_id'))->name;

        $notification->process_id = Input::get('process_id');
        $notification->priority = 2;

        if( $notification->save() ){

            Histories::dok_history($notification->id, 9, Auth::user()->id, 'z '.$old_process.' na '.$new_process);

            $result['code'] = 0;
            return json_encode($result);
        }

    }
	
}
?>