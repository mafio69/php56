<?php

class CommissionStep extends Eloquent
{
    protected $table = 'commission_steps';
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;
}
