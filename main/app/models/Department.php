<?php

class Department extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['user_id', 'name'];

	protected $dates = ['deleted_at'];
}