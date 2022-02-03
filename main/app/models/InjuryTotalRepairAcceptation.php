<?php

class InjuryTotalRepairAcceptation extends Eloquent
{
    protected $table = 'injury_total_repair_acceptation';

    protected $guarded = array();

    public $timestamps = false;

    public function totalRepair()
    {
        return $this->belongsTo('InjuryTotalRepair', 'injury_total_repair_id');
    }

    public function acceptation()
    {
        return $this->belongsTo('InjuryTotalRepairAcceptationType', 'injury_total_repair_acceptation_type_id');
    }

    public function scopeHasAcceptation($query,$acceptationType_id)
    {
        return $query->whereInjury_total_repair_acceptation_type_id($acceptationType_id);
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }


}