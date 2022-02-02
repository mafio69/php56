<?php

class UserLogin extends \Eloquent {
    protected $fillable = ['user_id', 'ip'];

    public function user()
    {
        return $this->belongsTo('User');
    }
}
