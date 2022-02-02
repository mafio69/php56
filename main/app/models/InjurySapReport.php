<?php

class InjurySapReport extends Eloquent
{
    protected $fillable = array(
        'filename',
        'report_date',
    );

    protected $dates = ['report_date'];
}