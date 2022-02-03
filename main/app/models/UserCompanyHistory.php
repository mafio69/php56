<?php

class UserCompanyHistory extends \Eloquent {
    protected $fillable = array('triggerer_user_id', 'user_id', 'vmanage_company_id', 'mode');


    public function triggererUser()
    {
        return $this->belongsTo('User', 'triggerer_user_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    public function vmanage_company()
    {
        return $this->belongsTo('VmanageCompany', 'vmanage_company_id');
    }
}
