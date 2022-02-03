<?php

class InjuryDocumentTypeAvailability extends Eloquent
{
    protected $table = 'injury_document_type_availability';

    protected $guarded = array();

    public $timestamps = false;

    public function document_type()
    {
        return $this->belongsTo('InjuryDocumentType', 'injury_document_type_id');
    }

}