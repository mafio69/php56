<?php

class SettingsDocumentTemplatesController extends BaseController {

	public function getIndex()
	{
		$owners = Owners::with('documentTemplate', 'conditionalDocumentTemplate')->get();

		$templates = DocumentTemplate::where('slug', '!=', 'default')->get();
		return View::make('settings.document-templates.index', compact('owners', 'templates'));
	}

	public function getEdit($owner_id)
    {
        $owner = Owners::with('documentTemplate', 'conditionalDocumentTemplate')->findOrFail($owner_id);

        $documentTemplates = ['' => '--- wybierz ---'] + DocumentTemplate::lists('name', 'id');

        return View::make('settings.document-templates.edit', compact('owner', 'documentTemplates'));
    }

    public function postUpdate($owner_id)
    {
        $owner = Owners::findOrFail($owner_id);

        $owner->update(Input::all());

        return json_encode(['code' => 0]);
    }
}