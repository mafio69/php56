<?php

class TaskAssignment extends \Eloquent {
	protected $fillable = ['email_from', 'user_id'];

	public function user()
    {
        return $this->belongsTo('User');
    }
}