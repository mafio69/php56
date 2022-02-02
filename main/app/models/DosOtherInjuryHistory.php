<?php

class DosOtherInjuryHistory extends Eloquent
{
    protected $table = 'dos_other_injury_history';

    protected $guarded = array();

    public $timestamps = false;

    public function injury_history_content()
    {
        return $this->hasOne('DosOtherInjuryHistoryContent');
    }

    public function history_type()
    {
        return $this->belongsTo('History_type');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }
}