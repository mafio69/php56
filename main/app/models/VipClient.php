<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class VipClient extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'vip_clients';
    protected $guarded = array();

    protected $fillable = ['vip_clients_import_id', 'registration'];
    protected $dates = ['deleted_at'];

    public function import()
    {
        return $this->belongsTo('VipClientImport', 'vip_clients_import_id');
    }
}
