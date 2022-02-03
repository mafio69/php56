<?php

class VmanageCsmType extends \Eloquent {
	use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = [
		'name',
		'default',
        'vmanage_company_id'
	];
    protected $dates = ['deleted_at'];

    public function company()
    {
        return $this->belongsTo('VmanageCompany');
    }
}