<?php

class ContractStatus extends \Eloquent {
	protected $fillable = ['name', 'is_active'];

	public function getActiveAttribute()
    {
        return $this->is_active;
    }
}