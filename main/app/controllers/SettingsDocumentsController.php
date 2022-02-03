<?php

class SettingsDocumentsController extends \BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:dostepnosc_dokumentow#wejscie');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function injuries()
    {
        $documentsTypes = InjuryDocumentType::whereActive(0)->with('availabilities', 'ownersGroups', 'steps')->get();
        $steps = InjurySteps::lists('name', 'id');

        return View::make('settings.documents.index', compact('documentsTypes', 'steps'));
    }

    public function editInjuries($document_id)
    {
        $documentType = InjuryDocumentType::find($document_id);
        $ownersGroups = OwnersGroup::get();
        $steps = InjurySteps::lists('name', 'id');

        return View::make('settings.documents.edit-injuries', compact('documentType', 'steps', 'ownersGroups'));

    }

    public function editInjuriesDocumentName($document_id)
    {
        $documentType = InjuryDocumentType::find($document_id);
        return View::make('settings.documents.edit-injuries-document-name', compact('documentType'));
    }

    public function updateInjuries($document_id)
    {
        $documentType = InjuryDocumentType::find($document_id);
        $documentType->cfm = Input::get('cfm');
        $documentType->save();

        if(Input::has('ownersGroups'))
        {
            $documentType->ownersGroups()->sync(Input::get('ownersGroups'));
        }else{
            $documentType->ownersGroups()->detach();
        }

        if(Input::has('steps'))
        {
            $documentType->steps()->sync(Input::get('steps'));
        }else{
            $documentType->steps()->detach();
        }

        Flash::success('Zaktualizowano '.$documentType->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function updateInjuriesDocumentName($document_id)
    {
        $documentType = InjuryDocumentType::find($document_id);
        $documentType->name = Input::get('name');
        $documentType->save();

        Flash::success('Zaktualizowano '.$documentType->name);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function groupsInfo()
    {
        $groups = OwnersGroup::with('owners')->get();

        return View::make('settings.documents.groups-info', compact('groups'));
    }

    public function templateInjuriesDoc($document_type_id)
    {
        $document = InjuryDocumentType::find($document_type_id);
        $branch_id = Branch::latest()->first()->id;
        $doc = new Idea\DocGenerator\DocGenerator(1,'Injury', $document_type_id, [],$branch_id);

        return $doc->generatePreview($document->name);
    }
}
