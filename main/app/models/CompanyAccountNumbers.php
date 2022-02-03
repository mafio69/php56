<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CompanyAccountNumbers extends Eloquent
{

    use SoftDeletingTrait;

    protected $fillable = ['company_id', 'account_number'];

    public function company()
    {
        return $this->belongsTo('Company');
    }

    public function injuryInvoices()
    {
        return $this->belongsToMany('InjuryInvoices', 'injury_invoices_account_numbers');
    }
}