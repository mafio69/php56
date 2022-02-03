<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Brands extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'brands';
    protected $fillable = [
        'typ',
        'name',
        'nazwa_alt',
        'key_mobileeu',
        'key_otomoto',
        'key_gratka',
        'key_trader',
        'allegro_cat',
        'if_multibrand'
    ];
    protected $dates = ['deleted_at'];

    public function models()
    {
        return $this->hasMany('Brands_model', 'brand_id');
    }
}