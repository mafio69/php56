<?php

class InjurySapResponse extends Eloquent
{
    protected $fillable = array(
        'injury_sap_entity_id',
        'szkoda_id',
        'typ',
        'kod',
        'message'
    );

    public function entity()
    {
        return $this->belongsTo('InjurySapEntity', 'injury_sap_entity_id');
    }
}