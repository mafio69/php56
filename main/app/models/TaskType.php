<?php

class TaskType extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['task_group_id', 'task_subgroup_id', 'name'];
	protected $dates = ['deleted_at'];

	public function group()
    {
        return $this->belongsTo('TaskGroup', 'task_group_id');
    }

    public function subgroup()
    {
        return $this->belongsTo('TaskSubgroup', 'task_subgroup_id');
    }
}