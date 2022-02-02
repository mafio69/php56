<?php


use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LeasingAgreementInsuranceGroupRowPackage extends \Eloquent {
    use SoftDeletingTrait;

    protected $fillable = [
        'leasing_agreement_insurance_group_row_id',
        'name',
        'months_12_percentage',
        'months_24_percentage',
        'months_36_percentage',
        'months_48_percentage',
        'months_60_percentage',
        'months_72_percentage',
        'months_84_percentage',
        'months_96_percentage',
        'months_108_percentage',
        'months_120_percentage',
        'months_12_amount',
        'months_24_amount',
        'months_36_amount',
        'months_48_amount',
        'months_60_amount',
        'months_72_amount',
        'months_84_amount',
        'months_96_amount',
        'months_108_amount',
        'months_120_amount'
    ];

    protected $dates = ['deleted_at'];

    public function groupRow()
    {
        return $this->belongsTo('LeasingAgreementInsuranceGroupRow', 'leasing_agreement_insurance_group_row_id');
    }
}