<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class BranchPlanGroup extends \Eloquent {
    use SoftDeletingTrait;
	protected $fillable = ['branch_id', 'plan_group_id'];
	protected $dates = ['deleted_at'];

	public function branch()
    {
        return $this->belongsTo('Branch');
    }

    public function planGroup()
    {
        return $this->belongsTo('PlanGroup');
    }

    public function branchBrands()
    {
        return $this->belongsToMany('BranchBrand', 'branch_brand_branch_plan_group')->withTimestamps()->withPivot('if_sold');
    }
}