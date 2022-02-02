<?php

class InjuryTheft extends Eloquent
{
    protected $table = 'injury_theft';

    protected $guarded = array();

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function acceptations($withType = 0)
    {
        if($withType != 0)
            return $this->hasMany('InjuryTheftAcceptation')->hasAcceptation($withType);
        else
            return $this->hasMany('InjuryTheftAcceptation');
    }

    public function hasAllAcceptations()
    {
        $countAcceptations = $this->acceptations()->get()->count();
        $numberOfAcceptationsType = InjuryTheftAcceptationType::active()->get()->count();
        if($countAcceptations >= $numberOfAcceptationsType)
            return true;
        else
            return false;

    }


}