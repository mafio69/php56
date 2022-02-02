<?php

class LeasingAgreementHistoryLog extends \Eloquent {
	protected $fillable = [
		'leasing_agreement_history_id',
		'object_name',
		'nr_contract',
		'client_name',
		'client_address',
		'client_NIP',
		'client_REGON',
		'loan_value',
		'net_gross',
		'rate',
		'contribution',
		'insurance_from',
		'insurance_to',
		'months',
		'log_new',
		'log_previous'
	];

	public function history()
	{
		return $this->belongsTo('LeasingAgreementHistory', 'leasing_agreement_history_id');
	}
}