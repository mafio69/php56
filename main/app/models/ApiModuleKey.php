<?php

class ApiModuleKey extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;
	protected $fillable = ['api_module_id', 'api_key'];
	protected $dates = ['deleted_at'];

	public function apiModule()
    {
        return $this->belongsTo('ApiModule');
    }
}