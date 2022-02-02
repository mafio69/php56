<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class Branches_typevehicles extends Eloquent
{
    protected $table = 'branches_typevehicles';
    protected $guarded = array();

    public function branch()
    {
        return $this->belongsTo('Branch');
    }

    public function typevehicles()
    {
    	return $this->belongsTo('Typevehicles');
    }

}