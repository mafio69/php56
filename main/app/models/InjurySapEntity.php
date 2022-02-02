<?php

class InjurySapEntity extends Eloquent
{
    protected $fillable = array(
        'injury_id',
        'szkodaId',
        'rokum',
        'nrum',
        'symbol',
        'nrrej',
        'nrpolisy',
        'nrpolisyZew',
        'dataszkody',
        'rodzub',
        'rodzszk',
        'stanszk',
        'towub',
        'kwota',
        'kwotaOdsz',
        'kwotawypl',
        'datawypl',
        'odmowa',
        'uwagi',
        'odbWarsz',
        'odbLb',
        'odbGl',
        'odbInny',
        'inne',
        'kosztH',
        'kosztP',
        'kosztI',
        'kwPotrRat',
        'kwPotrInn',
        'kwPozost',
        'mPostoju',
        'datWazSprzWra',
        'towlikw',
        'idWarsz',
        'field1',
        'field2',
        'field3',
        'field4',
        'created_at'
    );

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function responses()
    {
        return $this->hasMany('InjurySapResponse');
    }
}