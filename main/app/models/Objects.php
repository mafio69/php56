<?php

class Objects extends Eloquent
{
    protected $table = 'objects';

    protected $fillable = [
        'syjon_vehicle_id',
        'syjon_contract_id',
        'syjon_contract_internal_agreement_id',
        'syjon_policy_id',
        'source',
        'owner_id',
        'client_id',
        'parent_id',
        'nr_contract',
        'end_leasing',
        'description',
        'factoryNbr',
        'assetType_id',
        'year_production',
        'nr_policy',
        'expire',
        'contract_status',
        'insurance_company_name',
        'insurance_company_id',
        'insurance',
        'contribution',
        'netto_brutto',
        'gap',
        'legal_protection',
        'active'
    ];

    public static function boot(){
        parent::boot();

        static::addGlobalScope(new \Idea\Scopes\ObjectManageableScope());
    }


    public function client()
    {
        return $this->belongsTo('Clients');
    }
    public function owner()
    {
        return $this->belongsTo('Owners');
    }

    public function insurance_company()
    {
        return $this->belongsTo('Insurance_companies');
    }

    public function assetType()
    {
        return $this->belongsTo('ObjectAssetType', 'assetType_id');
    }


}