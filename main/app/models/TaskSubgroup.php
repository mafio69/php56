<?php

class TaskSubgroup extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;
	protected $fillable = ['task_group_id', 'name'];
    protected $dates = ['deleted_at'];
	public function group()
    {
        return $this->belongsTo('TaskGroup', 'task_group_id');
    }

    public function taskTypes()
    {
        return $this->hasMany('TaskType');
    }
}