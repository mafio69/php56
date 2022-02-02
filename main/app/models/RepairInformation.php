<?php

class RepairInformation extends Eloquent
{
    protected $table = 'repair_information';

	protected $fillable = [
        'name'
	];

	public $timestamps = false;
}
