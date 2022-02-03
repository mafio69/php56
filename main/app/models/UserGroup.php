<?php

class UserGroup extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    public function users()
    {
        return $this->belongsToMany('User', 'user_group', 'user_group_id', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('Permission', 'user_group_permission', 'user_group_id', 'permission_id');
    }

    public function permissionHistories()
    {
        return $this->hasMany('PermissionHistory');
    }

    public function userHistories()
    {
        return $this->hasMany('UserGroupHistory');
    }
}
