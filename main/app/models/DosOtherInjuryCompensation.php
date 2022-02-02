<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DosOtherInjuryCompensation extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'dos_other_injury_compensations';

    protected $fillable =
        [
            'injury_id',
            'injury_files_id',
            'user_id',
            'date_decision',
            'injury_compensation_decision_type_id',
            'receive_id',
            'compensation',
            'net_gross',
            'remarks'
        ];
    protected $dates = ['deleted_at'];

    public function injury()
    {
        return $this->belongsTo('DosOtherInjury');
    }

    public function injury_files()
    {
        return $this->belongsTo('DosOtherInjuryFiles');
    }

    public function injury_file()
    {
        return $this->belongsTo('DosOtherInjuryFiles', 'injury_files_id');
    }

    public function user()
    {
        return $this->belongsTo('User')->withTrashed();
    }

    public function decisionType()
    {
        return $this->belongsTo('InjuryCompensationDecisionType', 'injury_compensation_decision_type_id')->withTrashed();
    }

    public function receive()
    {
        return $this->belongsTo('Receives');
    }
}
