<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Idea\Gap\UserInfo;

class GapAgreementObjectType extends Eloquent {

	use SoftDeletingTrait,UserInfo;

	protected $fillable = [
		'name',
		'code',
	];

	protected $dates = ['deleted_at','created_at','updated_at'];

	public function object(){
		return $this->belongsTo('GapAgreementObject','type_id');
	}

	public function author(){
		return $this->belongsTo('User','user_id');
	}


}
