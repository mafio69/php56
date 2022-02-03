<?php

class InjuryStepTotalHistory extends Eloquent
{
    protected $fillable = ['injury_id', 'injury_step_total_id'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function stage()
    {
        return $this->belongsTo('InjuryStepTotal', 'injury_step_total_id');
    }
}