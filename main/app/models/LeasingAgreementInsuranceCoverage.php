<?php

class LeasingAgreementInsuranceCoverage extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;
    protected $fillable = ['leasing_agreement_insurance_id', 'leasing_agreement_insurance_coverage_type_id', 'amount', 'currency_id', 'net_gross'];
    protected $dates = ['deleted_at'];

    public function type()
    {
        return $this->belongsTo('LeasingAgreementInsuranceCoverageType', 'leasing_agreement_insurance_coverage_type_id', 'id');
    }
}