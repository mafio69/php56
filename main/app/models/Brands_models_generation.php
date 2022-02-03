<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Brands_models_generation extends \Eloquent {
	use SoftDeletingTrait;

	protected $fillable = ['id_model_om', 'id_marka_om', 'name'];
	protected $dates = ['deleted_at'];

	public function model()
	{
		return $this->belongsTo('Brands_model', 'id_model_om', 'key_otomoto');
	}
}