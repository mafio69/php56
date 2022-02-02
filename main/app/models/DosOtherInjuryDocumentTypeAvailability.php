<?php

class DosOtherInjuryDocumentTypeAvailability extends Eloquent
{
    protected $table = 'dos_other_injury_document_type_availability';

    protected $guarded = array();

    public $timestamps = false;

    public function document_type()
    {
        return $this->belongsTo('DosOtherInjuryDocumentType');
    }

}