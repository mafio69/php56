<?php

class PermissionHistory extends \Eloquent {
    protected $table = 'permission_histories';
    protected $fillable = array('triggerer_user_id', 'permission_id', 'user_group_id', 'mode');


    public function triggererUser()
    {
        return $this->belongsTo('User', 'triggerer_user_id');
    }

    public function permission()
    {
        return $this->belongsTo('Permission', 'permission_id');
    }

    public function userGroup()
    {
        return $this->belongsTo('UserGroup', 'user_group_id');
    }
}
