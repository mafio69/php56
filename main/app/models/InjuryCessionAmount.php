<?php

class InjuryCessionAmount extends Eloquent
{
    protected $table = 'injury_cession_amounts';

    protected $fillable = array(
        'injury_id',
        'paid_amount',
        'net_gross',
        'fv_amount',
    );

    public function injury()
    {
        return $this->belongsTo('Injury');
    }
}