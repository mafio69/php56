<?php
namespace Idea\Observers;

use Auth;
use Idea\SyjonService\SyjonService;
use InjuryNote;

class InjuryObserver {

    public function updated($injury)
    {
        $store=[
          'il_repair_info',
          'if_il_repair',
          'in_service',
          'reported_ic',
          'il_repair_info_description',
          'injuries_type_id',
          'receive_id',
          'invoicereceives_id',
          'settlement_cost_estimate',
          'date_event',
          'time_event',
          'type_incident_id',
          'insurance_company_id',
          'injury_nr',
          'police',
          'police_nr',
          'police_unit',
          'police_contact',
          'if_statement',
          'if_registration_book',
          'if_driver_fault',
          'if_towing',
          'if_courtesy_car',
          'if_door2door',
        ];

        foreach($injury->getDirty() as $key=>$item){
            if(in_array($key,$store)){
                $injury->changes()->create(['name'=>$key,'value'=>$injury->getOriginal($key)]);
            }
            if($injury->vehicle->syjon_vehicle_id) {
                $syjonService = new SyjonService();

                if ($key == 'total_status_id') {
                    $syjonService->updateTotalStatus($injury->vehicle->syjon_vehicle_id, $item);
                }elseif ($key == 'theft_status_id') {
                    $syjonService->updateTheftStatus($injury->vehicle->syjon_vehicle_id, $item);
                }elseif ($key == 'injury_total_step_stage_id' || $key == 'injury_step_stage_id') {
                    $syjonService->updateStage($injury->vehicle->syjon_vehicle_id, $item);
                }elseif($key == 'step'){
                    $syjonService->updateStatus($injury->vehicle->syjon_vehicle_id, $item);
                }
            }
        }

        if($injury->sap) {
            $status = null;
            if (array_key_exists('total_status_id', $injury->getDirty())) {
                $status = $injury->totalStatus;
            }elseif (array_key_exists( 'theft_status_id', $injury->getDirty())) {
                $status = $injury->theftStatus;
            }

            if($status) {
                $noteAvailabilities = $status->notes;
                foreach ($noteAvailabilities as $noteAvailability) {
                    $sap = new \Idea\SapService\Sap();
                    $notes[0] = $noteAvailability->note;
                    $result = $sap->szkodaNotUtworz($injury, $notes);

                    $errors = [];
                    if (isset($result['ftReturn']) && is_array($result['ftReturn'])) {
                        foreach ($result['ftReturn'] as $ftReturn) {
                            if ($ftReturn['typ'] == 'E') {
                                $errors[] = $ftReturn;
                            }
                        }
                    }

                    if (count($errors) > 0) {
                    } else {
                        foreach ($result['ftNotatkaN'] as $note_item => $note) {
                            InjuryNote::create([
                                'injury_id' => $injury->id,
                                'user_id' => Auth::user() ? Auth::user()->id : null,
                                'roknotatki' => $note['roknotatki'],
                                'nrnotatki' => $note['nrnotatki'],
                                'obiekt' => $note['obiekt'],
                                'temat' => $note['temat'],
                                'data' => $note['data'],
                                'uzeit' => $note['uzeit'],
                            ]);
                        }
                    }
                }
            }
        }


    }

    public function creating($injury){
        if(! $injury->sap_rodzszk ){
            $injury->sap_rodzszk = 'CZ';
        }

        if(! $injury->sap_stanszk ){
            $injury->sap_stanszk = 1;
        }
    }
}
