<?php


namespace Idea\Observers;


use Idea\SyjonService\SyjonService;

class InjuryWreckObserver
{
    public function updated($wreck)
    {
        $fields = [
            'off_register_vehicle_confirm' => 'off_register_vehicle',
            'alert_buyer_confirm' => 'alert_buyer',
            'pro_forma_request_confirm' => 'pro_forma_request',
            'payment_confirm' => 'payment'
        ];

        foreach($wreck->getDirty() as $key=>$item){
            if($wreck->injury->vehicle->syjon_vehicle_id && key_exists($key, $fields)) {
                if($item && $item != '0000-00-00') {
                    $syjonService = new SyjonService();
                    $syjonService->updateField($wreck->injury->vehicle->syjon_vehicle_id, $fields[$key], $wreck->{$fields[$key]});
                }
            }
        }
    }
}