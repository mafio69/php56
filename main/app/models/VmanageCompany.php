<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class VmanageCompany extends \Eloquent {
	use SoftDeletingTrait;

	protected $table = "vmanage_companies";
	protected $fillable = [
		'owner_id',
		'client_id',
		'name',
		'nip',
		'regon',
		'street',
		'post',
		'city',
		'phone',
		'mail'
	];
	protected $dates = ['deleted_at'];

	public function owner()
	{
		return $this->belongsTo('Owners');
	}

	public function client()
	{
		return $this->belongsTo('Clients');
	}

	public function vehicles()
	{
		return $this->hasMany('VmanageVehicle', 'vmanage_company_id')->where('outdated', 0)->where('if_truck', 0);
	}

    public function trucks()
    {
        return $this->hasMany('VmanageVehicle', 'vmanage_company_id')->where('outdated', 0)->where('if_truck', 1);
    }

	public function users()
	{
		return $this->hasMany('VmanageUser', 'vmanage_company_id');
	}

	public function guardians()
	{
		return $this->belongsToMany('User', 'vmanage_company_user', 'vmanage_company_id', 'user_id');
	}

	public function csm()
	{
		return $this->hasMany('VmanageCompanyCsm', 'vmanage_company_id');
	}

	public function csmTypes()
	{
		return $this->hasMany('VmanageCsmType', 'vmanage_company_id');
	}
}
