<?php

class InjuryHistory extends Eloquent
{
    protected $table = 'injury_history';

    protected $guarded = array();

    public $timestamps = false;

    public function injury_history_content()
    {
        return $this->hasOne('InjuryHistoryContent');
    }

    public function history_type()
    {
        return $this->belongsTo('History_type');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function injury()
    {
        return $this->belongsTo('Injury', 'injury_id');
    }
}