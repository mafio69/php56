<?php

class InjuryUploadedDocumentType extends Eloquent
{
    protected $fillable = ['parent_id', 'name', 'ordering', 'hidden'];

    public $timestamps = false;

    public function subtypes()
    {
        return $this->hasMany('InjuryUploadedDocumentType', 'parent_id');
    }

    public function notes()
    {
        return $this->morphMany('InjuryDocumentNoteAvailability', 'document');
    }
}