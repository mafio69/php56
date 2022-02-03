<?php

class TaskStep extends \Eloquent {
	protected $fillable = ['name'];

	public function getSectionAttribute()
    {
        if($this->id == 1 ){
            return 'new';
        }elseif($this->id == 2){
            return 'inprogress';
        }

        return 'complete';
    }
}