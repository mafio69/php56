<?php

class UserTaskGroupHistory extends \Eloquent {
    protected $fillable = array('triggerer_user_id', 'user_id', 'task_group_id', 'mode');


    public function triggererUser()
    {
        return $this->belongsTo('User', 'triggerer_user_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id')->withTrashed();
    }

    public function taskGroup()
    {
        return $this->belongsTo('TaskGroup', 'task_group_id');
    }
}
