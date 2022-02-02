<?php

class InjuryContractStatusHistory extends \Eloquent {
	protected $fillable = ['injury_id', 'contract_status_id', 'vehicle_id', 'vehicle_type'];

	public function injury()
    {
        return $this->belongsTo('Injury', 'injury_id');
    }

    public function contractStatus()
    {
        $this->belongsTo('ContractStatus', 'contract_status_id');
    }

    public function vehicle()
    {
        return $this->morphTo()->withTrashed();
    }
}