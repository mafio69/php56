<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class Offenders extends Eloquent
{
    protected $table = 'offenders';
    protected $fillable = array('name', 'surname', 'post', 'city', 'street', 'registration', 'email', 'car', 'oc_nr', 'zu', 'expire', 'owner', 'remarks');
    protected $guarded = array();
}