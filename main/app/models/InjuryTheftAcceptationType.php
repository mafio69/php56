<?php

class InjuryTheftAcceptationType extends Eloquent
{
    protected $table = 'injury_theft_acceptation_type';

    protected $guarded = array();

    public function scopeActive()
    {
        return $this->whereActive(0);
    }

}