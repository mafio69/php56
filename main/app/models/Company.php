<?php

class Company extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;
	protected $fillable = [
        'is_active_vat',
        'company_vat_check_id',
		'commission_type_id',
		'billing_cycle_id',
        'contractor_group_id',
		'name',
		'street',
		'code',
		'city',
		'nip',
		'krs',
		'regon',
		'www',
		'email',
		'phone',
		'remarks',
		'commission',
		'account_nr',
		'active',
		'new',
		'dirty',
        'service_cession_data'
	];

	protected $dates = ['deleted_at'];

    public function setNipAttribute($value)
    {
        $this->attributes['nip'] = preg_replace("/[^0-9]/", "", $value );
    }
    
	public function commissionType()
	{
		return $this->belongsTo('CommissionType');
	}

	public function billingCycle()
	{
		return $this->belongsTo('BillingCycle');
	}

	public function commissions()
	{
		return $this->hasMany('CompanyCommission');
	}

	public function invoiceCommissions()
	{
		return $this->hasMany('Commission', 'company_id');
	}

    public function contractorGroup()
    {
        return $this->belongsTo('ContractorGroup', 'contractor_group_id');
	}
	
	public function guardian()
    {
        return $this->belongsTo('CompanyGuardian', 'guardian_id');
    }

    public function groups()
    {
        return $this->belongsToMany('CompanyGroup', 'company_company_group', 'company_id', 'company_group_id')
            ->whereNull('company_company_group.deleted_at')
            ->withTimestamps()
            ->withPivot('user_id');
    }

    public function allGroups()
    {
        return $this->belongsToMany('CompanyGroup', 'company_company_group', 'company_id', 'company_group_id')
            ->withTimestamps()
            ->withPivot('user_id');
    }

    public function branches() {
		return $this->hasMany('Branch');
	}

    public function companyVatCheck()
    {
        return $this->belongsTo('CompanyVatCheck');
    }

    public function companyVatChecks()
    {
        return $this->hasMany('CompanyVatCheck')->orderBy('id', 'desc');
    }

    public function accountNumbers()
    {
        return $this->hasMany('CompanyAccountNumbers');
    }

    public function accountNumbersWithTrashed()
    {
        return $this->hasMany('CompanyAccountNumbers')->withTrashed();
    }

    public function accountNumbersOnlyTrashed()
    {
        return $this->hasMany('CompanyAccountNumbers')->onlyTrashed();
    }
    

    public function scopeHasTerm($query, $term)
    {
        return $query->leftJoin('branches', 'branches.company_id', '=', 'companies.id')->where(function ($q) use ($term) {
            $q->where('branches.city', 'like', '%' . $term . '%')->orWhere('branches.short_name', 'like', '%' . $term . '%')->orWhere('name', 'like', '%' . $term . '%');
        });

    }

    public function getAddressAttribute()
    {
        $address = $this->code.' '.$this->city.', '.$this->street;
        if(trim($address) != ','){
            return $address;
        }else{
            return null;
        }
    }
}
