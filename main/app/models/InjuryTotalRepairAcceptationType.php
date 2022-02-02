<?php

class InjuryTotalRepairAcceptationType extends Eloquent
{
    protected $table = 'injury_total_repair_acceptation_type';

    protected $guarded = array();

    public function scopeActive()
    {
        return $this->whereActive(0);
    }

}