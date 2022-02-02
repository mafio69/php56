<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class PlanGroup extends \Eloquent {
    use SoftDeletingTrait;
	protected $fillable = ['plan_id', 'name', 'ordering'];
	protected $dates = ['deleted_at'];

    public static function boot()
    {
        parent::boot();
        static::deleted(function($planGroup)
        {
            $planGroup->branchPlanGroups()->delete();
        });
    }

	public function plan()
    {
        return $this->belongsTo('Plan');
    }

    public function branchPlanGroups()
    {
        return $this->hasMany('BranchPlanGroup');
    }

    public function companyGroups()
    {
        return $this->belongsToMany('CompanyGroup', 'plan_group_company_group','plan_group_id', 'company_group_id');
    }
}