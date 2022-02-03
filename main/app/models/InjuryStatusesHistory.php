<?php

class InjuryStatusesHistory extends Eloquent
{
    protected $table = 'injury_statuses_history';

    protected $fillable = ['injury_id', 'user_id', 'status_id', 'status_type'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }
}