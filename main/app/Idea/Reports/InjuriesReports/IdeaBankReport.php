<?php

namespace Idea\Reports\InjuriesReports;

use Carbon\Carbon;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Injury;

class IdeaBankReport extends BaseReport implements ReportsInterface
{

    private $params;
    private $filename;

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;
    }

    public function generateReport()
    {
        set_time_limit(500);
        \DB::disableQueryLog();

        \Config::set('excel::csv.delimiter', ';');
        \Config::set('excel::csv.enclosure', '');

        Excel::create($this->filename, function($excel) {
            $excel->sheet('Export', function($sheet) {


                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                Injury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                    ->where('step', '!=', '-10')
                    ->whereHas('client', function($query){
                        $query->where('nip', trim($this->params['nip']));
                    })
                    ->with('vehicle', 'vehicle.user', 'vehicle.users', 'injuries_type', 'getRemarks','damages', 'damages.damage', 'driver', 'type_incident', 'historyEntries', 'compensations')
                    ->chunk(100, function($injuries) use (&$sheet){
                        foreach ($injuries as $k => $injury) {
                            $damages = $this->generateDamages($injury);
                            $driver_user = $this->getDriverUser($injury);
                            $injury_type = $this->getInjuryType($injury);
                            $injury_value = $this->getInjuryValue($injury);

                            if ($injury->if_driver_fault == 1){
                                $if_driver_fault = 'tak';
                            }elseif($injury->if_driver_fault == 0){
                                $if_driver_fault = 'nie';
                            }else{
                                $if_driver_fault = 'nie ustalono';
                            }

                            $sheet->appendRow(array(
                                $injury->injury_nr,
                                $injury->vehicle->registration,
                                ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin,
                                $injury->vehicle->mileage,
                                $injury_type,
                                $injury->injuries_type->name,
                                ($injury->injuries_type_id == 4) ? 'TAK' : 'NIE',
                                $injury->date_event,
                                $injury->created_at->format('Y-m-d'),
                                '',
                                '',
                                round($injury_value, 2),
                                ($injury->getRemarks) ? preg_replace("/[\n\r]/","", str_replace(';', ',', $injury->getRemarks->content) ) : '',
                                $damages,
                                $driver_user,
                                ( $injury->type_incident ) ? $injury->type_incident->name : '',
                                $if_driver_fault,
                                '1'
                            ));
                        }
                    });
            });

        })->export('csv');
    }

    private function generateDamages($injury)
    {
        $damages = '';
        if($injury->damages)
        {
            foreach($injury->damages as $damage)
            {
                $damages.=$damage->damage->name;
                if($damage->param != 0) {
                    if ($damage->param == 1) {
                        $damages.= ' lewe/y';
                    } else {
                        $damages .= ' prawe/y';
                    }
                }
                $damages.= ',';
            }
            $damages = substr($damages, 0, -1);
        }
        return $damages;
    }

    private function getDriverUser($injury)
    {
        $driver_info = '';

        if($injury->driver)
        {
            $driver_info = $injury->driver->name.' '.$injury->driver->surname.', tel: '.$injury->driver->phone.', mail: '.$injury->driver->email.', miasto: '.$injury->driver->city;
        }elseif($injury->vehicle_type == 'VmanageVehicle'){
            $date_event = Carbon::createFromFormat('Y-m-d', $injury->date_event)->endOfDay();
            if($injury->vehicle->user){
                if($injury->vehicle->user->created_at->lte( $date_event )) {
                    $driver_info = $injury->vehicle->user->name . ' ' . $injury->vehicle->user->surname . ', tel: ' . $injury->vehicle->user->phone . ', mail: ' . $injury->vehicle->user->email;
                }else{
                    $latest_user = null;
                    foreach($injury->vehicle->users as $user){
                        if($user->created_at->lte($date_event))
                        {
                            $latest_user = $user;
                        }else{
                            break;
                        }
                    }
                    if(!is_null($latest_user)){
                        $driver_info = $latest_user->name . ' ' . $latest_user->surname . ', tel: ' . $latest_user->phone . ', mail: ' . $latest_user->email;
                    }
                }
            }
        }

        return $driver_info;
    }

    private function getInjuryType($injury)
    {
        if ($injury->type_incident_id == 12 || in_array($injury->step, [40,41,42,43,44,45,46]) ||
            (
                ($injury->step == '-7' && in_array($injury->prev_step, [40,41,42,43,44,45,46]))  ||
                ($injury->step == '-7' && $injury->theft_status_id > 0) ||
                ($injury->step == '-7' && in_array('118', $injury->historyEntries->lists('history_type_id')) )
            )
        )
            return 'KRAD';
        elseif (in_array($injury->step, [30,31,32,33,34,35,36,37]) ||
            (
                ($injury->step == '-7' && in_array($injury->prev_step, [30,31,32,33,34,35,36,37]))  ||
                ($injury->step == '-7' && $injury->total_status_id > 0) ||
                ($injury->step == '-7' && in_array('30', $injury->historyEntries->lists('history_type_id')) )
            )
        )
            return 'CALK';
        elseif($injury->type_incident_id == '4')
            return 'PARK';
        else
            return 'KOMU';
    }

    private function getInjuryValue($injury)
    {
        $value = 0;

        foreach($injury->compensations as $compensation)
        {
            $value += $compensation->compensation;
        }

        return $value;
    }

}