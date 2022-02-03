<?php

class TaskFile extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['task_id', 'filename', 'original_filename', 'mime'];
    protected $dates = ['deleted_at'];

	public function task()
    {
        return $this->belongsTo('Task', 'task_id');
    }
}