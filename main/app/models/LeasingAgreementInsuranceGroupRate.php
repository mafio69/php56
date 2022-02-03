<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LeasingAgreementInsuranceGroupRate extends \Eloquent {
	use SoftDeletingTrait;

	protected $fillable = ['insurance_company_id', 'name', 'deductible_value', 'deductible_percent'];
	protected $dates = ['deleted_at'];

	public function insuranceCompany()
	{
		return $this->belongsTo('Insurance_companies');
	}
}
