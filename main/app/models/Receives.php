<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class Receives extends Eloquent
{
    protected $table = 'receives';

    protected $guarded = array();
}