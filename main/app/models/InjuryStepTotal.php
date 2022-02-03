<?php

class InjuryStepTotal extends Eloquent
{
    protected $fillable = ['type','injury_steps', 'injury_total_statuse_id', 'injury_step_id', 'condition', 'next_injury_step_id'];

    public function steps()
    {
        return InjurySteps::where('id', '!=', 0)->whereIn('id',explode(',',$this->injury_steps))->get();
    }
    public function step(){
        return $this->hasOne('InjurySteps','id', 'injury_step_id');
    }

    public function stage()
    {
        return $this->hasOne('InjuryTotalStatuses','id', 'injury_total_statuse_id');
    }

    public function uploadedDocumentTypes()
    {
        return $this->belongsToMany('InjuryUploadedDocumentType', 'injury_step_total_uploaded_document_type', 'injury_step_total_id', 'injury_uploaded_document_type_id');
    }

    public function documentTypes()
    {
        return $this->belongsToMany('InjuryDocumentType', 'injury_step_total_document_type', 'injury_step_total_id', 'injury_document_type_id');
    }
}