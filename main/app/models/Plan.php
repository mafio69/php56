<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Plan extends \Eloquent {
    use SoftDeletingTrait;
	protected $fillable = ['name', 'sales_program'];
	protected $dates = ['deleted_at'];

    public function groups()
    {
        return $this->hasMany('PlanGroup');
    }
}