<?php

class LeasingAgreementChat extends Eloquent
{
    protected $fillable = ['leasing_agreement_id', 'user_id', 'topic', 'deadline', 'deadline_user_id', 'active'];

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function messages()
    {
        return $this->hasMany('LeasingAgreementChatMessage');
    }

    public function agreement()
    {
        return $this->belongsTo('LeasingAgreement', 'leasing_agreement_id');
    }


}