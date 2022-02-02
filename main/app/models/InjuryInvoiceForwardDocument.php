<?php

class InjuryInvoiceForwardDocument extends \Eloquent {
	protected $fillable = [
	    'injury_invoice_id',
        'injury_invoice_forward_document_type_id',
    ];

    public function type()
    {
        return $this->belongsTo('InjuryInvoiceForwardDocumentType', 'injury_invoice_forward_document_type_id');
    }

    public function invoice()
    {
        return $this->belognsTo('InjuryInvoice', 'injury_invoice_id');
    }

}