<?php

class InjuryInvoices extends Eloquent
{
    protected $table = 'injury_invoices';

    protected $guarded = array();

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function injury_files()
    {
        return $this->belongsTo('InjuryFiles', 'injury_files_id');
    }

    public function invoicereceive()
    {
        return $this->belongsTo('Invoicereceives', 'invoicereceives_id');
    }

    public function parent()
    {
        return $this->belongsTo('InjuryInvoices', 'parent_id');
    }

    public function child()
    {
        return $this->hasOne('InjuryInvoices', 'parent_id');
    }

    public function base_invoice()
    {
        return $this->belongsTo('InjuryInvoices', 'base_invoice_id');
    }

    public function children()
    {
        return $this->hasMany('InjuryInvoices', 'base_invoice_id');
    }

    public function serviceType()
    {
        return $this->belongsTo('InjuryInvoiceServiceType', 'injury_invoice_service_type_id');
    }

    public function status()
    {
        return $this->belongsTo('InjuryInvoiceStatus', 'injury_invoice_status_id');
    }

    public function assignedBankAccountNumbers()
    {
        return $this->belongsToMany('CompanyAccountNumbers', 'injury_invoices_account_numbers');
    }

    public function assignedBankAccountNumbersWithTrashed()
    {
        return $this->belongsToMany('CompanyAccountNumbers', 'injury_invoices_account_numbers')->withTrashed();
    }

    public function valueOfCommission()
    {
        if ($this->commission == 1) {
            if ($this->injury->branch_id != 0 && $this->injury->branch_id != '-1') {
                if ($this->injury->branch->company->commission != NULL && $this->injury->branch->company->commission != '') {
                    if ($this->injury_files->category == 3) {
                        return $this->injury->branch->company->commission * $this->base_netto * 0.01;
                    } elseif ($this->parent_id != 0 && $this->parent) {
                        $korekta = $this->injury->branch->company->commission * $this->base_netto * 0.01;
  											$parent = $this->injury->branch->company->commission * $this->parent->base_netto * 0.01;
  											$diff = $korekta - $parent;
  											return $diff;
  									}else{
  											return $this->injury->branch->company->commission * $this->base_netto * 0.01;
  									}
  							}else{
  									if($this->injury_files->category == 4)
  											return 0;
  									else
  											return 0;
  							}
  					}else{
  							return 0;
  					}
  			//}elseif($this->injury_files->category == 4 && $this->parent_id != 0 && $this->parent->commission == 1){
  			//		return 0;
  			}else{
  					return 0;
  			}
  	}

  	public function relatedCommission()
    {
    	return $this->hasOne('Commission', 'injury_invoice_id');
    }

    public function companyVatCheck()
    {
        return $this->belongsTo('CompanyVatCheck', 'company_vat_check_id');
    }

    public function initialCompanyVatCheck()
    {
        return $this->belongsTo('CompanyVatCheck', 'initial_company_vat_check_id');
    }

    public function injuryInvoiceForwardDocuments(){
        return $this->hasMany('InjuryInvoiceForwardDocument', 'injury_invoice_id');
    }

    public function note()
    {
        return $this->belongsTo('InjuryNote', 'injury_note_id');
    }

    public function branch()
    {
        return $this->belongsTo('Branch', 'branch_id')->withTrashed();
    }

    public function compensations()
    {
        return $this->belongsToMany('InjuryCompensation', 'invoice_compensations', 'invoice_id', 'injury_compensation_id');
    }

}
