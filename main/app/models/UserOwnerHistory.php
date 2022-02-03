<?php

class UserOwnerHistory extends \Eloquent {
    protected $fillable = array('triggerer_user_id', 'user_id', 'owner_id', 'mode');


    public function triggererUser()
    {
        return $this->belongsTo('User', 'triggerer_user_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id')->withTrashed();
    }

    public function owner()
    {
        return $this->belongsTo('Owners', 'owner_id');
    }
}
