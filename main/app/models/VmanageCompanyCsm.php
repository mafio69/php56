<?php

class VmanageCompanyCsm extends \Eloquent {
	use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $table = 'vmanage_company_csm';

	protected $fillable = [
		'vmanage_company_id',
		'vmanage_csm_type_id',
		'content'
	];

	protected $dates = ['deleted_at'];

	public function company()
	{
		return $this->belongsTo('VmanageCompany', 'vmanage_company_id');
	}

    public function csmType()
    {
        return $this->belongsTo('VmanageCsmType', 'vmanage_csm_type_id');
    }
}