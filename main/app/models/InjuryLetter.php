<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class InjuryLetter extends \Eloquent {
	use SoftDeletingTrait;

	protected $fillable = [
		'user_id',
		'injury_file_id',
        'category',
		'injury_nr',
		'nr_contract',
		'registration',
		'file',
		'name',
		'description',
		'nr_document',
		'is_unprocessed'
	];
	protected $dates = ['deleted_at'];

	public function user(){
		return $this->belongsTo('User')->withTrashed();
	}

	public function injury_file(){
		return $this->belongsTo('InjuryFiles');
	}

	public function uploadedDocumentType()
    {
        return $this->belongsTo('InjuryUploadedDocumentType', 'category');
    }
}