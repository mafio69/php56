<?php

class LeasingAgreementInsuranceGroup extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $fillable = ['create_user_id', 'close_user_id', 'insurance_company_id', 'valid_from', 'valid_to'];

    protected $dates = ['deleted_at'];

    public function leasingAgreements()
    {
        return $this->hasMany('LeasingAgreement');
    }

    public function rows()
    {
        return $this->hasMany('LeasingAgreementInsuranceGroupRow');
    }

    public function create_user()
    {
        return $this->belongsTo('User', 'create_user_id')->withTrashed();
    }

    public function close_user()
    {
        return $this->belongsTo('User', 'close_user_id')->withTrashed();
    }

    public function insuranceCompany()
    {
        return $this->belongsTo('Insurance_companies');
    }

    public function getGroupNameAttribute()
    {
        if(is_null($this->attributes['valid_to']) )
            return 'od '.$this->attributes['valid_from'];

        return 'od '.$this->attributes['valid_from'] .' do '. $this->attributes['valid_to'];
    }
}
