
<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class DokChat extends Eloquent
{
    protected $table = 'dok_chat';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function messages()
    {
        return $this->hasMany('DokChatMessages', 'chat_id');
    }

    public function notification()
    {
        return $this->belongsTo('DokNotifications', 'dok_notification_id');
    }


}