<?php

class BillingCycle extends Eloquent
{
    protected $table = 'billing_cycles';
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;
}
