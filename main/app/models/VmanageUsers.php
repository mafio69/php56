<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class VmanageUsers extends \Eloquent {
    use SoftDeletingTrait;

    protected $table = "vmanage_users";
    protected $fillable = ['name', 'surname', 'company_id'];
    protected $dates = ['deleted_at'];

    public function company()
    {
        return $this->belongsTo('VmanageCompanies', 'company_id');
    }
}