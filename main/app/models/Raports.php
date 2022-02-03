<?php

class Raports extends Eloquent
{
    protected $table = 'raports';
    protected $guarded = array();

    public function user()
    {
        return $this->belongsTo('User');
    }
}