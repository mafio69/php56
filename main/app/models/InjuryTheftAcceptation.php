<?php

class InjuryTheftAcceptation extends Eloquent
{
    protected $table = 'injury_theft_acceptation';

    protected $guarded = array();

    public $timestamps = false;

    public function theft()
    {
        return $this->belongsTo('InjuryTheft', 'injury_theft_id');
    }

    public function acceptation()
    {
        return $this->belongsTo('InjuryTheftAcceptationType', 'injury_theft_acceptation_type_id');
    }

    public function scopeHasAcceptation($query,$acceptationType_id)
    {
        return $query->whereInjury_theft_acceptation_type_id($acceptationType_id);
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }


}