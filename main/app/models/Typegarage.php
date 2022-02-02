<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class Typegarage extends Eloquent
{
    protected $table = 'typegarages';
    protected $guarded = array();
}