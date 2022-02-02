<?php

class InjuryDocumentType extends Eloquent
{
    protected $table = 'injury_document_type';

    protected $fillable = ['name', 'short_name', 'template_name', 'task_authorization', 'fee', 'if_fee_collection', 'alert_name', 'conditions', 'cfm', 'pdf', 'chronology', 'active'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function availabilities()
    {
        return $this->hasMany('InjuryDocumentTypeAvailability');
    }

    public function steps()
    {
        return $this->belongsToMany('InjurySteps', 'injury_document_type_availability', 'injury_document_type_id', 'injury_steps_id');
    }

    public function getAvailabilities()
    {
        $availabilities = $this->availabilities;
        $result = array();
        foreach ($availabilities as $avaibility)
        {
            $result[] = $avaibility->injury_steps_id;
        }
        return $result;
    }

    public function ownersGroups()
    {
        return $this->belongsToMany('OwnersGroup', 'injury_document_type_owners_group', 'injury_document_type_id', 'owners_group_id');
    }

    public function notes()
    {
        return $this->morphMany('InjuryDocumentNoteAvailability', 'document');
    }
}