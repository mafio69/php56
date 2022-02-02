<?php
class Injuries_type extends Eloquent
{
    protected $table = 'injuries_type';

    protected $fillable = [
        'name',
        'sap_name',
        'if_injury_vehicle',
        'if_injury_other'
    ];

    public $timestamps = false;
}