<?php

class Branch extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;
    protected $table = 'branches';
    protected $fillable = [
        'sap_id',
        'custom_id',
        'company_id',
        'transferred_company_id',
        'short_name',
        'nip',
        'street',
        'code',
        'city',
        'voivodeship_id',
        'invalid_post',
        'email',
        'other_emails',
        'phone',
        'contact_people', //
        'if_map',
        'if_map_correct',
        'lat',
        'lng',
        'tug',
        'tug24h',
        'tug_remarks', //
        'delivery_cars', //
        'remarks',
        'priority',
        'priorities', //
        'active',
        'new',
        'dirty',
        'suspended',
        'open_time', //
        'close_time' //
    ];
    protected $dates = ['deleted_at'];
    protected $appends = ['address'];

    public function setNipAttribute($value)
    {
        $this->attributes['nip'] = preg_replace("/[^0-9]/", "", $value );
    }

    public function company()
    {
        return $this->belongsTo('Company')->withTrashed();
    }

    public function typevehicle()
    {
    	return $this->belongsToMany('Typevehicles', 'branches_typevehicles')->withPivot('value');
    }

    public function typevehicles()
    {
        return $this->hasMany('Branches_typevehicles');
    }

    public function brand()
    {
        return $this->belongsToMany('Brands', 'branches_brands', 'branch_id', 'brand_id');
    }

    public function brands()
    {
        return $this->belongsToMany('Brands', 'branches_brands', 'branch_id', 'brand_id')->withPivot('authorization');
    }

    public function branchBrands()
    {
        return $this->hasMany('BranchBrand');
    }

    public function authorizations()
    {
        return $this->brands()->wherePivot('authorization', 1);
    }

    public function typegarages()
    {
        return $this->belongsToMany('Typegarage', 'branches_typegarages', 'branch_id', 'typegarages_id');
    }

    public function branchPlanGroups()
    {
        return $this->hasMany('BranchPlanGroup');
    }

    public function scopeActive($query)
    {
        return $query->where('branches.active', '=', 0);
    }

    public function scopeHasType($query, $type)
    {
        return $query->leftJoin('branches_typegarages', 'branches.id', '=', 'branches_typegarages.branch_id')->where('typegarages_id', '=', $type);
    }

    public function scopeHasIdeatype($query, $type)
    {
        return $query->leftJoin('companies', 'branches.company_id', '=', 'companies.id')->where('type', '=', $type);
    }

    public function injuries()
    {
        return $this->hasMany('Injury', 'branch_id');
    }

    public function voivodeship()
    {
        return $this->belongsTo('Voivodeship');
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

    public function transferredCompany()
    {
        return $this->belongsTo('Company')->withTrashed();
    }
}
