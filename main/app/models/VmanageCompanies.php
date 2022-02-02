<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class VmanageCompanies extends \Eloquent {
	use SoftDeletingTrait;

	protected $table = "vmanage_companies";
	protected $fillable = ['name', 'nip', 'regon', 'street', 'code', 'city', 'phone', 'mail'];
	protected $dates = ['deleted_at'];

	public function vehicles()
	{
		return $this->hasMany('VmanageVehicle', 'company_id');
	}

	public function users()
	{
		return $this->hasMany('VmanageUsers', 'company_id');
	}
}