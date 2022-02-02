<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Idea\Gap\UserInfo;

class GapAgreement extends Eloquent {

	use SoftDeletingTrait,UserInfo;

	protected $fillable = [
		'agreement_number',
		'group_id',
		'pesel',
		'regon',
		'gross_net',
		'type_id',
		'contribution',
		'time',
		'activation_date',
		'accept_date',
		'status_id',
	];

	protected $dates = ['deleted_at','activation_date','accept_date'];

	public function object(){
		return $this->hasOne('GapAgreementObject','agreement_id');
	}

	public function histories(){
		return $this->hasMany('GapAgreementHistory','agreement_id');
	}

	public function status(){
		return $this->belongsTo('GapAgreementStatus','status_id');
	}

	public function type(){
		return $this->belongsTo('GapAgreementType','type_id');
	}

	public function group(){
		return $this->belongsTo('GapAgreementGroup','group_id');
	}

	public function author(){
		return $this->belongsTo('User','user_id');
	}

	public function storeHistory($type,$description=null,$object=null,$value = null){
		$type_object = GapAgreementHistoryType::where('name',$type)->first();
		$history = null;
		if($type_object){
			$history = $this->histories()->create([
				'type_id'=>$type_object->id,
				'description'=>$description,
				'value'=>$value,
			]);
		}
		return $history;
	}





}
