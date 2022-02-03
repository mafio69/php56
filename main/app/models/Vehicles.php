<?php

class Vehicles extends Eloquent
{
    protected $table = 'vehicles';

    protected $fillable = [
        'syjon_vehicle_id',
        'syjon_contract_id',
        'syjon_contract_internal_agreement_id',
        'syjon_policy_id',

        'owner_id',
        'client_id',
        'parent_id',
        'registration',
        'VIN',
        'brand',
        'model',
        'engine',
        'nr_contract',
        'nr_vb',
        'year_production',
        'first_registration',
        'mileage',
        'expire',
        'contribution',
        'netto_brutto',
        'assistance',
        'assistance_name',
        'insurance',
        'risks',
        'nr_policy',
        'object_type',
        'contract_status',
        'gap',
        'legal_protection',
        'syjon_program_id',
        'active',
        'insurance_company_name',
        'insurance_company_id',
        'policy_insurance_company_id',
        'end_leasing',
        'cfm',
        'register_as',
        'seller_id'
    ];

    protected $with = ['owner'];

    public static function boot(){
        parent::boot();

        static::addGlobalScope(new \Idea\Scopes\VehicleManageableScope());
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

    public function policyInsuranceCompany()
    {
        return $this->belongsTo('Insurance_companies', 'policy_insurance_company_id');
    }

    public function injuries()
    {
        return $this->morphMany('Injury', 'vehicle');
    }

    public function getInsuranceExpireDateAttribute()
    {
        return $this->attributes['expire'];
    }

    public function salesProgram()
    {
        return $this->belongsTo('SyjonProgram', 'syjon_program_id');
    }

    public function seller()
    {
        return $this->belongsTo('VehicleSellers', 'seller_id');
    }

    public function getProgramAttribute()
    {
        $syjonProgram = $this->salesProgram;

        if($syjonProgram)
        {
            $plan_exist = \Plan::where('sales_program', $syjonProgram->name_key)->first();
            if($plan_exist) {
                return $syjonProgram->name;
            }
        }

        return null;
    }
}
