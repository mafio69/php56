<?php

class DosOtherInjuriesCardDialogsController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia#zarzadzaj');
    }


    public function getEditInjury($id)
    {
        $injury = DosOtherInjury::find($id);
        $injuries_type = DosInjuryType::all();
        $receives = Receives::all();
        $invoicereceives = Invoicereceives::all();
        $type_incident = DosOtherInjuryTypeIncident::orderBy('order', 'asc')->get();
        $insurance_company = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();

        return View::make('dos.other_injuries.dialog.edit', compact('id', 'injuries_type', 'receives', 'type_incident', 'injury', 'invoicereceives', 'insurance_company'));
    }

    public function getEditInjuryInsurance($id)
    {
        $injury = DosOtherInjury::find($id);
        $object = $injury->object;


        return View::make('dos.other_injuries.dialog.edit-insurance', compact('id', 'injury', 'object'));
    }

    public function getEditInjuryClientContact($id)
    {
        $injury = DosOtherInjury::with('client')->find($id);

        return View::make('dos.other_injuries.dialog.edit-clientContact', compact('id', 'injury'));
    }

    public function getEditInjuryInfo($id)
    {
        $injury = DosOtherInjury::find($id);
        $info = Text_contents::find($injury->info);


        return View::make('dos.other_injuries.dialog.edit-info', compact('id', 'info', 'injury'));
    }

    public function getEditInjuryMap($id)
    {
        $injury = DosOtherInjury::find($id);

        return View::make('dos.other_injuries.dialog.edit-map', compact('id', 'injury'));
    }

    public function getEditInvoice($id)
    {
        $invoice = DosOtherInjuryInvoices::where('id', '=', $id)->with('injury_files')->get()->first();

        return View::make('dos.other_injuries.dialog.edit-invoice', compact('id', 'invoice'));
    }

    public function getDelImage($id)
    {
        return View::make('dos.other_injuries.dialog.imageDelete', compact('id'));
    }

    public function getAddHistory($id)
    {
        return View::make('dos.other_injuries.dialog.add-history', compact('id'));
    }

    public function getDocumentSet()
    {
        $input = Input::get('files');
        return View::make('dos.other_injuries.dialog.document', compact('input'));
    }

    public function getDelDoc($id)
    {
        return View::make('dos.other_injuries.dialog.docDelete', compact('id'));
    }

    public function getDelDocConf($id)
    {
        return View::make('dos.other_injuries.dialog.docDeleteConf', compact('id'));
    }

    public function getInprogress($id)
    {
        return View::make('dos.other_injuries.dialog.getInprogress', compact('id'));
    }

    public function getComplete($id)
    {
        return View::make('dos.other_injuries.dialog.complete', compact('id'));
    }

    public function getCompleteL($id)
    {
        return View::make('dos.other_injuries.dialog.complete-l', compact('id'));
    }

    public function getCompleteN($id)
    {
        return View::make('dos.other_injuries.dialog.complete-n', compact('id'));
    }

    public function getCompletedPayment($id)
    {
        return View::make('dos.other_injuries.dialog.completed-payment', compact('id'));
    }

    public function getCompletedRefuse($id)
    {
        return View::make('dos.other_injuries.dialog.completed-refuse', compact('id'));
    }

    public function getCompletedWithoutRepaired($id)
    {
        return View::make('dos.other_injuries.dialog.completed-without-repaired', compact('id'));
    }

    public function getCancel($id)
    {
        return View::make('dos.other_injuries.dialog.cancel', compact('id'));
    }

    public function getClaimsResignation($id)
    {
        return View::make('dos.other_injuries.dialog.claims-resignation', compact('id'));
    }

    public function getUnlock($id)
    {
        return View::make('dos.other_injuries.dialog.unlock', compact('id'));
    }

    public function getLock($id)
    {
        return View::make('dos.other_injuries.dialog.lock', compact('id'));
    }

    public function getRestoreCanceled($id)
    {
        return View::make('dos.other_injuries.dialog.restoreCanceled', compact('id'));
    }

    public function getRestore($id)
    {
        return View::make('dos.other_injuries.dialog.restore', compact('id'));
    }

    public function getRestoreDeleted($id)
    {
        return View::make('dos.other_injuries.dialog.restoreDeleted', compact('id'));
    }

    public function getRestoreCompleted($id)
    {
        return View::make('dos.other_injuries.dialog.restoreCompleted', compact('id'));
    }

    public function getRestoreTotalFinished($id)
    {
        return View::make('dos.other_injuries.dialog.restoreTotalFinished', compact('id'));
    }

    public function getRefusal($id)
    {
        return View::make('dos.other_injuries.dialog.refusal', compact('id'));
    }

    public function getTotal($id)
    {
        return View::make('dos.other_injuries.dialog.total', compact('id'));
    }
    public function getTotalPayment($id)
    {
        return View::make('dos.other_injuries.dialog.total-payment', compact('id'));
    }
    public function getTotalRefuse($id)
    {
        return View::make('dos.other_injuries.dialog.total-refuse', compact('id'));
    }

    public function getTotalFinished($id)
    {
        return View::make('dos.other_injuries.dialog.totalFinished', compact('id'));
    }

    public function getTheft($id)
    {
        return View::make('dos.other_injuries.dialog.theft', compact('id'));
    }
    public function getTheftPayment($id)
    {
        return View::make('dos.other_injuries.dialog.theft-payment', compact('id'));
    }
    public function getTheftRefuse($id)
    {
        return View::make('dos.other_injuries.dialog.theft-refuse', compact('id'));
    }

    public function getTheftFinishedPayment($id)
    {
        return View::make('dos.other_injuries.dialog.theft-finished-payment', compact('id'));
    }
    public function getTheftFinishedRefuse($id)
    {
        return View::make('dos.other_injuries.dialog.theft-finished-refuse', compact('id'));
    }

    public function getTotalFinishedPayment($id)
    {
        return View::make('dos.other_injuries.dialog.total-finished-payment', compact('id'));
    }
    public function getTotalFinishedRefuse($id)
    {
        return View::make('dos.other_injuries.dialog.total-finished-refuse', compact('id'));
    }

    public function getEditInjuryOffender($id)
    {
        $injury = DosOtherInjury::find($id);
        $offender = $injury->offender()->first();
        return View::make('dos.other_injuries.dialog.edit-offender', compact('id', 'offender'));
    }

    public function getEditObject($id)
    {
        $injury = DosOtherInjury::find($id);
        $object =$injury->object;

        $assetTypes = ObjectAssetType::lists('name', 'id');

        return View::make('dos.other_injuries.dialog.edit-object', compact('id', 'object', 'assetTypes'));
    }


    public function getEditNotifier($id){
        $injury = DosOtherInjury::find($id);

        return View::make('dos.other_injuries.dialog.edit-notifier', compact('id', 'injury'));
    }


    public function getAssignLeader($injury_id)
    {
        $users = User::where('login', '!=', 'default')
            ->orderBy('name')->lists('name', 'id');

        return View::make('dos.other_injuries.dialog.assign-leader', compact('injury_id', 'users'));
    }

    public function getRemoveLeader($injury_id)
    {
        return View::make('dos.other_injuries.dialog.remove-leader', compact('injury_id'));
    }

    public function getMarkAsLeader($injury_id)
    {
        return View::make('dos.other_injuries.dialog.mark-as-leader', compact('injury_id'));
    }

    public function getEditCompensation($id)
    {
        $compensation = DosOtherInjuryCompensation::with('injury_file')->find($id);
        $decisionTypes = InjuryCompensationDecisionType::lists('name', 'id');
        $receives = Receives::lists('name', 'id');

        return View::make('dos.other_injuries.dialog.edit-compensation', compact('compensation', 'decisionTypes', 'receives'));
    }

    public function getDeleteCompensation($id)
    {
        $compensation = DosOtherInjuryCompensation::find($id);

        return View::make('dos.other_injuries.dialog.delete-compensation', compact('compensation'));
    }

    public function docsSendDialog($injury_id)
    {
        $docsToSend = DosOtherInjuryFiles::whereIn('id', Input::get('docs_to_send'))->get();
        $injury = DosOtherInjury::find($injury_id);

        $client = $injury->client;
        $notifier = $injury->notifier_email;
        if($client){
            preg_match_all("#[a-z\d!\#$%&'*+/=?^_{|}~-]+(?:\.[a-z\d!\#$%&'*+/=?^_{|}~-]+)*@(?:[a-z\d](?:[‌​a-z\d-]*[a-z\d])?\.)+[a-z\d](?:[a-z\d-]*[a-z\d])?#i", $client->email, $client_emails);
        }else{
            $client_emails = null;
        }

        $insuranceCompany = $injury->object->insurance_company()->first();

        if($insuranceCompany)
        {
            preg_match_all("#[a-z\d!\#$%&'*+/=?^_{|}~-]+(?:\.[a-z\d!\#$%&'*+/=?^_{|}~-]+)*@(?:[a-z\d](?:[‌​a-z\d-]*[a-z\d])?\.)+[a-z\d](?:[a-z\d-]*[a-z\d])?#i", $insuranceCompany->email, $insuranceCompany_emails);
        }else{
            $insuranceCompany_emails = null;
        }

        return View::make('dos.other_injuries.dialog.send-docs', compact('docsToSend', 'injury', 'insuranceCompany', 'insuranceCompany_emails', 'client', 'notifier', 'client_emails'));
    }

    public function downloadDocs($injury_id)
    {
        $docsToSend = DosOtherInjuryFiles::whereIn('id', Input::get('docs_to_send'))->get();
        $injury = DosOtherInjury::find($injury_id);

        $zipname = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/zip/'.time().str_random(5).'.zip';
        if(!file_exists(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/zip')){
            mkdir(Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/zip');
        }
        $zip = new \ZipArchive;
        $zip->open($zipname, \ZipArchive::CREATE);
        foreach ($docsToSend as $doc) {
            if($doc->type == 2) {
                $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$doc->file;
            }
            else{
                $documentType = DosOtherInjuryDocumentType::find($doc->category);

                $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$doc->file;
            }   

            $zip->addFile($path,$doc->file);
        }
        $zip->close();

        return Response::download($zipname,'files-'.str_replace('/','-',$injury->case_nr).'.zip');

    }

    public function getCancelMobile($id)
    {
        return View::make('dos.other_injuries.dialog.cancel-mobile', compact('id'));
    }
    
    public function getDelete($id)
    {
        return View::make('dos.other_injuries.dialog.delete', compact('id'));
    }

    public function getSettled($id)
    {
        return View::make('dos.other_injuries.dialog.settled', compact('id'));
    }

    public function getTotalSettled($id)
    {
        return View::make('dos.other_injuries.dialog.totalSettled', compact('id'));
    }

    public function getTheftSettled($id)
    {
        return View::make('dos.other_injuries.dialog.theftSettled', compact('id'));
    }

}
