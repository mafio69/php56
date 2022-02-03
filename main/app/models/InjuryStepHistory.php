<?php

class InjuryStepHistory extends \Eloquent {
	protected $table = 'injury_step_history';

	protected $fillable = ['user_id', 'injury_id', 'prev_step_id','next_step_id','injury_step_stage_id','created_at'];

	public function stepStage(){
        return $this->belongsTo('InjuryStepStage', 'injury_step_stage_id');
	}
	
	public function user()
    {
        return $this->belongsTo('User')->withTrashed();
	}
	
	public function prevStep(){
        return $this->belongsTo('InjurySteps', 'prev_step_id');
	}

	public function nextStep(){
        return $this->belongsTo('InjurySteps', 'next_step_id');
	}
}