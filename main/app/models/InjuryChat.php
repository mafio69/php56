<?php

class InjuryChat extends Eloquent
{
    protected $table = 'injury_chat';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function messages()
    {
        return $this->hasMany('InjuryChatMessages', 'chat_id');
    }

    public function injury()
    {
        return $this->belongsTo('Injury');
    }


}
