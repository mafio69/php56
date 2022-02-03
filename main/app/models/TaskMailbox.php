<?php

class TaskMailbox extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = [
	    'is_valid',
	    'task_group_id',
	    'name',
        'server',
        'login',
        'password',
        'task_source_id'
    ];

	protected $dates = [
	    'deleted_at'
    ];

    public function taskSource()
    {
        return $this->belongsTo('TaskSource');
    }

    public function taskGroup()
    {
        return $this->belongsTo('TaskGroup');
    }

    public function tasks()
    {
        return $this->morphMany('Task', 'source');
    }

    public function mails()
    {
        return $this->hasMany('TaskMailboxMail');
    }
}