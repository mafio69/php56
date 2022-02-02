<?php

class DokProcesses extends Eloquent
{
    protected $table = 'dok_processes';
    protected $guarded = array();
    protected $fillable = array('parent_id', 'name', 'description', 'weight', 'time_limit', 'priority', 'active');

    public function process()
    {
        return $this->belongsTo('DokProcesses', 'parent_id');
    }


    public function processes()
    {
        return $this->hasMany('DokProcesses', 'parent_id');
    }

    public function users()
    {
        return $this->hasMany('DokProcessesUsers', 'dok_processes_id');
    }

    public function setProcesses($dataInput)
    {

        $processes = $this->processes()->get();
        foreach($processes as $process)
        {
            foreach( $dataInput as $k => $v){
                $process->$k = $v;
            }
            $process->save();
            $process->setProcesses($dataInput);
        }

    }
    

   

}