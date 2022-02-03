<?php

class CommissionType extends Eloquent
{
    protected $table = 'commission_types';
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;
}
