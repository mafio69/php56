<?php

class ApiHistory extends \Eloquent {
	protected $fillable = ['api_module_id', 'api_user_id', 'request', 'response', 'ip'];

	public function apiModule()
    {
        return $this->belongsTo('ApiModule');
    }

    public function apiUser()
    {
        return $this->belongsTo('ApiUser');
    }
}