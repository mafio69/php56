<?php


class InjuryStatusNoteAvailability extends Eloquent
{
    protected $fillable = [
        'status_id',
        'status_type',
        'note',
    ];

    public function status()
    {
        return $this->morphTo();
    }


}