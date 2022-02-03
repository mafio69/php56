<?php

class LeasingAgreementType extends \Eloquent {
	protected $fillable = ['name'];

    public $timestamps = false;

    public function leasingAgreements()
    {
        return $this->hasMany('LeasingAgreement');
    }
}