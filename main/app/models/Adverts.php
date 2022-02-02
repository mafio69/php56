<?php

class Adverts extends Eloquent
{
    protected $table = 'adverts';
    protected $fillable = array('resolution_type_id', 'file', 'url', 'active', 'dirty', 'new');

    public $timestamps = false;

    public function resolution(){
        return $this->belongsTo('resolution_types', 'resolution_type_id');
    }

}