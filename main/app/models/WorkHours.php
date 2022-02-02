<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 07.07.2014
 * Time: 11:32
 */

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class WorkHours extends Eloquent {

    protected $table = 'work_hours';
    protected $guarded = array();

} 