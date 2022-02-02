<?php

class LeasingAgreementDocumentType extends Eloquent
{

    public function company()
    {
        return $this->belongsToMany('Insurance_companies','leasing_agreement_document_type_company','company_id','document_id');
    }

}
