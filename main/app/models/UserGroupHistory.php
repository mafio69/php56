<?php

class UserGroupHistory extends \Eloquent {
    protected $table = 'user_group_histories';
    protected $fillable = array('triggerer_user_id', 'user_id', 'user_group_id', 'mode');


    public function triggererUser()
    {
        return $this->belongsTo('User', 'triggerer_user_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id')->withTrashed();
    }

    public function userGroup()
    {
        return $this->belongsTo('UserGroup', 'user_group_id')->withTrashed();
    }
}
