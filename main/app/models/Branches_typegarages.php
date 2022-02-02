<?php

class Branches_typegarages extends Eloquent
{
    protected $table = 'branches_typegarages';
    protected $guarded = array();
    public $timestamps = false;

    public function branch()
    {
        return $this->belongsTo('Branch');
    }

    public function typegarages()
    {
    	return $this->belongsTo('Typegarage');
    }

}
