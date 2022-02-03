<?php

class BranchBrand extends \Eloquent {
    protected $table = 'branches_brands';
	protected $fillable = ['branch_id', 'brand_id', 'authorization', 'if_multibrand'];

	public function branch()
    {
        return $this->belongsTo('Branch');
    }

    public function brand()
    {
        return $this->belongsTo('Brands');
    }
}