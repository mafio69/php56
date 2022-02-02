<?php

class InjuryGap extends \Eloquent {
	protected $fillable = [
	    'injury_id',
        'insurance_company_id',
        'gap_type_id',
        'insurance_amount',
        'netto_brutto',
        'forecast',
        'injury_number'
    ];

	public function injury(){
	    return $this->belongsTo('Injury');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo('Insurance_companies', 'insurance_company_id');
    }

    public function gapType()
    {
        return $this->belongsTo('GapType');
    }
}