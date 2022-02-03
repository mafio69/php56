<?php

class TaskBlackList extends \Eloquent {
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

	protected $fillable = ['user_id', 'email', 'topic'];

	protected $dates = ['deleted_at'];


}