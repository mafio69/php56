<?php

use Idea\Docer\Docer;
use Idea\DocGenerator\FileNotFoundException;
use Idea\Mail\Mailer;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InjuriesDocsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:kartoteka_szkody#dokumentacja#dodaj_dokument', ['only' => ['postDocument', 'setDocumentSet']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dokumentacja#wyslij_dokument', ['only' => ['getSendDocs', 'postSendDocs']]);
        $this->beforeFilter('permitted:kartoteka_szkody#zdjecia#dodaj_zdjecia', ['only' => ['postImage']]);
        $this->beforeFilter('permitted:kartoteka_szkody#zdjecia#usun_zdjecie', ['only' =>  ['setDelImage']]);
    }

    public static function downloadGenerateDoc($id) {
        ob_start();
        $file = InjuryFiles::find($id);

        $documentType = InjuryDocumentType::find($file->category);

        $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$file->file;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        $name = rand('10000','99999');
        // Prepare the headers
        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => finfo_file($finfo, $path),
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($path),
            'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
        );
        finfo_close($finfo);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        session_write_close();
        if (ob_get_contents()) ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        //$response->foundation->finish();

        exit;
    }

	public function getGenerateDocsInfo($id, $key)
	{
        $documentType = InjuryDocumentType::find($key);
        $injury = Injury::with(['activeInvoices' => function($query){
            $query->with('companyVatCheck');
        }])->find($id);
        $ideaOffices = IdeaOffices::whereActive(0)->get();
        $insuranceCompany=null;
        $insuranceCompany_emails = null;
        if($documentType->mail){
          $insuranceCompany = $injury->vehicle->insurance_company()->first();

          if($insuranceCompany)
          {
              preg_match_all("#[a-z\d!\#$%&'*+/=?^_{|}~-]+(?:\.[a-z\d!\#$%&'*+/=?^_{|}~-]+)*@(?:[a-z\d](?:[‌​a-z\d-]*[a-z\d])?\.)+[a-z\d](?:[a-z\d-]*[a-z\d])?#i", $insuranceCompany->email, $insuranceCompany_emails);
          }else{
              $insuranceCompany_emails = null;
          }
        }

        $branch = $injury->branch;

		return View::make('injuries.dialog.generateDocument', compact('id', 'key', 'documentType', 'injury', 'ideaOffices','insuranceCompany','insuranceCompany_emails', 'branch'));
	}

    public function generateDoc($id, $document_type_id)
    {
        Debugbar::disable();
        set_time_limit(120);
        ob_start();
        $injury = Injury::with('branch.company.groups')->find($id);
        $vehicle = $injury->vehicle;
        $owner = $vehicle->owner;

        $base_step = $injury->step;

        $inputs = Input::all();

        
        $branch = null;
        if(isset($inputs['branch'])) {
            $branch = $inputs['branch'];
        } elseif ($injury->branch_id > 0) {
            $branch = $injury->branch_id;
        }


        if (in_array($document_type_id, [52, 60])) {
            $injury->is_cas_case = 1;
            $injury->save();
        }
        $doc = new Idea\DocGenerator\DocGenerator($id, 'Injury', $document_type_id, $inputs, $branch);

        if ($owner->conditionalDocumentTemplate) {
            $contract_number = $vehicle->nr_contract;

            if (!preg_match('/.*\/\d{4}$/', $contract_number)) {
                $documentTemplate = $owner->conditionalDocumentTemplate;
            } else {
                $documentTemplate = $owner->documentTemplate;
            }
        } elseif ($owner->documentTemplate) {
            $documentTemplate = $owner->documentTemplate;
        } else {
            $documentTemplate = \DocumentTemplate::where('slug', 'default')->first();
        }

        if ($doc->getDocumentType()->mail) {
            $emails = [];
            $unmachedEmails = [];
            if (Input::has('custom_emails') && Input::get('custom_emails') != '') {
                $custom_emails = explode(',', Input::get('custom_emails'));

                foreach ($custom_emails as $custom_email) {
                    $custom_email = trim($custom_email);

                    if( !filter_var($custom_email, FILTER_VALIDATE_EMAIL) === false ) {
                        $emails[$custom_email] = $custom_email;
                    }else{
                        $unmachedEmails[] = $custom_email;
                    }
                }
            }
          if(Input::has('insuranceCompanies'))
                {
                    foreach(Input::get('insuranceCompanies') as $insuranceCompany)
                    {
                        if( !filter_var($insuranceCompany, FILTER_VALIDATE_EMAIL) === false ) {
                            $emails[$insuranceCompany] = $insuranceCompany;
                        }else{
                            $unmachedEmails[] = $insuranceCompany;
                        }
                    }
                }
          if(Input::has('special_emails')) {
              foreach (Input::get('special_emails') as $special_email) {
                  $special_email = trim($special_email);

                  if (!filter_var($special_email, FILTER_VALIDATE_EMAIL) === false) {
                      $emails[$special_email] = $special_email;
                  } else {
                      $unmachedEmails[] = $special_email;
                  }
              }
          }
            if(count($emails) > 0 ) {
                $templatePath = $doc->templatePath().'_mail';

                $vehicle = $injury->vehicle()->first();

                $subject="ZGŁOSZENIE SZKODY NR REJ ".$vehicle->registration;
;
                $owner = $injury->vehicle->owner()->first();
                $owner_group = $owner->group;

                Mail::send($templatePath, ['inputs' => $inputs, 'injury' => $injury, 'owner' => $owner, 'branch' => $injury->branch, 'owner_group' => $owner_group, 'documentTemplate' => $documentTemplate], function ($message) use ($emails, $subject) {
                    $message->subject($subject);

                    foreach ($emails as $email) {
                        $message->to($email);
                    }
                });

                $mailer = new Mailer();
                foreach ($emails as $email) {
                    $mailer->addAddress($email);
                }
                $mailer->setSubject($subject);
                $html = View::make($templatePath, ['inputs' => $inputs, 'injury' => $injury, 'owner' => $owner, 'branch' => $injury->branch, 'owner_group' => $owner_group, 'documentTemplate' => $documentTemplate])->render();
                $mailer->setBody($html);
                $mail_filename = substr(md5(time().'xx'.rand(0, 9999)), 7, 16).'.eml';
                file_put_contents(\Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$mail_filename, $mailer->getEml());
                InjuryFiles::create(array(
                    'injury_id' => $injury->id,
                    'type' => 4,
                    'category' => 16,
                    'document_type' => 'InjuryUploadedDocumentType',
                    'document_id' => 16,
                    'user_id' => Auth::user()->id,
                    'file' => $mail_filename,
                    'name' => 'Wysłanie druku - Zgłoszenia szkody do TU przez EDB'
                ));


                Histories::history($injury->id, 196);
            }
        }

        $filename = str_random();
        $fs = new Illuminate\Filesystem\Filesystem();

        $view = $doc->generateDocView();
        if(preg_match('/^.+\.(([pP][dD][fF]))$/', $view)){
            //plik pdf
        }else {
            $fs->put(base_path('converter/' . $filename . '.html'), $view);

            $cmd = 'node convert.js --template=' . $documentTemplate->slug . ' --infilename=' . $filename . '.html --outfilename=' . $filename . '.pdf --path="' . $doc->savePath() . '"';

            if ($doc->getDocumentType()->if_pure == 1) {
                $cmd .= ' --mode=pure';
            }

            $process = new Process($cmd);
            $process->setWorkingDirectory(base_path('converter'));
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }
        $filename = $doc->getFilename();


        $file = InjuryFiles::create(array(
            'injury_id' => $id,
            'type' => 3,
            'category' => $document_type_id,
            'document_type' => 'InjuryDocumentType',
            'document_id' => $document_type_id,
            'user_id' => Auth::user()->id,
            'file' => $filename,
            'name' => ($doc->getDocumentType()->fee == 1 && Input::get('issue_fee') == 0) ? 'Wygenerowano bez naliczenia opłat: ' . Input::get('reason') : '',
            'if_fee' => (Input::has('issue_fee') && Input::get('issue_fee') == 1) ? 1 : 0,
            'if_fee_collected' => (!is_null(Input::has('if_fee_collection')) && (!is_null(Input::get('if_fee_collection'))) ? Input::get('if_fee_collection') : 0)
        ));

        if($doc->getDocumentType()->fee == 1 && Input::get('issue_fee') == 0)
            $reason = " <b>Wygenerowano bez naliczenia opłat: ".Input::get('reason')."</b>";
        else
            $reason = '';

        Histories::history($id, 126, Auth::user()->id, '-1', 'Kategoria '.$doc->getDocumentType()->name.' - <a target="_blank" href="'.URL::route('injuries-downloadGenerateDoc', array($file->id)).'">pobierz</a>'.$reason);

        if($injury->vehicle->cfm==1){
          //Zał. nr 2-upoważnienie na serwis współpracujący z Idea Fleet
          if($doc->getDocumentType()->id==53){
              $injury->task_authorization = 1;
          }
        }
        else{
          if($doc->getDocumentType()->task_authorization == 1)
              $injury->task_authorization = 1;
        }



        if($doc->getDocumentType()->fee == 1 && Input::has('issue_fee') && Input::get('issue_fee') == 1 && $injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1){

            $contract = $injury->vehicle->nr_contract;
            $issuedate = $injury->date_event;
            $issuenumber = $injury->case_nr;
            $issuetype = 'B';
            $username = substr(Auth::user()->login, 0, 10);
            $owner_id = $injury->vehicle->owner_id;
            $feeamount = '0';

            $data = new Idea\Structures\ADDISSUEFEEInput($issuenumber, $feeamount, $username);

            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('addIssueFee');

            $xml = $webservice->getResponseXML();

            if( $xml->Error->ErrorCde == 'ERR0000' ){
                $injury->issue_fee = $xml->feeAmount;
            }else if($xml->Error->ErrorCde == 'ERR0010'){

                $data = new Idea\Structures\REGINSISSUEInput($contract,$issuedate, $issuenumber, $issuetype, $username);

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                $xml = $webservice->getResponseXML();

                if( $xml->Error->ErrorCde != 'ERR0000' ) {
                    $injury->issue_fee = '-1';
                }else{
                    $data = new Idea\Structures\ADDISSUEFEEInput($issuenumber, $feeamount, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('addIssueFee');

                    $xml = $webservice->getResponseXML();

                    if( $xml->Error->ErrorCde == 'ERR0000' )
                        $injury->issue_fee = $xml->feeAmount;
                    else
                        $injury->issue_fee = '-1';
                }
            }else
                $injury->issue_fee = '-1';
        }elseif(Input::has('issue_fee') && Input::get('issue_fee') == 1){
			$injury->issue_fee = 1;
		}else
            $injury->issue_fee = 0;

        $injury->touch();
        $injury->save();

        switch($document_type_id){
            case in_array($document_type_id, [11, 102]):
                if( $wreck = $injury->wreck ){
                    if(Input::has('alert'))
                        $wreck->alert_repurchase = Input::get('alert');

                    if($wreck->expire_tender == '0000-00-00' || !$wreck->expire_tender) {
                        $wreck->expire_tenderer = Date('Y-m-d', strtotime("+7 days"));
                    }

                    $wreck->save();

                    $injury->total_status_id = 1;
                    $injury->save();

                    InjuryStatusesHistory::create([
                        'injury_id' => $injury->id,
                        'user_id'   => Auth::user()->id,
                        'status_id' => 1,
                        'status_type' => 'InjuryTotalStatuses'
                    ]);
                }
                break;
            case 15:
                if(!$injury->totalRepair){
                    InjuryTotalRepair::create(array(
                       'injury_id'  => $id,
                        'alert_receive' => Input::get('alert')
                    ));

                    $injury->total_status_id = 9;
                    $injury->save();

                    InjuryStatusesHistory::create([
                        'injury_id' => $injury->id,
                        'user_id'   => Auth::user()->id,
                        'status_id' => 9,
                        'status_type' => 'InjuryTotalStatuses'
                    ]);
                }

                break;
            case 16:
                $injury_wreck = $injury->wreck;
                if($injury_wreck->alert_buyer == '0000-00-00')
                {
                    if(Input::has('alert'))
                        $injury_wreck->alert_buyer = Input::get('alert');
                    $injury_wreck->save();
                }
                break;
            case 17:
                $injuryTotal = $injury->totalRepair;

                if($injuryTotal->repair_agreement_date == '0000-00-00') {
                    $injuryTotal->date_document_confirmation = Input::get('date_document_confirmation');
                    $injuryTotal->email_document_confirmation = Input::get('email_document_confirmation');
                    $injuryTotal->repair_agreement_date = date('Y-m-d');
                    $injuryTotal->save();
                }

                $vehicle = $injury->vehicle;

                $data = array(
                    'injury' => $injury,
                    'vehicle' => $vehicle,
	                'logo' => public_path() . '/assets/css/images/idea-getin-logo.png'
                );

                $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$doc->getDocumentType()->short_name."/".$file->file;

                Mail::send('injuries.card_file.repair_total.mail_templates.agreement', $data, function($message) use ($injuryTotal, $path) {
                    $subject = '[IdeaLeasing] Zgoda na naprawę szkody całkowitej';
                    $message->subject($subject);
                    $message->attach($path, array('as' => 'Zgoda na naprawę szkody całkowitej.pdf'));
                    $message->to($injuryTotal->email_document_confirmation);
                });

                break;
            case 62:
                if ($injury->step = 40) {
                    $injury->step = 41;
                    $injury->save();
                }
                
                break;
            case 69:
                if( $wreck = $injury->wreck ){
                    if(Input::has('sending_date')) {
                        $wreck->alert_repurchase = Input::get('sending_date');
                    }   else {
                        $wreck->alert_repurchase = date('Y-m-d');
                    }

                    //$wreck->expire_tenderer = Date('Y-m-d', strtotime("+7 days"));
                    $wreck->save();

                    $injury->total_status_id = 1;
                    $injury->save();

                    InjuryStatusesHistory::create([
                        'injury_id' => $injury->id,
                        'user_id'   => Auth::user()->id,
                        'status_id' => 1,
                        'status_type' => 'InjuryTotalStatuses'
                    ]);
                }
                break;

        }


        if (in_array($document_type_id, [6, 49, 52, 60]) && $injury->step == 10) {
            $branch = Branch::with('company', 'company.groups')->find($injury->branch_id);

            if ($branch && $branch->company->groups->contains(1) || ($branch->company->groups->contains(5) && $injury->vehicle->cfm == 1)) {
                $injury->step = 11;
                $injury->save();
            }
        }

        $stages = InjuryStepStage::where('injury_step_id', $injury->step)->whereHas( 'documentTypes', function($query) use($document_type_id){
                                    $query->where( 'injury_document_type_id', $document_type_id);
                                })->get();

        $qualified = false;
        $status_update = true;
        foreach($stages as $stage) {
            if ($stage->condition && !$qualified) {

                $qualified = false;

                switch ($stage->id) {
                    case 5:
                        if ($injury->receive_id == 3 || in_array($document_type_id, [109, 68])) {
                            $qualified = true;
                        }
                        break;
                    case 6:
                        if ($injury->receive_id == 1) {
                            $qualified = true;
                        }
                        break;

                    case 32:
                        if ($injury->receive_id == 3) {
                            $qualified = true;
                        }
                        break;
                    case 33:
                        if ($injury->receive_id == 1) {
                            $qualified = true;
                        }
                        break;
                }
            }else{
                $qualified = true;
            }

            if($qualified)
            {
                break;
            }
        }

        if($qualified) {
	        $next_step_proceed = true;
            $stage_to_update = $stage->parent_stage_id ? $stage->parent_stage_id : $stage->id;

	        if ( in_array($injury->step, ['15', '16', '17', '18', '19', '21', '23', '24', '25'])) {

		        if (in_array($injury->step, ['15', '16', '17', '19', '21', '25'])) {
			        $next_step_proceed = false;
		        } elseif ($injury->step == '18') {
			        if (!in_array($stage->next_injury_step_id, [15, 24, 23])) {
				        $next_step_proceed = false;
			        }
		        } elseif ($injury->step == '23') {
			        if (!in_array($stage->next_injury_step_id, [15, 24])) {
				        $next_step_proceed = false;
			        }
		        } elseif ($injury->step == '24') {
			        if (!in_array($stage->next_injury_step_id, [15])) {
				        $next_step_proceed = false;
			        }
		        }
            }
            
	        if($next_step_proceed) {
                if ($status_update) $injury->update(['injury_step_stage_id' => $stage_to_update]);

		        InjuryStepStageHistory::create([
			        'injury_id' => $injury->id,
			        'injury_step_stage_id' => $stage_to_update
		        ]);

		        if ($stage->next_injury_step_id) {
			        if (
				        $injury->branch_id != '-1' &&
				        $injury->branch_id != '0' &&
				        (
					        $injury->branch->company->groups->contains(1)
					        ||
					        ($injury->branch->company->groups->contains(5) && $injury->vehicle->cfm == 1)
				        ) &&
				        $injury->edb()->count() > 0
			        ) {
				        $if_edb = true;
			        } else {
				        $if_edb = false;
			        }

			        switch ($stage->id) {
				        case 5:
					        if ($injury->receive_id == 3 || !$if_edb || in_array($document_type_id, [109, 68])) {
                                if ($status_update) {
                                    $injury->update(['step' => $stage->next_injury_step_id, 'injury_step_stage_id' => $stage_to_update]);
                                } else {
                                    $injury->update(['step' => $stage->next_injury_step_id]);
                                }

						        if ($stage->next_injury_step_id == 23) {
							        Histories::history($injury->id, 174);
						        } elseif ($stage->next_injury_step_id == 24) {
							        Histories::history($injury->id, 173);
						        }
					        }
					        break;
				        case 6:
					        if ($injury->receive_id == 1 || !$if_edb || in_array($document_type_id, [109, 68])) {
                                if ($status_update) {
                                    $injury->update(['step' => $stage->next_injury_step_id, 'injury_step_stage_id' => $stage_to_update]);
                                } else {
                                    $injury->update(['step' => $stage->next_injury_step_id]);
                                }

						        if ($stage->next_injury_step_id == 23) {
							        Histories::history($injury->id, 174);
						        } elseif ($stage->next_injury_step_id == 24) {
							        Histories::history($injury->id, 173);
						        }
					        }
					        break;

				        default:
					        $next_step_proceed = true;
					        if ($stage->next_step_condition == 1) {
						        if ($stage->next_injury_step_id == 24 && $injury->document(2, 6)->where('active', 0)->first()) {
							        $next_step_proceed = false;
						        }
					        }
					        if ($next_step_proceed) {
						        $injury->update(['step' => $stage->next_injury_step_id, 'injury_step_stage_id' => $stage_to_update]);

						        if ($stage->next_injury_step_id == 23) {
							        Histories::history($injury->id, 174);
						        } elseif ($stage->next_injury_step_id == 24) {
							        Histories::history($injury->id, 173);
						        }
					        }
					        break;
			        }
		        }
	        }
        }

        if(in_array($injury->step, [30,31,32,33,34,35,36,37])) {
            $stage_step = InjuryStepTotal::wherenotNull('injury_total_statuse_id')->where('injury_steps', 'LIKE', '%' . $injury->step . '%')->whereHas('documentTypes', function ($query) use ($document_type_id) {
                $query->where('injury_document_type_id', $document_type_id);
            })->first();
            if ($stage_step) {
                InjuryStepTotalHistory::create([
                    'injury_id'            => $injury->id,
                    'injury_step_total_id' => $stage_step->id
                ]);
                $injury->update(['total_status_id' => $stage_step->injury_total_statuse_id]);
            }

            $stage_status = InjuryStepTotal::wherenotNull('injury_step_id')->whereHas('documentTypes', function ($query) use ($document_type_id) {
                $query->where('injury_document_type_id', $document_type_id);
            })->first();
            if ($stage_status) {
                $injury->update(['step' => $stage_status->injury_step_id]);
            }
        }
        if(in_array($injury->step, [40,41,42,43,44,45,46])) {
            $stage_step = InjuryStepTheft::wherenotNull('injury_theft_statuse_id')->where('injury_steps', 'LIKE', '%' . $injury->step . '%')->whereHas('documentTypes', function ($query) use ($document_type_id) {
                $query->where('injury_document_type_id', $document_type_id);
            })->first();
            if ($stage_step) {
                InjuryStepTheftHistory::create([
                    'injury_id'            => $injury->id,
                    'injury_step_theft_id' => $stage_step->id
                ]);
                $injury->update(['theft_status_id' => $stage_step->injury_theft_statuse_id]);
            }

            $stage_status = InjuryStepTheft::wherenotNull('injury_step_id')->whereHas('documentTypes', function ($query) use ($document_type_id) {
                $query->where('injury_document_type_id', $document_type_id);
            })->first();
            if ($stage_status) {
                if($stage_status->injury_step_id == 41){
                    Idea\AsService\AsService::theft($injury->id);
                }
                $injury->update(['step' => $stage_status->injury_step_id]);
            }
        }

        if($base_step != $injury->step)
        {
            if(in_array($injury->step, array(
                '-10', '-7', 15, 16, 17, 18, 21, 23, 24, 25, 26, 34, 35, 45, 44, 36, 37
            ))){
                $injury->date_end = date('Y-m-d H:i:s');

                $step = InjurySteps::findOrFail($injury->step);
                switch ($step->injury_group_id){
                    case 1:
                        $injury->date_end_normal = date("Y-m-d H:i:s");
                        break;
                    case 2:
                        $injury->date_end_total = date("Y-m-d H:i:s");
                        break;
                    case 3:
                        $injury->date_end_theft = date("Y-m-d H:i:s");
                        break;
                }

                $injury->save();
            }
        }

        if (in_array($document_type_id, [52, 60])) {

            $history = $injury->historyEntries()->where('history_type_id', 207)->first();

            if (!$history) {
                if ($injury->contact_person == 1) {
                    if ($injury->driver_id != '') {
                        $driver = Drivers::find($injury->driver_id);
                        $phone_nb = trim($driver->phone);
                        $phone_nb = str_replace(' ', '', $phone_nb);
                    } else
                        $phone_nb = '';
                } else {
                    $phone_nb = trim($injury->notifier_phone);
                    $phone_nb = str_replace(' ', '', $phone_nb);
                }

                if ($phone_nb != '') {
                    if ($injury->vehicle->cfm == 1) {
                        $msg = 'W związku z dokonanym zgłoszeniem szkody, oraz skierowaniem zlecenia naprawy do serwisu naprawczego, informujemy, że w zakresie niezbędnym do realizacji tej usługi Administratorem Państwa danych osobowych jest Centrum Asysty Szkodowej Sp. z o.o. z siedzibą we Wrocławiu, przy ul. Gwiaździstej 66, kod pocztowy 53 - 413. Szczegółowe informacje znajdują się na stronie https://www.cas-auto.pl/RODO';
                    } else {
                        $msg = 'Wobec zgłoszenia przez Państwo chęci skorzystania z oferty naprawy pojazdu, w naszej sieci serwisów współpracujących, informujemy, że w zakresie niezbędnym do realizacji tej usługi Administratorem Państwa danych osobowych jest Centrum Asysty Szkodowej Sp. z o.o. z siedzibą we Wrocławiu, przy ul. Gwiaździstej 66, kod pocztowy 53 - 413. Szczegółowe informacje znajdują się na stronie https://www.cas-auto.pl/RODO';
                    }

                    send_sms($phone_nb, $msg);

                    Histories::history($injury->id, 207, Auth::user()->id, $phone_nb);
                }
            }
        }

        $noteAvailabilities = $doc->getDocumentType()->notes;

        foreach($noteAvailabilities as $noteAvailability)
        {
            if(
                ($noteAvailability->receive_id && $injury->receive_id != $noteAvailability->receive_id)
                ||
                ! $injury->sap
            ){
                continue;
            }

            $sap = new \Idea\SapService\Sap();
            $notes[0] = $noteAvailability->note;
            $result = $sap->szkodaNotUtworz($injury, $notes);

            $errors = [];
            if(isset($result['ftReturn']) && is_array($result['ftReturn'])){
                foreach($result['ftReturn'] as $ftReturn){
                    if($ftReturn['typ'] =='E'){
                        $errors[] = $ftReturn;
                    }
                }
            }

            if(count($errors) > 0){
                Flash::error('Wystąpił błąd w trakcie wysyłki notatek.');
            }else{
                foreach($result['ftNotatkaN'] as $note_item => $note){
                    $injuryNote = InjuryNote::create([
                        'referenceable_id' => $file->id,
                        'referenceable_type' => 'InjuryFiles',
                        'injury_id' => $injury->id,
                        'user_id' => Auth::user()->id,
                        'roknotatki' => $note['roknotatki'],
                        'nrnotatki'=> $note['nrnotatki'],
                        'obiekt'=> $note['obiekt'],
                        'temat'=> $note['temat'],
                        'data'=> $note['data'],
                        'uzeit'=> $note['uzeit'],
                    ]);

                    $file->note()->associate($injuryNote);
                    $file->save();
                }
            }
        }


        if(in_array($document_type_id, [60, 52]) && $injury->sap && !in_array($injury->sap_rodzszk, ['TOT', 'KRA'])){
            $injury->update(['sap_rodzszk' => 'CZA']);
            $sap = new \Idea\SapService\Sap();
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($injury->id, 217, Auth::user()->id);
            }
        }

        return URL::route('injuries-downloadGenerateDoc', array($file->id));
    }

    public static function downloadDoc($id) {
        ob_start();
        $file = InjuryFiles::find($id);
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/files/".$file->file;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        $name = rand('10000','99999');
        // Prepare the headers

        $content_type = finfo_file($finfo, $path);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if($ext == 'eml'){
            $content_type = 'message/rfc822';
        }

        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => $content_type,
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($path),
            'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
        );
        finfo_close($finfo);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        session_write_close();
        if (ob_get_contents()) ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        //$response->foundation->finish();

        exit;
    }

    public function previewDoc($id, $type = null)
    {
    	if($type == 'filename') {

	    }elseif($type  == 'letter') {
	    	$file = InjuryLetter::find($id);
	    }else{
		    $file = InjuryFiles::find($id);
	    }

	    if($type == 'filename') {
		    $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/" . $id;
	    }elseif($type && $type != 'letter'){
		    $documentType = InjuryDocumentType::find($type);
		    $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/" . $file->file;
	    }else {
		    $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/" . $file->file;
	    }

	    $response = Response::make(File::get($path), 200);
	    $finfo = finfo_open(FILEINFO_MIME_TYPE);
	    $response->header('Content-Type', finfo_file($finfo, $path));
	    finfo_close($finfo);

	    return $response;
    }

    public function postImage($id, $key)
    {

        $id_injury = $id;
        $category = $key;

        $input = Input::all();
        $rules = array(
            'file' => 'image',
        );

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        {
            return Response::json(array('status' => 'error', 'description' => 'przesłany plik nie jest zdjęciem'));
        }

        $randomKey  = sha1( time() . microtime() );

        $extension  = Input::file('file')->getClientOriginalExtension();

        $filename   = $randomKey.'.'.$extension;

        $path       = '/images/full';
        $path_min       = '/images/min';
        $path_thumb       = '/images/thumb';

        if (!file_exists(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path)) {
            mkdir(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path, 666, true);
        }
        if (!file_exists(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_min)) {
            mkdir(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_min, 666, true);
        }
        if (!file_exists(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_thumb)) {
            mkdir(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path_thumb, 666, true);
        }

        // Move the file and determine if it was succesful or not
        $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );

        $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename)->resize(320, null, true);
        $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_min.'/'.$filename);

        $img = Image::make(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path.'/'.$filename)->resize(null, 100, true);
        $img->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').$path_thumb.'/'.$filename);

        if( $upload_success ) {

            $image =InjuryFiles::create(array(
                'injury_id' => $id_injury,
                'type'		=> 1,
                'category'	=> $category,
                'user_id'	=> Auth::user()->id,
                'file'		=> $filename,
            ));

            Histories::history($id_injury, 22, Auth::user()->id, 'Kategoria '.Config::get('definition.imageCategory.'.$category).' - <a target="_blank" href="'.URL::route('injuries-downloadImg', array($image->id)).'">pobierz</a>');

            return Response::json(array('status' => 'success', 'file' => $filename));
        } else {
            return Response::json(array('status' => 'error'));
        }
    }

    public function postDocument($id){
        $id_injury = $id;

        $input = Input::all();

        $randomKey  = sha1( time() . microtime() );

        $extension  = Input::file('file')->getClientOriginalExtension();

        $filename   = $randomKey.'.'.$extension;

        $path       = '/files';

        // Move the file and determine if it was succesful or not
        $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );

        if( $upload_success ) {

            $file = InjuryFiles::create(array(
                'injury_id' => $id_injury,
                'type'		=> 2,
                'category'	=> 0,
                'user_id'	=> Auth::user()->id,
                'file'		=> $filename,
            ));

            return Response::json(array('status' => 'success', 'file' => $filename, 'id' => $file->id));
        } else {
            return Response::json(array('status' => 'error'));
        }
    }

    public function setDocumentDel(){
        if(Input::has('files')) {
            $input = Input::get('files');
            $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/files/';
            foreach ($input as $k => $v) {
                $img = InjuryFiles::find($v);
                File::delete(public_path() . $path . $img->file);
                $img->delete();
            }
        }
        echo '0';
    }

    public function setDocumentSet(){
        $input = Input::get('files');
        $fileType = Input::get('fileType');
        $uploadedDocumentType = InjuryUploadedDocumentType::find($fileType);
        if($uploadedDocumentType->subtypes->count() > 0){
            $fileType = Input::get('fileSubType');
        }

        foreach ($input as $k => $v) {
            $file = InjuryFiles::find($v);
            Docer::setDocumentType($file, $fileType, Input::get('amount'), Input::get('content'));
        }
        $injury = Injury::find($file->injury_id);

        Docer::processDocumentInjury($injury, $fileType);

        return $file->category;
    }

    public function setDelDoc($id)
    {
        $file = InjuryFiles::find($id);
        $file->active = '9';
        $file->touch();

        $sap = new Idea\SapService\Sap();
        $notesToRemove = [];
        if($file->type == 2 && ($file->category == 3 || $file->category == 4) ){
            $invoices = InjuryInvoices::where('injury_files_id', '=', $id)->get();

            foreach($invoices as $k => $invoice){
                $invoice->active = 9;
                $invoice->touch();
                $invoice->save();

                if($invoice->note){
                    $notesToRemove[] = $invoice->note;
                }
            }
        }
        if($file->type == 2 && ($file->category == 6 || $file->category == 37) ){
            $compensations = InjuryCompensation::where('injury_files_id', '=', $id)->get();
            foreach($compensations as $k => $compensation){
                if($compensation->note){
                    $notesToRemove[] = $compensation->note;
                }

                if(Input::get('premium') == 1){
                    $sap = new Idea\SapService\Sap();

                    if($compensation->premium)
                    {
                        $compensation->premium->delete();
                        $compensation->delete();
                        $sap->szkoda($compensation->injury);
                    }elseif($compensation->mode == 1){
                        $compensation_value = $compensation->compensation;

                        $compensation->update(['compensation' => 0]);
                        $sap->szkoda($compensation->injury);

                        $compensation->update(['compensation' => $compensation_value]);
                        $compensation->delete();
                    }
                }else {
                    $compensation->delete();
                }
            }
        }




        if ($file->type == 2 && $file->category == 2) {
            $estimates = InjuryEstimate::where('injury_file_id', '=', $id)->get();
            foreach ($estimates as $k => $estimate) {
                $estimate->delete();
            }
        }
        if ($file->category == 46) {
            $injury = Injury::find($file->injury_id);
            $injury->if_doc_fee_enabled = true;
            $injury->save();
        }


        if(count($notesToRemove) > 0)
        {
            $result = $sap->szkodaNotKasuj($file->injury, $notesToRemove);
            if(isset($result['ftNotatkaKeys'])){
                foreach($result['ftNotatkaKeys'] as $notatkaKey){
                    InjuryNote::where('injury_id', $file->injury->id)->where('roknotatki', $notatkaKey['roknotatki'])->where('nrnotatki', $notatkaKey['nrnotatki'])->delete();
                }
            }else{
                Flash::error('Wystąpił błąd w trakcie usuwania notatek.');
            }
        }

        Histories::history($file->injury_id, 21, Auth::user()->id, '<a target="_blank" href="' . URL::route('injuries-downloadDoc', array($id)) . '">pobierz</a>');
        if ($file->save()) echo $id;
    }

    public function setDelDocConf($id)
    {
        $file = InjuryFiles::find($id);
        $file->active = '9';
        $file->touch();
        Histories::history($file->injury_id, 21, Auth::user()->id, '-1', '<a target="_blank" href="'.URL::route('injuries-downloadGenerateDoc', array($id)).'">pobierz</a> Przyczyna usunięcia:'.Input::get('content'));

        $injury = Injury::find($file->injury_id);
        if($file->category == 6 && $file->type == 3 && $injury->step == 11)
        {
            $injury->update(['step' => 10]);
        }


        if($file->note)
        {
            $sap = new Idea\SapService\Sap();
            $notesToRemove[] = $file->note;
            $result = $sap->szkodaNotKasuj($file->injury, $notesToRemove);
            if(isset($result['ftNotatkaKeys'])){
                foreach($result['ftNotatkaKeys'] as $notatkaKey){
                    InjuryNote::where('injury_id', $file->injury->id)->where('roknotatki', $notatkaKey['roknotatki'])->where('nrnotatki', $notatkaKey['nrnotatki'])->delete();
                }
            }else{
                Flash::error('Wystąpił błąd w trakcie usuwania notatek.');
            }
        }

        if(in_array($file->category, [60, 52]) && $injury->sap && !in_array($injury->sap_rodzszk, ['TOT', 'KRA'])){
            $injury->update(['sap_rodzszk' => 'CZ']);
            $sap = new \Idea\SapService\Sap();
            $result = $sap->szkoda($injury);

            if($result['status'] == 200){
                Flash::message('Szkoda zaktualizowana w SAP');
            }else {
                Session::flash('show.modal.in.the.next.request', $result['msg']);
            }

            if(in_array( $result['status'], [200, 300])){
                Histories::history($injury->id, 217, Auth::user()->id);
            }
        }

        if( $file->save() ) echo $id;
    }

    public static function downloadImg($id) {
        ob_start();
        $file = InjuryFiles::find($id);
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/images/full/".$file->file;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        $name = rand('10000','99999');
        // Prepare the headers
        $headers = array(
            'Content-Description' => 'File Transfer',
            'Content-Type' => finfo_file($finfo, $path),
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
            'Content-Length' => File::size($path),
            'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
        );
        finfo_close($finfo);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== '') {
            Session::save();
        }

        // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
        if (ob_get_contents()) session_write_close();
        ob_end_clean();
        $response->sendHeaders();
        if ($file = fopen($path, 'rb')) {
            while (!feof($file) and (connection_status() == 0)) {
                print(fread($file, 1024 * 8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        //$response->foundation->finish();

        exit;
    }

    public function setDelImage($id)
    {
        $file = InjuryFiles::find($id);
        $file->active = '9';
        $file->touch();
        Histories::history($id, 23, Auth::user()->id, '<a target="_blank" href="'.URL::route('injuries-downloadImg', array($id)).'">pobierz</a>');
        if( $file->save() ) echo $id;
    }

    public function getSendDocs($injury_id)
    {
        $docsToSend = InjuryFiles::whereIn('id', Input::get('docs_to_send'))->get();
        $injury = Injury::find($injury_id);

        $client = $injury->client;
        $notifier = $injury->notifier_email;
        $driver = $injury->driver;
        $branch = $injury->branch;
        if($client){
            preg_match_all("#[a-z\d!\#$%&'*+/=?^_{|}~-]+(?:\.[a-z\d!\#$%&'*+/=?^_{|}~-]+)*@(?:[a-z\d](?:[‌​a-z\d-]*[a-z\d])?\.)+[a-z\d](?:[a-z\d-]*[a-z\d])?#i", $client->email, $client_emails);
        }else{
            $client_emails = null;
        }

        if ($branch){
            preg_match_all("#[a-z\d!\#$%&'*+/=?^_{|}~-]+(?:\.[a-z\d!\#$%&'*+/=?^_{|}~-]+)*@(?:[a-z\d](?:[‌​a-z\d-]*[a-z\d])?\.)+[a-z\d](?:[a-z\d-]*[a-z\d])?#i", $branch->email, $branch_emails);
        }else{
            $branch_emails = null;
        }

        $insuranceCompany = $injury->insuranceCompany;

        if($insuranceCompany)
        {
            preg_match_all("#[a-z\d!\#$%&'*+/=?^_{|}~-]+(?:\.[a-z\d!\#$%&'*+/=?^_{|}~-]+)*@(?:[a-z\d](?:[‌​a-z\d-]*[a-z\d])?\.)+[a-z\d](?:[a-z\d-]*[a-z\d])?#i", $insuranceCompany->email, $insuranceCompany_emails);
        }else{
            $insuranceCompany_emails = null;
        }

        return View::make('injuries.dialog.send-docs', compact('docsToSend', 'injury', 'insuranceCompany', 'insuranceCompany_emails', 'client', 'notifier', 'driver', 'branch', 'branch_emails', 'client_emails'));
    }

    public function postSendDocs($injury_id)
    {
        $injury = Injury::find($injury_id);

        $lastHistoryRecord = InjuryHistory::where('injury_id', $injury_id)->where('user_id', Auth::user()->id)->where('history_type_id', '168')->orderBy('id', 'desc')->first();
        if($lastHistoryRecord)
        {
            $now = \Carbon\Carbon::now();

            if( $now->diffInSeconds($lastHistoryRecord->created_at) < 5){
                Log::info('mało '.Auth::user()->id);
                return json_encode([
                    'code' => 3
                ]);
            }
        }

        $docsToSend = InjuryFiles::whereIn('id', Input::get('doc_ids') )->with('document_type')->get();

        $emails = [];
        $unmachedEmails = [];
        $template_name = null;

        if(Input::has('template_name')){
            $template_name = Input::get('template_name');
            if(!$template_name){
                return json_encode([
                    'code' => 2,
                    'error' => 'Nieprawidłowy wzór'
                ]);
            }
        }

        if(Input::has('addressees')) {
            foreach (Input::get('addressees') as $addresse_type => $email) {
                $email = trim($email);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $emails[$email] = $email;
                } else {
                    $unmachedEmails[] = $email;
                }
            }
        }

        if(Input::has('special_emails')) {
            foreach (Input::get('special_emails') as $special_email) {
                $special_email = trim($special_email);

                if (!filter_var($special_email, FILTER_VALIDATE_EMAIL) === false) {
                    $emails[$special_email] = $special_email;
                } else {
                    $unmachedEmails[] = $special_email;
                }
            }
        }

        if(Input::has('custom_emails') && Input::get('custom_emails') != '')
        {
            $custom_emails = explode(',', Input::get('custom_emails'));

            foreach ($custom_emails as $custom_email)
            {
                $custom_email = trim($custom_email);

                if( !filter_var($custom_email, FILTER_VALIDATE_EMAIL) === false ) {
                    $emails[$custom_email] = $custom_email;
                }else{
                    $unmachedEmails[] = $custom_email;
                }
            }
        }

        if(Input::has('clients'))
        {
            foreach(Input::get('clients') as $client)
            {
                if( !filter_var($client, FILTER_VALIDATE_EMAIL) === false ) {
                    $emails[$client] = $client;
                }else{
                    $unmachedEmails[] = $client;
                }
            }
        }

        if(Input::has('branches'))
        {
            foreach(Input::get('branches') as $branch)
            {
                if( !filter_var($branch, FILTER_VALIDATE_EMAIL) === false ) {
                    $emails[$branch] = $branch;
                }else{
                    $unmachedEmails[] = $branch;
                }
            }
        }

        if(Input::has('insuranceCompanies'))
        {
            foreach(Input::get('insuranceCompanies') as $insuranceCompany)
            {
                if( !filter_var($insuranceCompany, FILTER_VALIDATE_EMAIL) === false ) {
                    $emails[$insuranceCompany] = $insuranceCompany;
                }else{
                    $unmachedEmails[] = $insuranceCompany;
                }
            }
        }

        /*
        $user_email = Auth::user()->email;

        if($user_email && !filter_var($user_email, FILTER_VALIDATE_EMAIL) === false)
            $emails[$user_email] = $user_email;
        */

        $email_comment = Input::get('email_comment');
        
        if(count($emails) > 0 ) {
              Queue::push('Idea\Mail\MailQueue', array(
                'injury' => $injury,
                'docsToSend' => $docsToSend->toArray(),
                'email_comment' => $email_comment,
                'emails'=>$emails,
                'injury_id' => $injury_id,
                'template_name' => $template_name,
                'doc_ids'=> \Input::get('doc_ids'),
                'user_id'=> Auth::user()->id,
                'url'=> url('injuries'),
              ));
        }

        return json_encode([
            'code' => 2,
            'error' => 'Wysłano wiadomość na adresy: '.implode(',', $emails)
        ]);
    }
    public function downloadDocs($injury_id)
    {
        $docsToSend = InjuryFiles::whereIn('id', Input::get('docs_to_send'))->get();
        $injury = Injury::find($injury_id);

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
              $documentType = InjuryDocumentType::find($doc->category);

              $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER')."/".$documentType->short_name."/".$doc->file;
            }

            $zip->addFile($path,$doc->file);
        }
        $zip->close();

        return Response::download($zipname,'files-'.str_replace('/','-',$injury->case_nr).'.zip');

    }

    public function setDocumentMatchSap($compensation_id)
    {
        $compensation = InjuryCompensation::with('injury_file')->findOrFail($compensation_id);

        if(Input::has('mode')) {
            $compensation->update(['mode' => 1, 'compensation' => $compensation->injury->sap->kwotaOdsz]);
            $compensation->injury->compensations()->where('id', '!=', $compensation_id)->update(['mode' => 2]);
        }elseif(Input::has('new_premium')){
            $compensation->update(['is_premiumable' => 1, 'mode' => 2]);
        }elseif(Input::has('new')){
            $compensation->update(['mode' => 1]);
        }else {
            $sapPremium = InjurySapPremium::findOrFail(Input::get('injury_sap_premium_id'));
            $compensation->update(['is_premiumable'=> 1, 'mode' => 2, 'compensation' => $sapPremium->kwDpl, 'date_decision' => $sapPremium->dataDpl]);

            $sapPremium->update(['injury_compensation_id' => $compensation_id]);
        }
        $decisionTypes = InjuryCompensationDecisionType::lists('name', 'id');
        $receives = Receives::lists('name', 'id');

        return View::make('injuries.dialog.edit-compensation', compact('compensation', 'decisionTypes', 'receives'))->render();
    }

    public function getGenerateVDeskTextView($id) {
        return View::make('injuries.dialog.V-desk-text', compact('id'));
    }

    public function getGenerateVDeskText($id, $amount) {
        $invoice = InjuryInvoices::with('injury.vehicle')->findOrFail($id);
        $injury = $invoice->injury;
        // if(!count($invoice->assignedBankAccountNumbersWithTrashed)) {
        //     return json_encode([
        //         'code' => 200,
        //         'data' => "Nie przypisany numer konta do faktury {$invoice->invoice_nr}"
        //     ]);
        // }
        $compensationsSum = 0;
        $amount = number_format(checkIfEmpty($amount, null, 0), 2, ",", " ");
        $brand = checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand);
        $model = checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model);
        $text = 
        "Faktura za naprawę pojazdu {$brand} {$model}, Nr rejestracyjny {$injury->vehicle->registration}\n"
        ."Do umowy leasingu  {$injury->vehicle->nr_contract}\n"
        ."Ubezpieczyciel wypłacił kwotę:\n";

        foreach($invoice->compensations as $compensation) {
            if($compensation->injury_compensation_decision_type_id == 7) $compensation->compensation = abs($compensation->compensation) * -1;
            $tempValue =number_format(checkIfEmpty($compensation->compensation, null, 0), 2, ",", " ");
            $text .= "{$tempValue} zł".' '.checkIfEmpty(Config::get('definition.compensationsNetGross.'.$compensation->net_gross))." na konto ".$compensation->receive->name."\n";
            $compensationsSum += $compensation->compensation;
        }
        $compensationsSum = number_format(checkIfEmpty($compensationsSum, null, 0), 2, ",", " ");
        
        $text .= "RAZEM: {$compensationsSum} zł\n"
        ."Proszę o rozliczenie kwoty zgodnie z załączonymi dokumentami.\n"
        ."Do zapłaty: {$amount} zł\n";

        if($invoice ? $invoice->companyVatCheck ? $invoice->companyVatCheck->status_code == 'C': false : false) {
            $text .= "\nVAT CZYNNY na dzień przekazania faktury.";
        }
        if(true
        /*is_null($invoice->assignedBankAccountNumbersWithTrashed) ? $invoice->assignedBankAccountNumbersWithTrashed->first()->deleted_at : false*/ ) {
            $text .= "\nRachunek bankowy na BIAŁEJ LIŚCIE. ";
        }

       return json_encode([
            'code' => 200,
            'data' => $text
        ]);
    }

}
