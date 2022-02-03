<?php

class VmanageContractStatus extends Eloquent
{

    protected $fillable = [
        'code',
        'status'
    ];

    public $timestamps = false;
}
