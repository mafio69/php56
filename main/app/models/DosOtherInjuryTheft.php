<?php

class DosOtherInjuryTheft extends Eloquent
{
    protected $table = 'dos_other_injury_theft';

    protected $guarded = array();

    public function injury()
    {
        return $this->belongsTo('DosOtherInjury');
    }

    public function acceptations($withType = 0)
    {
        if($withType != 0)
            return $this->hasMany('DosOtherInjuryTheftAcceptation', 'injury_theft_id')->hasAcceptation($withType);
        else
            return $this->hasMany('DosOtherInjuryTheftAcceptation', 'injury_theft_id');
    }

    public function hasAllAcceptations()
    {
        $theftAcceptations = $this->acceptations()->get();
        foreach($theftAcceptations as $acceptation)
        {
            $theftAcceptationsA[$acceptation->injury_theft_acceptation_type_id] = 1;
        }

        if( $this->injury()->first()->type_incident_id == 4)
            return true;

        if( in_array( $this->injury()->first()->type_incident_id, array(1, 2, 3) ) && ( !isset($theftAcceptationsA[3]) || !isset($theftAcceptationsA[4]) ) )
            return false;

        if( !isset($theftAcceptationsA[1]) || !isset($theftAcceptationsA[2]) || !isset($theftAcceptationsA[5]) )
            return false;

        return true;

    }


}