<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class VmanageVehicle extends \Eloquent {
	use SoftDeletingTrait;

	protected $table = 'vmanage_vehicles';
	protected $fillable = [
	    'if_truck',
		'vmanage_company_id',
        'owner_id',
        'client_id',
        'brand_id',
        'model_id',
        'generation_id',
        'vmanage_seller_id',
        'version',
        'car_category_id',
        'year_production',
        'first_registration',
        'doors_nb',
        'car_engine_id',
        'engine_capacity',
        'horse_power',
        'car_gearbox_id',
        'registration',
        'vin',
        'nr_contract',
        'nr_policy',
        'object_type',
        'vmanage_user_id',
        'place_of_usage',
        'declare_mileage',
        'actual_mileage',
        'technical_exam_date',
        'servicing_date',
        'servicing_mileage',
        'insurance_expire_date',
        'insurance',
        'risks',
        'contribution',
        'netto_brutto',
        'assistance',
        'cfm',
        'gap',
        'legal_protection',
        'dls_program_id',
        'contract_status',
        'insurance_company_id',
        'policy_insurance_company_id',
        'register_as',
        'keeper_email',
        'min_franchise',
        'if_return',
        'if_vip',
        'outdated',
        'agreement_date',
        'end_leasing',
        'deleting_file'
    ];

	protected $dates = ['deleted_at'];

    protected $with = ['owner', 'brand', 'model'];

    public static function boot(){
        parent::boot();

        static::addGlobalScope(new \Idea\Scopes\VmanageVehicleManageableScope());
    }

    public function setRegistrationAttribute($value)
    {
        $this->attributes['registration'] = trim($value);
    }

	public function company()
	{
		return $this->belongsTo('VmanageCompany', 'vmanage_company_id');
	}

	public function brand()
	{
		return $this->belongsTo('Brands', 'brand_id');
	}

	public function model()
	{
		return $this->belongsTo('Brands_model', 'model_id');
	}

	public function generation()
	{
		return $this->belongsTo('Brands_models_generation', 'generation_id');
	}

    public function user()
    {
        return $this->belongsTo('VmanageUser', 'vmanage_user_id');
    }

    public function car_category()
    {
        return $this->belongsTo('Car_category');
    }

    public function car_gearbox()
    {
        return $this->belongsTo('Car_gearboxes');
    }

    public function car_engine()
    {
        return $this->belongsTo('Car_engines');
    }

    public function owner()
    {
        return $this->belongsTo('Owners');
    }

    public function users()
    {
        return $this->belongsToMany('VmanageUser', 'vmanage_vehicle_users');
    }

    public function injuries()
    {
        return $this->morphMany('Injury', 'vehicle');
    }

    public function client()
    {
        return $this->belongsTo('Clients');
    }

    public function insurance_company()
    {
        return $this->belongsTo('Insurance_companies', 'insurance_company_id');
    }

    public function policyInsuranceCompany()
    {
        return $this->belongsTo('Insurance_companies', 'policy_insurance_company_id');
    }

    public function seller()
    {
        return $this->belongsTo('VmanageSeller', 'vmanage_seller_id');
    }

    public function getExpireAttribute()
    {
        return $this->attributes['insurance_expire_date'];
    }

    public function history()
    {
        return $this->hasOne('VmanageVehicleHistory', 'previous_vmanage_vehicle_id');
    }

    public function salesProgram(){
        return $this->belongsTo('DlsProgram', 'dls_program_id');
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
