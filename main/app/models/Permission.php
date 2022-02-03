<?php

class Permission extends \Eloquent {
	protected $fillable = ['name', 'short_name', 'path', 'module_id'];

    public $timestamps=false;

    public function module()
    {
        return $this->belongsTo('Module');
    }

    public function groups()
    {
        return $this->belongsToMany('UserGroup', 'user_group_permission',  'permission_id', 'user_group_id');
    }
}
