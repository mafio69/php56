<?php

class LeasingAgreementChatMessage extends Eloquent
{
    protected $fillable = ['leasing_agreement_chat_id', 'user_id', 'content', 'active', 'delete_user_id'];


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function chat()
    {
        return $this->belongsTo('LeasingAgreementChat', 'leasing_agreement_chat_id');
    }

    
}