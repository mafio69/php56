<?php

class UserFooter extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = [
	    'user_id',
        'name',
        'footer'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('User');
    }
}