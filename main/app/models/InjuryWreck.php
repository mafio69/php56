<?php

class InjuryWreck extends Eloquent
{
    protected $table = 'injury_wreck';

    protected $fillable = [
        'injury_id',
        'not_applicable',
        'scrapped',
        'alert_repurchase',
        'alert_repurchase_user_id',
        'alert_repurchase_confirm',
        'value_repurchase',
        'value_repurchase_net_gross',
        'value_repurchase_currency',
        'value_tenderer',
        'value_tenderer_net_gross',
        'value_tenderer_currency',
        'expire_tenderer',
        'expire_tenderer_confirm',
        'if_tenderer',
        'value_undamaged',
        'value_undamaged_net_gross',
        'value_undamaged_currency',
        'nr_auction',
        'repurchase_price',
        'buyer',
        'buyer_id',
        'alert_buyer_confirm',
        'alert_buyer',
        'alert_buyer_user_id',
        'pro_forma_request',
        'pro_forma_request_user_id',
        'pro_forma_request_confirm',
        'pro_forma_number',
        'contractor_code',
        'pro_forma_value',
        'payment',
        'payment_user_id',
        'payment_confirm',
        'invoice_request',
        'invoice_request_user_id',
        'invoice_request_confirm',
        'value_invoice',
        'value_compensation',
        'value_compensation_net_gross',
        'value_compensation_currency',
        'compensation_description',
        'compensation_description_date',
        'extra_charge_ic',
        'extra_charge_ic_description',
        'extra_charge_ic_description_date',
        'value_gap',
        'gap_description',
        'gap_description_date',
        'dok_transfer',
        'cassation_receipt',
        'cassation_receipt_user_id',
        'cassation_receipt_confirm',
        'off_register_vehicle',
        'off_register_vehicle_user_id',
        'off_register_vehicle_confirm',
        'active'
    ];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function buyerInfo()
    {
        return $this->belongsTo('Buyer', 'buyer_id');
    }
}