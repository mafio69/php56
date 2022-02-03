<?php


class DosOtherInjuryChatMessages extends Eloquent
{
    protected $table = 'dos_other_injury_chat_messages';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function chat()
    {
        return $this->belongsTo('DosOtherInjuryChat', 'chat_id');
    }


}