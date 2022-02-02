<?php

class ObjectAssetTypeGroup extends Eloquent {

    protected $fillable = ['name'];

    public function types()
    {
        return $this->hasMany('ObjectAssetType', 'object_assetType_group_id');
    }
}