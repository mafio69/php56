<?php

class Insurance_companies extends Eloquent
{
    protected $table = 'insurance_companies';
    protected $fillable = [
        'sap_id',
        'parent_id',
        'name',
        'street',
        'post',
        'city',
        'contact_person',
        'email',
        'phone',
        'if_rounding',
        'if_full_year',
        'active'
    ];

    public function documentsTypes()
    {
        return $this->belongsToMany('LeasingAgreementDocumentType','leasing_agreement_document_type_company','company_id','document_id');
    }

    public function parent() {
        return $this->belongsTo('Insurance_companies', 'parent_id');
    }

    public function children() {
        return $this->hasMany('Insurance_companies', 'parent_id');
    }
}
