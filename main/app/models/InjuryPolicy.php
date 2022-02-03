<?php

class InjuryPolicy extends \Eloquent {
	protected $fillable = [
	    'insurance_company_id',
        'expire',
        'nr_policy',
        'insurance',
        'contribution',
        'netto_brutto',
        'assistance',
        'assistance_name',
        'risks',
        'gap',
        'legal_protection'
    ];

	public function injury()
    {
        return $this->hasOne('Injury');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo('Insurance_companies', 'insurance_company_id');
    }
}