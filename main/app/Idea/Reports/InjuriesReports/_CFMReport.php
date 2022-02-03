<?php
namespace Idea\Reports\InjuriesReports;

use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use Injury;
use InjuryFiles;

class CFMReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

    private $params;
    private $filename;

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;

        foreach(\DB::table('injury_groups')->get() as $item){
            $this->groups[$item->id] = $item->name;
        }
    }

    public function generateReport()
    {
        set_time_limit(500);

        Excel::create($this->filename, function($excel) {

            $excel->sheet('Raport CFM', function($sheet) {

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $filesA = InjuryFiles::whereActive(0)->whereType(3)->whereCategory(6)->get();
                $filesInjuryA = array();
                foreach ($filesA as $file) {
                    $filesInjuryA[$file->injury_id] = $file;
                }
                $sheet->appendRow(array('Raport CFM '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                Injury::where('active', '=', '0')
                    ->vehicleExists('cfm', 1, 'where')
                    ->whereBetween('created_at', array($date_from, $date_to))
                    ->with('user', 'vehicle', 'client', 'branch', 'branch.company', 'branch.company.groups', 'driver', 'type_incident',
                            'injuries_type', 'invoices', 'totalStatus', 'theftStatus', 'compensations', 'documents', 'status',  'historyEntries', 'status.injuryGroup')
                    ->chunk(100, function($injuries) use (&$sheet, $filesInjuryA){
                        foreach ($injuries as $k => $injury) {

                            $processingType = $this->processingType($injury);
                            $injuryType = $this->injuryType($injury);
                            $serviceType = $this->serviceType($injury);
                            $fee = $this->fee($injury);
                            $invoices = $this->invoices($injury);
                            $ct_invoices = count($invoices);
                            $value_of_invoices = $this->value_of_invoices($invoices);
                            $sumCompensation = $this->calculateCompensation($injury->compensations);

                            $sheet->appendRow(array(
                                ($injury->client_id == 0) ? '---' : $injury->client->name,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->contract_status,
                                checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) . ' ' . checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model),
                                $injury->vehicle->registration,
                                $injury->vehicle->owner->name,
                                $injury->case_nr,
                                substr($injury->created_at, 0, -3),
                                $injury->date_event,
                                (is_null($injury->date_end) ) ? '---' : substr($injury->date_end, 0, -3),
                                $injury->vehicle->mileage,
                                $injury->status->name,
                                $processingType,
                                $injuryType,
                                ($injury->driver_id != '') ? $injury->driver->surname . ' ' . $injury->driver->name . ' ' . $injury->driver->phone . ' ' . $injury->driver->email : '---',
                                $injury->user->name,
                                $injury->user->name,
                                $injury->injury_nr,
                                ($injury->branch_id == 0 || $injury->branch_id == '-1') ? '---' : $injury->branch->short_name,
                                $serviceType,
                                ($injury->branch_id != 0 && $injury->branch_id != '-1') ? $injury->branch->code . ' ' . $injury->branch->city . ', ' . $injury->branch->street : '---',
                                ($injury->task_authorization == 1) ? 'tak' : 'nie',
                                $fee,
                                $ct_invoices,
                                $value_of_invoices,
                                $sumCompensation,
                                ($injury->type_incident_id != 0 && $injury->type_incident_id != NULL) ? $injury->type_incident->name : '---',
                                $injury->injuries_type->name,
                                (isset($filesInjuryA[$injury->id])) ? 'tak' : 'nie',
                                (isset($filesInjuryA[$injury->id])) ? substr($filesInjuryA[$injury->id]->created_at, 0, -3) : '---',
                                ($injury->vehicle->cfm == 1) ? 'tak' : 'nie',
                            ));
                        }
                    });
            });

        })->export('xls');
    }

    private function processingType($injury)
    {
        if ($injury->totalStatus)
            return $injury->totalStatus->name;
        elseif ($injury->theftStatus)
            return $injury->theftStatus->name;

        return '---';
    }

    private function injuryType($injury)
    {
        /*
        if ($injury->step == '-5')
            return 'całkowita';
        elseif ($injury->step == '-3')
            return 'kradzież';

        return 'częściowa';
        */
//        if (in_array($injury->step, [30,31,32,33,34,35,36,37]) ||
//            (
//                ($injury->step == '-7' && in_array($injury->prev_step, [30,31,32,33,34,35,36,37]))  ||
//                ($injury->step == '-7' && $injury->total_status_id > 0) ||
//                ($injury->step == '-7' && in_array('30', $injury->historyEntries->lists('history_type_id')) )
//            )
//        )
//            return 'całkowita';
//        elseif (in_array($injury->step, [40,41,42,43,44,45,46]) ||
//            (
//                ($injury->step == '-7' && in_array($injury->prev_step, [40,41,42,43,44,45,46]))  ||
//                ($injury->step == '-7' && $injury->theft_status_id > 0) ||
//                ($injury->step == '-7' && in_array('118', $injury->historyEntries->lists('history_type_id')) )
//            )
//        )
//            return 'kradzież';
//        elseif($injury->step == '0' && $injury->if_theft == 1)
//            return 'kradzież';
//        elseif($injury->step == '-7')
//            return '---';
//        else
//            return 'częściowa';

        if( $injury->status->injuryGroup ){
            return $injury->status->injuryGroup->name;
        }

        if($injury->type_incident_id == 12){
            return $this->groups[3];
        }

        return $this->groups[2];
    }



    private function fee($injury)
    {
        if ($injury->task_authorization == 1) {
            if ($injury->issue_fee == '-1')
                return 'nie';
            else
                return 'tak';
        }

        return '---';
    }

    private function invoices($injury)
    {
        $invoicesA = array();
        foreach ($injury->invoices as $k => $invoice) {
            if ($invoice->active == 0) {
                if ($invoice->parent_id == 0)
                    $invoicesA[$invoice->id] = $invoice;
                else
                    $invoicesA[$invoice->parent_id] = $invoice;
            }
        }

        return $invoicesA;
    }

    private function value_of_invoices($invoices)
    {
        if (count($invoices) == 0) {
            return 0;
        } else {
            $sum = 0;
            foreach ($invoices as $k => $v) {
                $sum += $v->netto;
            }
        }

        return $sum;
    }

    public function generateTheads()
    {
        return array(
            'Klient',
            'Umowa',
            'Status Umowy',
            'Marka Model',
            'Numer Rejestracyjny',
            'Właściciel pojazdu',
            'Numer Szkody wewnętrzny',
            'Data Zgłoszenia',
            'Data Zdarzenia',
            'Data Zakończenia Szkody',
            'Przebieg',
            'Status Szkody',
            'Etap procesowania',
            'Typ Szkody',
            'Kierowca Pojazdu',
            'Przyjmujący Szkodę',
            'Prowadzący Szkodę',
            'Numer Szkody Towarzystwa',
            'Warsztat',
            'Serwis Idea/inny',
            'Adres Serwisu',
            'Wystawiono upoważnienie',
            'Czy naliczyć opłatę',
            'Liczba Faktur Kosztowych',
            'Wartość Netto z Faktury',
            'Wysokość wypłaconego odszkodowania',
            'Rodzaj Zdarzenia',
            'Sposób Rozliczenia',
            'Wysłano zlecenie do serwisu',
            'Data wygenerowania zlecenia',
            'CFM',
            'wpłynęła decyzja wypłaty odszkodowania',
            'ilość decyzji',
            'wartość decyzji',
            'wpłynęła decyzja wypłaty na IL',
            'wartość decyzji na IL'
        );
    }

    private function calculateCompensation($compensations)
    {
        $sum = 0;

        if(! $compensations->isEmpty())
        {
            foreach($compensations as $compensation)
            {
                if(!is_null($compensation->compensation)) {
                    if ($compensation->injury_compensation_decision_type_id == 7)
                        $compensation->compensation = abs($compensation->compensation) * -1;

                    $sum += $compensation->compensation;
                }
            }
        }
        return $sum;
    }
}