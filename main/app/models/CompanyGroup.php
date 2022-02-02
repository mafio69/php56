<?php

class CompanyGroup extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $fillable = ['name','marker'];
    protected $dates = ['deleted_at'];

    public function companies()
    {
        return $this->belongsToMany('Company', 'company_company_group')->withTimestamps()->withPivot('user_id');
    }

    public function owners()
    {
        return $this->belongsToMany('Owners', 'company_group_owners', 'company_group_id', 'owner_id');
    }

    public function planGroups()
    {
        return $this->belongsToMany('PlanGroup', 'plan_group_company_group', 'company_group_id', 'plan_group_id');
    }
}
