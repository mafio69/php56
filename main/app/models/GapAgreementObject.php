<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Idea\Gap\UserInfo;

class GapAgreementObject extends Eloquent {

	use SoftDeletingTrait,UserInfo;

	protected $fillable = [
		'name',
		'vin',
		'group_id',
		'type_id',
		'price',
		'type_id',
		'currency',
		'agreement_id',
	];

	protected $dates = ['deleted_at'];

	public function agreement(){
		return $this->belongsTo('GapAgreement','agreement_id');
	}

	public function type(){
		return $this->belongsTo('GapAgreementObjectType','type_id');
	}

	public function group(){
		return $this->belongsTo('GapAgreementObjectGroup','group_id');
	}






}
