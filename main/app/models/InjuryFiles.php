<?php


class InjuryFiles extends Eloquent
{
    protected $table = 'injury_files';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function document_type()
    {
        return $this->belongsTo('InjuryDocumentType', 'category');
    }

    public function document()
    {
        return $this->morphTo();
    }

    public function invoice()
    {
        return $this->hasOne('InjuryInvoices');
    }

    public function compensation()
    {
        return $this->hasOne('InjuryCompensation');
    }

    public function note()
    {
        return $this->belongsTo('InjuryNote', 'injury_note_id');
    }
}