<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class InjurySapPremium extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'injury_sap_premiums';
    
    protected $fillable = array(
        'injury_id',
        'injury_compensation_id',
        'nrRaty',
        'dataDpl',
        'kwDpl',
        'unameRej',
        'dataRej'
    );

    protected $dates = ['deleted_at'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function injuryCompensation()
    {
        return $this->belongsTo('InjuryCompensation');
    }
}