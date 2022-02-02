<?php


namespace Idea\Docer;


use Auth;
use Flash;
use Histories;
use Idea\AsService\AsService;
use Injury;
use InjuryCompensation;
use InjuryEstimate;
use InjuryInvoices;
use InjuryNote;
use InjurySteps;
use InjuryStepStage;
use InjuryStepStageHistory;
use InjuryStepTheft;
use InjuryStepTheftHistory;
use InjuryStepTotal;
use InjuryStepTotalHistory;
use InjuryUploadedDocumentType;
use Session;
use URL;

class Docer
{
    public static function setDocumentType($file, $fileType, $amount = null, $content = null)
    {
        $file->category = $fileType;
        $file->name = $amount ? $amount : $content;
        $file->document_type = 'InjuryUploadedDocumentType';
        $file->document_id = $fileType;
        $file->save();

        $documentType = InjuryUploadedDocumentType::find($fileType);

        Histories::history($file->injury_id, 20, Auth::user()->id, 'Kategoria '.$documentType->name.' - <a target="_blank" href="'.URL::route('injuries-downloadDoc', array($file->id)).'">pobierz</a>');
        $injury = Injury::find($file->injury_id);

        if($fileType == 3 || $fileType == 4){

            InjuryInvoices::create(array(
                    'initial_company_vat_check_id' => ($injury->branch && $injury->branch->company->companyVatCheck) ? $injury->branch->company->companyVatCheck->id : null,
                    'injury_id' 		=> $file->injury_id,
                    'injury_files_id'	=> $file->id,
                    'invoicereceives_id'=> $file->injury()->first()->invoicereceives_id,
                    'created_at'		=> $file->created_at,
                    'updated_at'		=> $file->updated_at
                )
            );
        }
        if($fileType == 6 || $fileType == 37)
        {
            if($file->injury->compensations()->where('mode', 1)->count() > 0) $mode = 2;
            else $mode = 1;

            InjuryCompensation::create(array(
                'injury_id' => $file->injury_id,
                'injury_files_id'	=> $file->id,
                'user_id' => Auth::user()->id,
                'mode' => $mode
            ));
        }

        if($fileType == 2)
        {
            InjuryEstimate::create(array(
                'injury_id' => $file->injury_id,
                'injury_file_id'	=> $file->id,
                'user_id' => Auth::user()->id
            ));
        }

        if($fileType == 46) {
            $injury = Injury::find($file->injury_id);
            $injury->if_doc_fee_enabled = false;
            $injury->save();
        }

        $noteAvailabilities = $documentType->notes;
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

        if($fileType == 46){
            $injury = Injury::find($file->injury_id);
            $injury->if_doc_fee_enabled = false;
            $injury->save();

        }

        return $file;
    }

    public static function processDocumentInjury($injury, $fileType)
    {
        $uploadedDocumentType = InjuryUploadedDocumentType::find($fileType);
        $base_step = $injury->step;
        $branch = $injury->branch;

        if(!in_array($injury->step, [34,35,44, 45, '-7'])) {
            if(in_array($injury->step, [30, 31, 32, 33, 36, 37])) {
                $stage_step = InjuryStepTotal::wherenotNull('injury_total_statuse_id')->where('injury_steps', 'LIKE', '%' . $injury->step . '%')->whereHas('uploadedDocumentTypes', function ($query) use ($fileType) {
                    $query->where('injury_uploaded_document_type_id', $fileType);
                })->first();
                if ($stage_step) {
                    InjuryStepTotalHistory::create([
                        'injury_id'            => $injury->id,
                        'injury_step_total_id' => $stage_step->id
                    ]);
                    $injury->update(['total_status_id' => $stage_step->injury_total_statuse_id]);
                }

                $stage_status = InjuryStepTotal::wherenotNull('injury_step_id')->whereHas('uploadedDocumentTypes', function ($query) use ($fileType) {
                    $query->where('injury_uploaded_document_type_id', $fileType);
                })->get();
                if (count($stage_status)) {
                    $accept_zu = false;
                    $decline = false;
                    foreach ($injury->documents as $document) {
                        if ($document->type == 2) {
                            if ($document->document_id == 6) {
                                $accept_zu = true;
                            }
                            if (in_array($document->document_id, [25,26,27,28,29,30,31,32,33,34,35,36])) {
                                $decline = true;
                            }
                        }
                    }
                    foreach($stage_status as $status) {
                        if($injury->step == 37 && ($accept_zu || $decline || $document->document_id == 20)){

                        }elseif ($status->injury_step_id == 34) {
                            if ($accept_zu) {
                                $injury->update(['step' => $status->injury_step_id]);
                                Histories::history($injury->id, 180, Auth::user()->id);
                                break;
                            }
                        } elseif ($status->injury_step_id == 35 && $injury->step != 34) {
                            if ($decline) {
                                $injury->update(['step' => $status->injury_step_id]);
                                Histories::history($injury->id, 181, Auth::user()->id);
                                break;
                            }
                        }elseif($status->injury_step_id == 32) {
                            $injury->update(['step' => $status->injury_step_id]);
                            AsService::total($injury->id);
                            break;
                        } elseif (!in_array($status->injury_step_id, [34,35])) {
                            $injury->update(['step' => $status->injury_step_id]);
                            break;
                        }
                    }
                }
            }elseif(in_array($injury->step, [40,41,42,43,46])) {
                $stage_step = InjuryStepTheft::wherenotNull('injury_theft_statuse_id')->where('injury_steps', 'LIKE', '%' . $injury->step . '%')->whereHas('uploadedDocumentTypes', function ($query) use ($fileType) {
                    $query->where('injury_uploaded_document_type_id', $fileType);
                })->first();
                if ($stage_step) {
                    InjuryStepTheftHistory::create([
                        'injury_id'            => $injury->id,
                        'injury_step_theft_id' => $stage_step->id
                    ]);
                    $injury->update(['theft_status_id' => $stage_step->injury_theft_statuse_id]);
                }

                $stage_status = InjuryStepTheft::wherenotNull('injury_step_id')->whereHas('uploadedDocumentTypes', function ($query) use ($fileType) {
                    $query->where('injury_uploaded_document_type_id', $fileType);
                })->get();

                if (count($stage_status)) {
                    $accept_zu = false;
                    $decline = false;
                    foreach ($injury->documents as $document) {
                        if ($document->type == 2) {
                            if (in_array($document->document_id, [25,26,27,28,29,30,31,32,33,34,35,36])) {
                                $decline = true;
                            }
                            if ($document->document_id == 6) {
                                $accept_zu = true;
                            }
                        }
                    }
                    foreach($stage_status as $status) {
                        if($injury->step == 46 && ($accept_zu || $decline || $document->document_id == 20)) {

                        }elseif ($status->injury_step_id == 44) {
                            if ($accept_zu) {
                                $injury->update(['step' => $status->injury_step_id]);
                                Histories::history($injury->id, 179, Auth::user()->id);
                                break;
                            }
                        } elseif ($status->injury_step_id == 45 && $injury->step != 44) {
                            if ($decline) {
                                $injury->update(['step' => $status->injury_step_id]);
                                Histories::history($injury->id, 178, Auth::user()->id);
                                break;
                            }
                        }elseif($status->injury_step_id == 43 && $injury->step == 42) {
                        } elseif (!in_array($status->injury_step_id, [44,45])) {
                            $injury->update(['step' => $status->injury_step_id]);
                            break;
                        }
                    }
                }
            }else {
                $next_step_proceed = true;
                $stage = InjuryStepStage::where('injury_step_id', $injury->step)->whereHas('uploadedDocumentTypes', function ($query) use ($fileType) {
                    $query->where('injury_uploaded_document_type_id', $fileType);
                })->first();


                if ( in_array($injury->step, ['15', '16', '17', '18', '19', '21', '23', '24', '25'])) {
                    if(in_array($injury->step, ['15', '16', '17', '19', '21', '25'])){
                        $next_step_proceed = false;
                    }elseif($injury->step == '18'){
                        if(! in_array($fileType, [6, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36])){
                            $next_step_proceed = false;
                        }
                        if ($stage && !in_array($stage->next_injury_step_id, [15,24,23])) {
                            $next_step_proceed = false;
                        }
                    }elseif($injury->step == '23'){
                        if(! in_array($fileType, [6, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36])){
                            $next_step_proceed = false;
                        }

                        if ($stage && !in_array($stage->next_injury_step_id, [15,24])) {
                            $next_step_proceed = false;
                        }
                    }elseif($injury->step == '24'){
                        if(! in_array($fileType, [6])){
                            $next_step_proceed = false;
                        }

                        if ($stage && !in_array($stage->next_injury_step_id, [15])) {
                            $next_step_proceed = false;
                        }
                    }

                    /*
                	if(in_array($fileType, [25, 26]))
                    {
                        $next_step_proceed = false;
                    }
                    */
                    /*
                    if ($injury->step == 21) { //rozliczona EDB
                        $next_step_proceed = false;
                    } elseif ($injury->step == 23) { // ZAKOŃCZONA - WYSTAWIONO UPOWAŻNIENIE

                    } elseif ($injury->step == 15) { //ZAKOŃCZONA WYPŁATĄ
                        if (!in_array($stage->next_injury_step_id, [11])) {
                            $next_step_proceed = false;
                        }
                    } elseif ($injury->step == 24) { //ZAKOŃCZONA ODMOWĄ TU

                    }
                    */
                }

                if ($next_step_proceed && $fileType == 6) {
                    if (
                        $branch
                        &&
                        (
                            $branch->company->groups->contains(1)
                            ||
                            (
                                $branch->company->groups->contains(5)
                                &&
                                $injury->vehicle->cfm == 1
                            )
                        )
                        &&
                        in_array($injury->step, ['11'])
                        &&
                        $injury->edb()->count() > 0
                    ) {
                        $injury->update(['step' => 14, 'injury_step_stage_id' => $stage ? $stage->id : null]);
                        Histories::history($injury->id, 162);
                    }elseif(in_array($injury->step, ['0', '10', '24', '23', '26'])){
                        $injury->update(['step' => 15]);
                        Histories::history($injury->id, 114);
                    }elseif(in_array($injury->step, ['14', '22'])){
                        $injury->update(['step' => 14]);
                        Histories::history($injury->id, 162);
                    }
                } elseif ($next_step_proceed && ($fileType == 4 || $fileType == 3) ) {
                    if ($branch && ($branch->company->groups->contains(1) || ($branch->company->groups->contains(5) && $injury->vehicle->cfm == 1)) && !in_array($injury->step, ['15', '16', '17', '18', '19', '21', '23', '24', '25']) && $injury->edb()->count() > 0) {
                        $injury->update(['step' => 14, 'injury_step_stage_id' => $stage ? $stage->id : null]);
                        Histories::history($injury->id, 162);
                    } else {
                        //$injury->update(['step' => 13]);
                    }
                }

                if ($next_step_proceed && $stage && $stage->next_injury_step_id) {
                    if ($stage->next_step_condition == 1) {
                        if ($branch && ($branch->company->groups->contains(1) || ($branch->company->groups->contains(5) && $injury->vehicle->cfm == 1))) {
                            $next_step_proceed = true;
                        } elseif ($stage->next_injury_step_id == 24 && $injury->getDocument(2, 6)->where('active', 0)->first()) {
                            $next_step_proceed = false;
                        }
                    }

                    if ($next_step_proceed) {
                        InjuryStepStageHistory::create([
                            'injury_id' => $injury->id,
                            'injury_step_stage_id' => $stage->id
                        ]);

                        $injury->update(['step' => $stage->next_injury_step_id, 'injury_step_stage_id' => $stage ? $stage->id : null]);

                        if ($stage->next_injury_step_id == 23) {
                            Histories::history($injury->id, 174);
                        } elseif ($stage->next_injury_step_id == 24) {
                            Histories::history($injury->id, 173);
                        }
                    }
                }
            }
        }

        if(in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46,'-7'] ) && $uploadedDocumentType->id == 27) //ustawić etap sprawy dla całek
        {
            $stage = InjuryStepStage::whereHas('uploadedDocumentTypes', function ($query) use ($fileType) {
                $query->where('injury_uploaded_document_type_id', $fileType);
            })->orderBy('id')->first();

            if($stage)
            {
                $injury->update([
                    'injury_total_step_stage_id' => $stage->id
                ]);
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

        if(
            $injury->sap && !in_array($injury->sap_rodzszk, ['TOT', 'KRA']) && in_array($fileType, [6]) && (int)$injury->sap_stanszk <= 2
        ){
            $sap = new \Idea\SapService\Sap();
            $injury->update(['sap_stanszk' => 2]);
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

        if(
            $injury->sap && !in_array($injury->sap_rodzszk, ['TOT', 'KRA']) && in_array($fileType, [25,26,27,28,29,30,31,32,33,34,35,36])
        ){
            $sap = new \Idea\SapService\Sap();
            $injury->update(['sap_stanszk' => 3]);
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
    }
}