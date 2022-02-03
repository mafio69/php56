<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class Brands_model extends \Eloquent {
	use SoftDeletingTrait;

	protected $fillable = [
		'typ',
        'brand_id',
        'name',
        'key_mobileeu',
        'key_otomoto',
        'key_gratka',
        'key_trader',
		'allegro_cat',
		'if_multibrand'
    ];
	protected $dates = ['deleted_at'];

	public function brand()
	{
		return $this->belongsTo('Brands', 'brand_id');
	}

	public function generations()
	{
		return $this->hasMany('Brands_models_generation', 'id_model_om', 'key_otomoto');
	}

}