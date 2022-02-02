<?php

class InjuryTotalRepair extends Eloquent
{
    protected $table = 'injury_total_repair';

    protected $guarded = array();

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function acceptations($withType = 0)
    {
        if($withType != 0)
            return $this->hasMany('InjuryTotalRepairAcceptation')->hasAcceptation($withType);
        else
            return $this->hasMany('InjuryTotalRepairAcceptation');
    }

    public function hasAllAcceptations()
    {
        $countAcceptations = $this->acceptations()->get()->count();
        $numberOfAcceptationsType = InjuryTotalRepairAcceptationType::active()->get()->count();
        if($countAcceptations >= $numberOfAcceptationsType)
            return true;
        else
            return false;

    }


}