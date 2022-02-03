<?php

class InjuryGroup extends Eloquent
{
    protected $table = 'injury_groups';

    protected $fillable = array('name');

    public $timestamps = false;

}