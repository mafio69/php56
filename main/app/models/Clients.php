<?php
class Clients extends Eloquent
{
    protected $table = 'clients';
    protected $fillable = [
        'syjon_contractor_id',
        'parent_id',
        'firmID',
        'group',
        'saldo',
        'name',
        'NIP',
        'REGON',
        'registry_post',
        'registry_city',
        'registry_street',
        'registry_voivodeship_id',
        'registry_invalid_post',
        'correspond_post',
        'correspond_city',
        'correspond_street',
        'correspond_voivodeship_id',
        'correspond_invalid_post',
        'phone',
        'email',
        'active'
    ];

    public function parent()
    {
        return $this->belongsTo('Clients', 'parent_id', 'id');
    }

    public function child()
    {
        return $this->hasMany('Clients', 'parent_id', 'id');
    }

    public function registryVoivodeship()
    {
        return $this->belongsTo('Voivodeship', 'registry_voivodeship_id');
    }

    public function correspondVoivodeship()
    {
        return $this->belongsTo('Voivodeship', 'correspond_voivodeship_id');
    }

    public function getAddressAttribute()
    {
        $address = $this->registry_post.' '.$this->registry_city.', '.$this->registry_street;
        if(trim($address) != ','){
            return $address;
        }else{
            return null;
        }
    }

    public function getFirmIDAttribute($value)
    {
        return ltrim($value, '0');
    }
}
