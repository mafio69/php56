<?php

class DokNotification_history extends Eloquent
{
    protected $table = 'dok_history';

    protected $guarded = array();

    public $timestamps = false;

    public function dok_history_content()
    {
        return $this->hasOne('DokHistory_content', 'dok_history_id');
    }

    public function history_type()
    {
        return $this->belongsTo('DokHistory_type', 'dok_history_type_id');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }
}