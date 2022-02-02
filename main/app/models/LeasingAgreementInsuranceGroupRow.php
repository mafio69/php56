<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LeasingAgreementInsuranceGroupRow extends \Eloquent {
    use SoftDeletingTrait;

	protected $fillable = [
        'user_id',
        'leasing_agreement_insurance_group_id',
        'leasing_agreement_insurance_group_rate_id',
        'symbol_product',
        'symbol_element',
        'general_contract',
        'commission',
        'months_12',
        'months_24',
        'months_36',
        'months_48',
        'months_60',
        'months_72',
        'months_84',
        'months_96',
        'months_108',
        'months_120',
        'minimal_12',
        'minimal_24',
        'minimal_36',
        'minimal_48',
        'minimal_60',
        'minimal_72',
        'minimal_84',
        'minimal_96',
        'minimal_108',
        'minimal_120'
    ];

    protected $dates = ['deleted_at'];

    protected $appends = array('rate_name');

    public function insurance_group()
    {
        return $this->belongsTo('LeasingAgreementInsuranceGroup','leasing_agreement_insurance_group_id');
    }

    public function rate()
    {
        return $this->belongsTo('LeasingAgreementInsuranceGroupRate', 'leasing_agreement_insurance_group_rate_id');
    }

    public function getRateNameAttribute()
    {
        return $this->rate->name;
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function packages()
    {
        return $this->hasMany('LeasingAgreementInsuranceGroupRowPackage');
    }
}