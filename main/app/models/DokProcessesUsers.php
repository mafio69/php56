<?php
 

class DokProcessesUsers extends Eloquent
{
    protected $table = 'dok_processes_users';
    protected $guarded = array();

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function process()
    {
        return $this->belongsTo('DokProcesses');
    }

    

}