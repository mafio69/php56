
<?php
 
 
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
 
class DokFiles extends Eloquent
{
    protected $table = 'dok_files';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function notification()
    {
        return $this->belongsTo('DokNotifications', 'dok_notification_id');
    }
}