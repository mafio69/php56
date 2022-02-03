<?php

class OwnersGroup extends \Eloquent {
    protected $table = 'owners_group';
	protected $fillable = ['name'];

    public function owners()
    {
        return $this->hasMany('Owners');
    }

    public function injuryDocumentTypes()
    {
        return $this->belongsToMany('InjuryDocumentType', 'injury_document_type_owners_group', 'owners_group_id', 'injury_document_type_id')->where('active', 0);
    }
}