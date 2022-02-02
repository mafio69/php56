<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class Module extends Eloquent
{
    protected $table = 'modules';

    protected $guarded = array();

    public $timestamps = false;

    public function roles()
    {
    	return $this->hasMany('Role');
    }
}