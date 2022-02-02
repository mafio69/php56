<?php


class DosOtherInjuryFiles extends Eloquent
{
    protected $table = 'dos_other_injury_files';

    protected $guarded = array();


    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function injury()
    {
        return $this->belongsTo('DosOtherInjury');
    }

    public function document_type()
    {
        return $this->belongsTo('DosOtherInjuryDocumentType', 'category');
    }
}