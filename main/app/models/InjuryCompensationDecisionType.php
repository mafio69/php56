<?php

class InjuryCompensationDecisionType extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['name', 'short_name', 'deleted_at'];
	public $timestamps = false;

    protected $dates = ['deleted_at'];
}