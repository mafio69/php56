<?php

class LeasingAgreementHistory extends \Eloquent {
    protected $fillable = [
        'leasing_agreement_id',
        'user_id',
        'leasing_agreement_history_type_id',
        'value',
        'notification_number'
    ];

    public function content()
    {
        return $this->hasOne('LeasingAgreementHistoryContent');
    }

    public function type()
    {
        return $this->belongsTo('LeasingAgreementHistoryType', 'leasing_agreement_history_type_id');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function log()
    {
        return $this->hasOne('LeasingAgreementHistoryLog');
    }

    public function agreement()
    {
        return  $this->belongsTo('LeasingAgreement', 'leasing_agreement_id');
    }
}