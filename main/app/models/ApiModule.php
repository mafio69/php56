<?php

class ApiModule extends \Eloquent {
	protected $fillable = ['name'];
    public $timestamps = false;
    
	public function apiKeys()
    {
        return $this->hasMany('ApiModuleKey');
    }

    public function apiUsers()
    {
        return $this->belongsToMany('ApiUser', 'api_user_api_module');
    }

    public function apiHistories()
    {
        return $this->hasMany('ApiHistories');
    }
}