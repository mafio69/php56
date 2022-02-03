<?php

class InjuryTheftStatuses extends Eloquent
{
    protected $table = 'injury_theft_statuses';

    protected $guarded = array();

    public $timestamps = false;

    public function notes()
    {
        return $this->morphMany('InjuryStatusNoteAvailability', 'status');
    }
}