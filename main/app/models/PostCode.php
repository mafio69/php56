<?php

class PostCode extends \Eloquent {
    protected $fillable = ['voivodeship_id', 'name'];

    public $timestamps = false;

    public function voivodeship()
    {
        return $this->belongsTo('Voivodeship');
    }
}