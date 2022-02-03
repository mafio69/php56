
<?php
 

 
class DokChatMessages extends Eloquent
{
    protected $table = 'dok_chat_messages';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function chat()
    {
        return $this->belongsTo('DokChat', 'chat_id');
    }

    
}