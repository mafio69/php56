<?php

class TaskReply extends \Eloquent {
	protected $fillable = ['user_id', 'task_id', 'task_file_id', 'sendable', 'receivers'];

	public function user()
    {
        return $this->belongsTo('User');
    }

    public function task()
    {
        return $this->belongsTo('Task');
    }

    public function taskFile()
    {
        return $this->belongsTo('TaskFile');
    }
}