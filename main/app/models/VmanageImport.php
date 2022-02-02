<?php

class VmanageImport extends \Eloquent {

    protected $table = "vmanage_imports";
    protected $fillable = [
        'if_truck',
        'user_id',
        'filename',
        'original_filename',
        'file_type',
        'parsed'
    ];

    public function user()
    {
        return $this->belongsTo('User');
    }

}
