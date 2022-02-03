<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class Text_contents extends Eloquent
{
    protected $table = 'text_contents';
    protected $guarded = array();
    public $timestamps = false;
}