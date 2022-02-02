<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class VmanageSeller extends \Eloquent {
    use SoftDeletingTrait;

    protected $table = "vmanage_sellers";
    protected $fillable = [
        'name',
        'nip',
        'street',
        'post',
        'city',
        'phone'
    ];
    protected $dates = ['deleted_at'];


}