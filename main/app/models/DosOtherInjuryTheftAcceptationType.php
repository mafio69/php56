<?php

class DosOtherInjuryTheftAcceptationType extends Eloquent
{
    protected $table = 'dos_other_injury_theft_acceptation_type';

    protected $guarded = array();

    public function scopeActive()
    {
        return $this->whereActive(0);
    }

}