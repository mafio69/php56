<?php


class InjuryDocumentNoteAvailability extends Eloquent
{
    protected $fillable = [
        'document_id',
        'document_type',
        'note',
        'receive_id'
    ];

    public function document()
    {
        return $this->morphTo();
    }

    public function receive()
    {
        return $this->belongsTo('Receives');
    }
}