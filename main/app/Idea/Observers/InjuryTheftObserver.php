<?php


namespace Idea\Observers;


use Idea\SyjonService\SyjonService;

class InjuryTheftObserver
{
    public function updated($theft)
    {
        $fields = [
            'deregistration_vehicle_confirm' => 'deregistration_vehicle'
        ];

        foreach($theft->getDirty() as $key=>$item){
            if($theft->injury->vehicle->syjon_vehicle_id && key_exists($key, $fields)) {
                if($item && $item != '0000-00-00') {
                    $syjonService = new SyjonService();

                    $syjonService->updateField($theft->injury->vehicle->syjon_vehicle_id, $fields[$key], $theft->{ $fields[$key]});
                }
            }
        }
    }
}