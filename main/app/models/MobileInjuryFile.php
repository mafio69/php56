<?php

class MobileInjuryFile extends Eloquent
{
    protected $table = 'mobile_injury_files';

    protected $fillable = array('mobile_injury_id', 'file');

    protected $guarded = array();

    public function injury()
    {
        return $this->belongsTo('MobileInjury', 'mobile_injury_id');
    }

}