<?php

class DosInjuryType extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;
    protected $table ='dos_injury_types';
	protected $fillable = ['name'];

	public $timestamps = false;
    protected $dates = ['deleted_at'];
}