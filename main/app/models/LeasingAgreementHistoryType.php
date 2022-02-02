<?php

class LeasingAgreementHistoryType extends \Eloquent {
	protected $fillable = ['content', 'warning','log_changes'];

    public $timestamps = false;
}