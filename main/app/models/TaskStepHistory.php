<?php


class TaskStepHistory extends \Eloquent {
    protected $fillable = ['task_instance_id', 'task_step_id', 'description'];

    public function taskInstance()
    {
        return $this->belongsTo('TaskInstance');
    }

    public function step()
    {
        return $this->belongsTo('TaskStep', 'task_step_id');
    }
}