<?php


class InjuryChatMessages extends Eloquent
{
    protected $table = 'injury_chat_messages';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function chat()
    {
        return $this->belongsTo('InjuryChat', 'chat_id');
    }

    public function note()
    {
        return $this->belongsTo('InjuryNote', 'injury_note_id');
    }

}
