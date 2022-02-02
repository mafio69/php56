<?php

class InjuryStepStageHistory extends Eloquent
{
    protected $fillable = ['injury_id', 'injury_step_stage_id'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function stage()
    {
        return $this->belongsTo('InjuryStepStage', 'injury_step_stage_id');
    }
}