<?php

class VipClientImport extends Eloquent
{
    protected $table = 'vip_clients_imports';
    protected $guarded = array();


    protected $fillable = ['filename', 'user_id'];

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function vips()
    {
        return $this->hasMany('VipClient', 'vip_clients_import_id');
    }
}
