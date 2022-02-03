<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LiquidationCards extends \Eloquent {
    use SoftDeletingTrait;

    protected $table = "liquidation_cards";

	protected $fillable = ['number', 'vehicle_id', 'release_date', 'expiration_date', 'user_id'];

    protected $dates = ['deleted_at'];

    public function vehicle()
    {
        return $this->belongsTo('Vehicles');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

}