<?php

class TaskComment extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['task_id', 'user_id','content'];
    protected $dates = ['deleted_at'];

    public function task()
    {
        return $this->belongsTo('Task');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }
}