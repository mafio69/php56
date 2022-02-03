<?php

class Task extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = [
	    'current_task_instance_id',
        'case_nb',
        'task_source_id',
        'source_id',
        'source_type',
        'task_mailbox_id',
        'task_group_id',
        'task_type_id',
        'to_name',
        'to_email',
        'from_name',
        'from_email',
        'cc_name',
        'cc_email',
        'uid',
        'subject',
        'content',
        'task_date'
    ];
    protected $dates = ['deleted_at', 'task_date'];

	public function sourceType()
    {
        return $this->belongsTo('TaskSource', 'task_source_id');
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->belongsTo('TaskType', 'task_type_id');
    }

    public function group()
    {
        return $this->belongsTo('TaskGroup', 'task_group_id');
    }

    public function files()
    {
        return $this->hasMany('TaskFile', 'task_id');
    }

    public function instances()
    {
        return $this->hasMany('TaskInstance');
    }

    public function currentInstance()
    {
        return $this->belongsTo('TaskInstance', 'current_task_instance_id');
    }

    public function injuries()
    {
        return $this->belongsToMany('Injury', 'task_injury')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany('TaskComment');
    }

    public function mailbox()
    {
        return $this->belongsTo('TaskMailbox', 'task_mailbox_id');
    }

    public function replies()
    {
        return $this->hasMany('TaskReply');
    }

    public function forwards()
    {
        return $this->hasMany('TaskForward');
    }
}