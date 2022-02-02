<?php

class Buyer extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $fillable = ['name', 'address_street', 'address_code', 'address_city', 'nip', 'regon', 'account_nr', 'phone', 'email', 'contact_person', 'active'];

    protected $dates = ['deleted_at'];

}