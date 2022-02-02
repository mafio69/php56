<?php

class Commission extends Eloquent
{
    protected $table = 'commissions';
    protected $fillable = [
        'injury_invoice_id',
	    'company_id',
	    'commission_individual_report_id',
	    'commission_group_report_id',
	    'commission_step_id',
	    'invoice_date',
	    'commission',
        'commission_percentage',
        'acceptation_date',
	    'omission_reason',
	    'omission_attachment',
    ];
    protected $dates = ['invoice_date', 'acceptation_date'];

    public function invoice()
    {
    	return $this->belongsTo('InjuryInvoices', 'injury_invoice_id');
    }

    public function company()
    {
    	return $this->belongsTo('Company');
    }

    public function individualReport()
    {
    	return $this->belongsTo('CommissionReport', 'commission_individual_report_id');
    }

	public function groupReport()
	{
		return $this->belongsTo('CommissionReport', 'commission_group_report_id');
	}

	public function step()
	{
		return $this->belongsTo('CommissionStep', 'commission_step_id');
	}
}
