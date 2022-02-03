<?php
namespace Idea\Reports\InjuriesReports;

use Carbon\Carbon;
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
                    ->where('step' , '!=' , '-10')
                    ->vehicleExists('cfm', 1, 'where')
                    ->whereBetween('created_at', array($date_from, $date_to))
                    ->with('user', 'vehicle', 'client', 'branch', 'branch.company', 'branch.company.groups',  'type_incident',
                            'injuries_type', 'invoices', 'totalStatus', 'theftStatus', 'compensations', 'documents', 'status',  'historyEntries', 'status.injuryGroup')
                    ->chunk(100, function($injuries) use (&$sheet, $filesInjuryA){
	                    $limit_date_01022018 = Carbon::createFromFormat('Y-m-d', '2018-02-01');

	                    foreach ($injuries as $k => $injury) {

                            $injuryType = $this->injuryType($injury);
                            $serviceType = $this->serviceType($injury);
                            $invoices = $this->invoices($injury);
                            $ct_invoices = count($invoices);
                            $value_of_invoices = $this->value_of_invoices($invoices);
                            $date_of_invoice = $this->date_of_invoice($invoices);
                            $sumCompensation = $this->calculateCompensation($injury->compensations);
                            $if_commission = $this->checkIfCommission($invoices);

                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->date_event,
                                $injury->injury_nr,
                                $injury->vehicle->insurance_company()->first()->name,
                                $injury->injuries_type->name,
                                $injuryType,
                                ($injury->client_id == 0) ? '---' : $injury->client->name,
                                ($injury->client_id == 0) ? '---' : $injury->client->NIP,
                                ($injury->client_id == 0) ? '---' : $injury->client->registry_post.' '.$injury->client->registry_city.' '.$injury->client->registry_street,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->registration,
                                ($injury->type_incident_id != 0 && $injury->type_incident_id != NULL) ? $injury->type_incident->name : '---',
                                $injury->notifier_name.' '.$injury->notifier_surname.' '.$injury->notifier_phone.' '.$injury->notifier_email,
                                ($injury->branch_id == 0 || $injury->branch_id == '-1') ? '---' : $injury->branch->short_name,
                                ($injury->branch_id != 0 && $injury->branch_id != '-1') ? $injury->branch->code . ' ' . $injury->branch->city . ', ' . $injury->branch->street : '---',
                                $serviceType,
                                ($ct_invoices > 0) ? 'TAK' : 'NIE',
                                $value_of_invoices,
                                $date_of_invoice,
                                $sumCompensation,
                                $if_commission,
                                $injury->vehicle->owner->name,
	                            $injury->vehicle->owner->old_nip,
                                $injury->user->name,
                                $injury->status->name
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

    private function date_of_invoice($invoices)
    {
        if (count($invoices) == 0) {
            return '';
        } else {
            $date = '';
            foreach ($invoices as $k => $v) {
                if($v->invoice_date != '0000-00-00')
                    $date = $v->invoice_date;
                else
                    $date = '';
            }
        }

        return $date;
    }

    public function generateTheads()
    {
        return array(
            'Nr sprawy',
            'Data szkody (data zdarzenia)',
            'Nr szkody ZU',
            'Nazwa ZU',
            'Sposób rozliczenia (AC,OC, itd.)',
            'Typ szkody (częściowa, całkowita,…)',
            'Dane klienta',
            'Dane klienta NIP',
            'Dane klienta adres rejestrowy',
            'Nr umowy',
            'Nr rejestracyjny',
            'Rodzaj zdarzenia',
            'Dane zgłaszającego',
            'Serwis',
            'Adres Serwisu',
            'Typ warsztatu',
            'Czy wprowadzona FV',
            'Kwota FV netto',
            'Data faktury',
            'Kwota decyzji',
            'Czy prowizja',
            'Właściciel',
            'Uprzedni NIP właściciela',
            'Wprowadzający',
            'Status szkody'
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

    private function checkIfCommission($invoices)
    {
        if (count($invoices) == 0) {
            return 'NIE';
        } else {
            $if = 'NIE';
            foreach ($invoices as $k => $v) {
                if($v->commission == 1)
                    $if = 'TAK';
            }
        }

        return $if;
    }
}