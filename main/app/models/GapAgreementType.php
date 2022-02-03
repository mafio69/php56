<?php

class GapAgreementType extends Eloquent {

  protected $fillable = [
    'name',
  ];

  public function agreement(){
		return $this->belongsTo('GapAgreement','type_id');
	}

}
