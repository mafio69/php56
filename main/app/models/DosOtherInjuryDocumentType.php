<?php

class DosOtherInjuryDocumentType extends Eloquent
{
    protected $table = 'dos_other_injury_document_type';

    protected $guarded = array();

    public function injury()
    {
        return $this->belongsTo('DosOtherInjury');
    }

    public function availabilities()
    {
        return $this->hasMany('DosOtherInjuryDocumentTypeAvailability', 'injury_document_type_id');
    }

    public function getAvailabilities()
    {
        $avaibleA = $this->availabilities;
        $result = array();
        foreach ($avaibleA as $avaible)
        {
            $result[] = $avaible->injury_steps_id;
        }
        return $result;
    }
}