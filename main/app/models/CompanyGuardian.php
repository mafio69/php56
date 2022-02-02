<?php

class CompanyGuardian extends Eloquent{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'company_guardians';

	protected $fillable = [
        'phone'
    ];

    public function user() {
        return $this->belongsTo('User', 'user_id');
    }

    public function email() {
        return $this->user->email;
    }

    public function name() {
        return $this->user->name;
    }
}
