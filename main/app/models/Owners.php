<?php
class Owners extends Eloquent
{
    protected $table = 'owners';
    protected $fillable = [
        'syjon_contractor_id',
        'name',
        'old_name',
        'short_name',
	    'old_nip',
        'post',
        'city',
        'street',
        'wsdl',
        'wsdl_location',
        'wsdl_login',
        'wsdl_password',
        'owners_group_id',
        'document_template_id',
        'conditional_document_template_id',
        'active'
    ];

    public function group()
    {
        return $this->belongsTo('OwnersGroup', 'owners_group_id');
    }

    public function vmanage_company()
    {
        return $this->hasOne('VmanageCompany', 'owner_id');
    }

    public function company_groups()
    {
        return $this->belongsToMany('CompanyGroup', 'company_group_owners', 'owner_id');
    }

    public function data()
    {
        return $this->hasMany('Idea_data', 'owner_id');
    }

    public function nip()
    {
        return $this->hasMany('Idea_data', 'owner_id')->where('parameter_id', 8);
    }

    public function documentTemplate()
    {
        return $this->belongsTo('DocumentTemplate', 'document_template_id');
    }

    public function conditionalDocumentTemplate()
    {
        return $this->belongsTo('DocumentTemplate', 'conditional_document_template_id');
    }
}
