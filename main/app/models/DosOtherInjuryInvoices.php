<?php

class DosOtherInjuryInvoices extends Eloquent
{
    protected $table = 'dos_other_injury_invoices';

    protected $guarded = array();

    public function injury()
    {
        return $this->belongsTo('DosOtherInjury');
    }

    public function injury_files()
    {
        return $this->belongsTo('DosOtherInjuryFiles');
    }

    public function invoicereceive()
    {
        return $this->belongsTo('Invoicereceives', 'invoicereceives_id');
    }

    public function parent()
    {
        return $this->belongsTo('DosOtherInjuryFiles', 'parent_id');
    }
}