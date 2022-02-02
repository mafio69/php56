<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 30.01.15
 * Time: 15:29
 */

class Custom_report_type extends Eloquent{

    protected $fillable = array('name', 'class_name', 'desc', 'default_term', 'datepicker');


    public function users()
    {
        return $this->belongsToMany('User', 'user_custom_report_type',  'custom_report_type_id', 'user_id');
    }
}