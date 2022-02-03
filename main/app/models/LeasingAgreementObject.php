<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LeasingAgreementObject extends \Eloquent {
    use SoftDeletingTrait;

	protected $fillable = [
        'user_id',
        'leasing_agreement_id',
        'name',
        'producer',
        'object_assetType_id',
        'net_value',
        'gross_value',
        'fabric_number',
        'registration_number',
        'chassis_number',
        'production_year'
    ];

    protected $dates = ['deleted_at'];

    public function leasing_agreement()
    {
        return $this->belongsTo('LeasingAgreement','leasing_agreement_id');
    }

    public function object_assetType()
    {
        return $this->belongsTo('ObjectAssetType', 'object_assetType_id');
    }


}