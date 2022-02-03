<?php

class InjuryTotalStatuses extends Eloquent
{
    protected $table = 'injury_total_statuses';

    protected $guarded = array();

    public $timestamps = false;

    public function notes()
    {
        return $this->morphMany('InjuryStatusNoteAvailability', 'status');
    }
}