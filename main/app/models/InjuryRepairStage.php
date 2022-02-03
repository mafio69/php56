<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class InjuryRepairStage extends Eloquent
{
    use SoftDeletingTrait;

    protected $fillable = ['injury_id', 't_injury_repair_stage_id', 'value', 'date_value', 'comment'];
    protected $dates = ['date_value', 'deleted_at'];

    public function injury()
    {
        return $this->belongsTo('Injury');
    }

    public function stage()
    {
        return $this->belongsTo('TInjuryRepairStage', 't_injury_repair_stage_id');
    }
}