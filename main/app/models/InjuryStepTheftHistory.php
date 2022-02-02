<?php

class InjuryStepTheftHistory extends Eloquent
{
    protected $fillable = ['injury_id', 'injury_step_theft_id'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function stage()
    {
        return $this->belongsTo('InjuryStepTheft', 'injury_step_theft_id');
    }
}