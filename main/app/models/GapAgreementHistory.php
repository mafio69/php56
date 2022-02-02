<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Idea\Gap\UserInfo;

class GapAgreementHistory extends Eloquent {

	use SoftDeletingTrait,UserInfo;

	protected $fillable = [
		'type_id',
		'agreement_id',
		'object_id',
		'object_type',
		'description',
		'value',
	];

	protected $dates = ['deleted_at','created_at','updated_at'];

	public function agreement(){
		return $this->belongsTo('GapAgreementObject','agreement_id');
	}

	public function type(){
		return $this->belongsTo('GapAgreementHistoryType','type_id');
	}

	public function author(){
		return $this->belongsTo('User','user_id');
	}


}
