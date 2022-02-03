<?php

class InjuryNote extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = [
	    'referenceable_id',
        'referenceable_type',
	    'injury_id',
        'user_id',
        'roknotatki',
        'nrnotatki',
        'obiekt',
        'temat',
        'data',
        'uzeit'
    ];

    protected $dates = ['deleted_at'];

	public function injury(){
	    return $this->belongsTo('Injury');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function getSourceAttribute()
    {
        switch ($this->referenceable_type){
            case 'InjuryInvoices':
                return 'Faktura';
            case 'InjuryChatMessages':
                return 'Komunikator';
            case 'InjuryCompensation':
                return 'Odszkodowanie';
            default:
                return 'Notka Własna';
        }
    }
}