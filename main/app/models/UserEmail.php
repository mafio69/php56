<?php


class UserEmail extends Eloquent
{
    protected $fillable = [
        'user_id', 'email'
    ];

    public function user()
    {
        return $this->belongsTo('User');
    }
}
