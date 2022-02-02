<?php

class InjurySteps extends Eloquent
{
    protected $table = 'injury_steps';

    protected $fillable = array('name', 'edb', 'injury_group_id');

    public $timestamps = false;

    public function uploadedDocumentTypes()
    {
        return $this->belongsToMany('InjuryUploadedDocumentType', 'injury_step_uploaded_document_type', 'injury_step_id', 'injury_uploaded_document_type_id')->withPivot('edb');
    }

    public function injuryGroup()
    {
        return $this->belongsTo('InjuryGroup', 'injury_group_id');
    }
}