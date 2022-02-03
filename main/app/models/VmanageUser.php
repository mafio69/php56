<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class VmanageUser extends \Eloquent {
    use SoftDeletingTrait;

    protected $table = "vmanage_users";
    protected $fillable = [
        'name',
        'surname',
        'phone',
        'email',
        'vmanage_company_id'
    ];
    protected $dates = ['deleted_at'];

    public function company()
    {
        return $this->belongsTo('VmanageCompanies', 'vmanage_company_id');
    }
}