<?php

class CompanyCommission extends Eloquent
{
	use \Illuminate\Database\Eloquent\SoftDeletingTrait;
    protected $table = 'company_commissions';
    protected $fillable = [
        'company_id',
	    'brand_id',
	    'min_value',
	    'min_amount',
	    'commission'
    ];
    protected $dates = ['deleted_at'];

    public function company()
    {
    	return $this->belongsTo('Company');
    }

    public function brand()
    {
    	return $this->belongsTo('Brands', 'brand_id');
    }
}
