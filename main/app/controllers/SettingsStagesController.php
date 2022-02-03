<?php

class SettingsStagesController extends \BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zarzadzanie_etapami#wejscie');
    }

    public function index()
    {
        $stages = InjuryStepStage::orderBy('injury_step_id')->with('step', 'uploadedDocumentTypes', 'documentTypes', 'nextInjuryStep')->get();
        return View::make('settings.stages.index', compact('stages'));
    }

    public function totalStep()
    {
        $stages = InjuryStepTotal::with( 'stage', 'uploadedDocumentTypes', 'documentTypes')->get();

        return View::make('settings.stages.total-step', compact('stages'));
    }

    public function totalStatus()
    {
        $stages = InjuryStepTotal::with( 'stage', 'uploadedDocumentTypes', 'documentTypes')->get();

        return View::make('settings.stages.total-status', compact('stages'));
    }

    public function theftStep()
    {
        $stages = InjuryStepTheft::with( 'stage', 'uploadedDocumentTypes', 'documentTypes')->get();

        return View::make('settings.stages.theft-step', compact('stages'));
    }

    public function theftStatus()
    {
        $stages = InjuryStepTheft::with( 'stage', 'uploadedDocumentTypes', 'documentTypes')->get();

        return View::make('settings.stages.theft-status', compact('stages'));
    }

    public function uploadedDocumentTypes($stage_id, $type)
    {
        if($type == 1){
            $stage = InjuryStepStage::with('uploadedDocumentTypes')->find($stage_id);
        }elseif($type == 2){
            $stage = InjuryStepTotal::with('uploadedDocumentTypes')->find($stage_id);
        }elseif($type == 3){
            $stage = InjuryStepTheft::with('uploadedDocumentTypes')->find($stage_id);
        }

        $uploadedDocumentTypes = [];
        $uploadedDocumentTypesDb = InjuryUploadedDocumentType::whereNull('hidden')->with('subtypes')->orderBy('ordering')->get();
        foreach ($uploadedDocumentTypesDb as $uploadedDocumentType)
        {
            if($uploadedDocumentType->subtypes->count() == 0){
                $uploadedDocumentTypes[$uploadedDocumentType->id] = $uploadedDocumentType->name;
            }
        }

        return View::make('settings.stages.uploaded-document-types', compact('stage', 'uploadedDocumentTypes','type'));
    }

    public function updateUploadedDocumentTypes($stage_id, $type)
    {
        if($type == 1){
            $stage = InjuryStepStage::find($stage_id);
            $stage->uploadedDocumentTypes()->sync(Input::get('types', []));
        }elseif($type == 2){
            $stage = InjuryStepTotal::find($stage_id);
            $stage->uploadedDocumentTypes()->sync(Input::get('types', []));
        }elseif($type == 3){
            $stage = InjuryStepTheft::find($stage_id);
            $stage->uploadedDocumentTypes()->sync(Input::get('types', []));
        }
        Flash::success('Zaktualizowano');

        $result['code'] = 0;
        return json_encode($result);
    }

    public function documentTypes($stage_id, $type)
    {
        if($type == 1) {
            $stage = InjuryStepStage::with('documentTypes')->find($stage_id);
        }elseif($type == 2){
            $stage = InjuryStepTotal::with('documentTypes')->find($stage_id);
        }elseif($type == 3){
            $stage = InjuryStepTheft::with('documentTypes')->find($stage_id);
        }

        $documentTypes = InjuryDocumentType::where('active', 0)->lists('name', 'id');

        return View::make('settings.stages.document-types', compact('stage', 'documentTypes', 'type'));
    }

    public function updateDocumentTypes($stage_id, $type)
    {
        if($type == 1){
            $stage = InjuryStepStage::find($stage_id);
            $stage->documentTypes()->sync(Input::get('types', []));
        }elseif($type == 2){
            $stage = InjuryStepTotal::find($stage_id);
            $stage->documentTypes()->sync(Input::get('types', []));
        }elseif($type == 3){
            $stage = InjuryStepTheft::find($stage_id);
            $stage->documentTypes()->sync(Input::get('types', []));
        }

        Flash::success('Zaktualizowano');

        $result['code'] = 0;
        return json_encode($result);
    }

    public function statues($stage_id, $type)
    {
        if($type == 2){
            $stage = InjuryStepTotal::find($stage_id);
            $statues = InjurySteps::whereIn('id', [30,31,32,33,34,35,36,37])->lists('name', 'id');
        }elseif($type == 3){
            $stage = InjuryStepTheft::find($stage_id);
            $statues = InjurySteps::whereIn('id', [40,41,42,43,44,45,46])->lists('name', 'id');
        }

        return View::make('settings.stages.statues', compact('stage', 'statues', 'type'));
    }

    public function updateStatues($stage_id, $type)
    {
        if($type == 2){
            $stage = InjuryStepTotal::find($stage_id);
            $stage->injury_steps = implode(',',Input::get('types',[]));
        }elseif($type == 3){
            $stage = InjuryStepTheft::find($stage_id);
            $stage->injury_steps = implode(',',Input::get('types',[]));
        }

        $stage->save();
        Flash::success('Zaktualizowano');

        $result['code'] = 0;
        return json_encode($result);
    }
}
