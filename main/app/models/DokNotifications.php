<?php
 
class DokNotifications extends Eloquent
{
    protected $table = 'dok_notifications';

    protected $guarded = array();

    public function process()
    {
        return $this->belongsTo('DokProcesses', 'process_id');
    }

    public function vehicle()
    {
        return $this->belongsTo('Vehicles');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function getInfo()
    {
        return $this->belongsTo('Text_contents', 'info');
    }

    public function wayof()
    {
        return $this->belongsTo('DokWayof', 'wayof_id');
    }

}