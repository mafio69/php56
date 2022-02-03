<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Idea\Gap\UserInfo;

class GapAgreementObjectGroup extends Eloquent {

	use SoftDeletingTrait,UserInfo;

	protected $fillable = [
		'name',
		'description',
		'type_id',
	];

	protected $dates = ['deleted_at','created_at','updated_at'];

	public function object(){
		return $this->belongsTo('GapAgreementObject','group_id');
	}

	public function type(){
		return $this->belongsTo('GapAgreementObjectType','type_id');
	}

	public function author(){
		return $this->belongsTo('User','user_id');
	}


}
