<?php

class InjuryStepStage extends Eloquent
{
    protected $fillable = ['injury_step_id', 'name', 'condition', 'next_injury_step_id', 'next_step_condition'];

    public function step()
    {
        return $this->belongsTo('InjurySteps', 'injury_step_id');
    }

    public function uploadedDocumentTypes()
    {
        return $this->belongsToMany('InjuryUploadedDocumentType', 'injury_step_stage_uploaded_document_type', 'injury_step_stage_id', 'injury_uploaded_document_type_id');
    }

    public function documentTypes()
    {
        return $this->belongsToMany('InjuryDocumentType', 'injury_step_stage_document_type', 'injury_step_stage_id', 'injury_document_type_id');
    }

    public function nextInjuryStep()
    {
        return $this->belongsTo('InjurySteps', 'next_injury_step_id');
    }
}