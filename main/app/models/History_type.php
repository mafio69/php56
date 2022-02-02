<?php
class History_type extends Eloquent
{
    protected $table = 'history_type';
    public $timestamps = false;

    protected $fillable = [ 'content' , 'injury_processing_type_id' ];
}
