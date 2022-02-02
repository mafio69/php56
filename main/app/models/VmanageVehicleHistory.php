<?php

class VmanageVehicleHistory extends \Eloquent {

    protected $fillable = [
        'history_id',
        'vmanage_vehicle_id',
        'previous_vmanage_vehicle_id'
    ];


    public function vehicle()
    {
        return $this->belongsTo('VmanageVehicle', 'vmanage_vehicle_id')->withTrashed();
    }

    public function previous_vehicle()
    {
        return $this->belongsTo('VmanageVehicle', 'previous_vmanage_vehicle_id')->withTrashed();
    }
}