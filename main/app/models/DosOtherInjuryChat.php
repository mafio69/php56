
<?php

class DosOtherInjuryChat extends Eloquent
{
    protected $table = 'dos_other_injury_chat';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function messages()
    {
        return $this->hasMany('DosOtherInjuryChatMessages', 'chat_id');
    }

    public function injury()
    {
        return $this->belongsTo('DosOtherInjury');
    }


}