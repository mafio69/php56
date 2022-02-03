<?php
class InjuryDamage extends Eloquent
{
    protected $table = 'injury_damage';

    protected $guarded = array();

    public $timestamps = false;

    public function damage()
    {
        return $this->belongsTo('Damage_type', 'damage_id');
    }
}