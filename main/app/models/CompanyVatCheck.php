<?php

class CompanyVatCheck extends Eloquent
{
    protected $fillable = ['company_id', 'status_code', 'status'];

    public function company()
    {
        return $this->belongsTo('Company');
    }
}


