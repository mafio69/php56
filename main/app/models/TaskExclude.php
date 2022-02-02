<?php

class TaskExclude extends \Eloquent {
	protected $fillable = ['technical_user_id', 'user_id', 'absence'];

	protected $dates = ['absence'];

	public function user()
    {
        return $this->belongsTo('User');
    }

    public function getAbsenceFormattedAttribute()
    {
        return $this->absence->format('Y-m-d');
    }
}