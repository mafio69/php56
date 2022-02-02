<?php

class LeasingAgreementImport extends \Eloquent {
	protected $fillable = [
        'no',
        'nr_contract',
        'nr_contract_pure',
        'if_exist_cession',
        'if_cession',
        'correction',
        'if_data_change',
        'if_refund_contribution',
        'insurance_number',
        'if_continuation',
        'insurance_type',
        'months',
        'agreement_type',
        'notification_number',
        'insurance_date',
        'date_from',
        'date_to',
        'insurance_company',
        'client_name',
        'client_address',
        'client_REGON',
        'client_NIP',
        'owner',
        'owner_address',
        'owner_post',
        'owner_city',
        'owner_REGON',
        'owner_NIP',
        'agreement_payment_way',
        'loan_net_value',
        'net_gross',
        'contribution',
        'rate',
        'rate_vbl',
        'refund',
        'agreement_insurance_group',
        'if_load_decision',
        'parsed'
    ];

    public $timestamps = false;
}