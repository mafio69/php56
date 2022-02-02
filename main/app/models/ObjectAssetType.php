<?php

class ObjectAssetType extends Eloquent
{
    protected $table = 'object_assetType';

    protected $fillable = ['name', 'if_yacht'];

    public function objects()
    {
        return $this->hasMany('Objects', 'assetType_id');
    }

    public function group()
    {
        return $this->belongsTo('ObjectAssetTypeGroup', 'object_assetType_group_id');
    }

}