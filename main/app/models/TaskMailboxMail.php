<?php

class TaskMailboxMail extends \Eloquent {
	protected $fillable = ['task_mailbox_id', 'mail', 'task_group_id'];

	public function mailbox(){
	    return $this->belongsTo('TaskMailbox', 'task_mailbox_id');
    }

    public function taskGroup()
    {
        return $this->belongsTo('TaskGroup');
    }
}