<?php

class InjuryChange extends Eloquent
{
    protected $table = 'injury_changes';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function injury()
    {
        return $this->belongsTo('Injury');
    }


}
