<?php

class MobileInjuryDamage extends Eloquent
{
    protected $table = 'mobile_injury_damage';

    protected $fillable = array('mobile_injury_id', 'mobile_damage_type_id');

    protected $guarded = array();

    public $timestamps = false;

    public function injury()
    {
        return $this->belongsTo('MobileInjury', 'mobile_injury_id');
    }

}