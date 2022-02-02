<?php

class LeasingAgreementInsurancePayment extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $fillable = ['leasing_agreement_insurance_id', 'deadline', 'amount', 'date_of_payment'];

    protected $dates = ['deleted_at'];
}