<?php

class TInjuryRepairStage extends Eloquent
{
    protected $fillable = ['name', 'if_datepicker', 'unchecked_description', 'checked_description'];
    public $timestamps = false;
}