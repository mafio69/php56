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
use Symfony\Component\HttpFoundation\StreamedResponse;

class RedirectionReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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
        \DB::disableQueryLog();

        $response = new StreamedResponse(function(){
            // Open output stream
            $handle = fopen('php://output', 'w');

            fputs( $handle, "\xEF\xBB\xBF" );


            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            fputcsv($handle,array('Raport przekierowań '.$this->params['date_from'].' - '.$this->params['date_to']), ';');
            fputcsv($handle,array(), ';');

            fputcsv($handle, $this->generateTheads(), ';');


            $injuries = Injury::where('active', '=', '0')
                ->where('step' , '!=' , '-10')
                ->whereHas('documents', function($query) use($date_from, $date_to){
                    $query->whereActive(0)->whereType(3)->where(function($query){
                        $query->whereCategory(6)->orWhere('category', 49)->orWhere('category', 60)->orWhere('category', 52);
                    })->whereBetween('created_at', array($date_from, $date_to));
                })
                ->with('user', 'vehicle', 'vehicle.owner','vehicle.owner.nip', 'vehicle.insurance_company',  'client', 'client.registryVoivodeship', 'branch', 'branch.voivodeship', 'branch.company', 'branch.company.groups', 'type_incident', 'injuries_type',
                    'invoices', 'totalStatus', 'theftStatus', 'compensations', 'historyEntries', 'status', 'status.injuryGroup', 'leader')
                ->with(['estimates'=>function($query){
                    $query->where('report',1)->orderBy('created_at','desc');
                }])->get();

            $filesA = InjuryFiles::whereActive(0)->whereType(3)->where(function($query){
                $query->whereCategory(6)->orWhere('category', 49)->orWhere('category', 60)->orWhere('category', 52);
            })->with('user')->whereBetween('created_at', array($date_from, $date_to))->whereIn('injury_id', $injuries->lists('id'))->get();

            $filesB = InjuryFiles::whereType(3)->where(function($query){
                $query->whereIn('category', [60,52,72,68,49,6,26,27,3,2,7,31,32]);
            })->with('user')->whereIn('injury_id', $injuries->lists('id'))->orderBy('created_at', 'desc')->get();


            $filesInjuryA = array();
            foreach ($filesA as $file) {
                if(!isset($filesInjuryA[$file->injury_id]))
                    $filesInjuryA[$file->injury_id] = $file;
            }

            foreach ($injuries as $k => $injury) {

                $processingType = $this->processingType($injury);
                $injuryType = $this->injuryType($injury);
                $serviceType = $this->serviceType($injury);
                $invoices = $this->invoices($injury);
                $ct_invoices = count($invoices);
                $value_of_invoices = $this->value_of_invoices($invoices);
                $sumCompensation = $this->calculateCompensation($injury->compensations);
                $injury_step = $this->injuryStep($injury);
                $injury_step_repair = $this->injuryStepRepair($injury);
                $value_of_estimate = $this->valueOfEstimate($injury);
                $person_generated = $this->personGenerated($injury,$filesB);

                fputcsv($handle, array(
                    ($injury->client_id == 0) ? '---' : $injury->client->name,
                    ($injury->client_id == 0) ? '---' : $injury->client->registry_city,
                    ($injury->client_id == 0 || ! $injury->client->registryVoivodeship) ? '---' : $injury->client->registryVoivodeship->name,
                    $injury->vehicle->nr_contract,
                    $injury->vehicle->contract_status,
                    checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) . ' ' . checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model),
                    $injury->vehicle->registration,
                    ($injury->vehicle->cfm == 1) ? 'tak' : 'nie',
                    $injury->vehicle->owner->name,
                    $injury->case_nr,
                    substr($injury->created_at, 0, -3),
                    $injury->date_event,
                    $injury->date_end ? substr($injury->date_end, 0, -3) : '',
                    $injury->status->name,
                    $processingType,
                    $injuryType,
                    $injury->user->name,
                    $injury->leader ? $injury->leader->name : '',
                    $injury->injury_nr,
                    ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                    ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->company->name : '---',
                    ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->company->nip : '---',
                    ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->short_name : '---' ,
                    $serviceType,
                    ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->code . ' ' . $injury->branch->city . ', ' . $injury->branch->street : '---',
                    ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0 && $injury->branch->voivodeship) ? $injury->branch->voivodeship->name : '---',
                    ($injury->task_authorization == 1) ? 'tak' : 'nie',
                    $ct_invoices,
                    $value_of_invoices,
                    $sumCompensation,
                    ($injury->type_incident_id != 0 && $injury->type_incident_id != NULL) ? $injury->type_incident->name : '---',
                    $injury->injuries_type->name,
                    (isset($filesInjuryA[$injury->id])) ? 'tak' : 'nie',
                    (isset($filesInjuryA[$injury->id])) ? substr($filesInjuryA[$injury->id]->created_at, 0, -3) : '---',
                    ($injury->vehicle->owner->nip->count() > 0) ?  $injury->vehicle->owner->nip->first()->value : '',
                    $injury_step,
                    $injury_step_repair,
                    $value_of_estimate,
                    $person_generated,
                    $injury->vehicle->owner->old_nip
                ), ';');
            }

        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'.csv"',
        ]);

        return $response;

        Excel::create($this->filename, function($excel) {

            $excel->sheet('Export', function($sheet) {
                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $sheet->appendRow(array('Raport przekierowań '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                $injuries = Injury::where('active', '=', '0')
                    ->whereHas('documents', function($query) use($date_from, $date_to){
                        $query->whereActive(0)->whereType(3)->where(function($query){
                            $query->whereCategory(6)->orWhere('category', 49)->orWhere('category', 60)->orWhere('category', 52);
                        })->whereBetween('created_at', array($date_from, $date_to));
                    })
                    ->with('user', 'vehicle', 'vehicle.owner','vehicle.owner.nip', 'vehicle.insurance_company',  'client', 'client.registryVoivodeship', 'branch', 'branch.voivodeship', 'branch.company', 'branch.company.groups', 'type_incident', 'injuries_type',
                            'invoices', 'totalStatus', 'theftStatus', 'compensations', 'historyEntries', 'status', 'status.injuryGroup', 'leader')
                            ->with(['estimates'=>function($query){
                              $query->where('report',1)->orderBy('created_at','desc');
                            }])->get();

                $filesA = InjuryFiles::whereActive(0)->whereType(3)->where(function($query){
                    $query->whereCategory(6)->orWhere('category', 49)->orWhere('category', 60)->orWhere('category', 52);
                })->with('user')->whereBetween('created_at', array($date_from, $date_to))->whereIn('injury_id', $injuries->lists('id'))->get();

                $filesB = InjuryFiles::whereType(3)->where(function($query){
                    $query->whereIn('category', [60,52,72,68,49,6,26,27,3,2,7,31,32]);
                })->with('user')->whereIn('injury_id', $injuries->lists('id'))->orderBy('created_at')->get();


                $filesInjuryA = array();
                foreach ($filesA as $file) {
                    if(!isset($filesInjuryA[$file->injury_id]))
                        $filesInjuryA[$file->injury_id] = $file;
                }

                foreach ($injuries as $k => $injury) {

                    $processingType = $this->processingType($injury);
                    $injuryType = $this->injuryType($injury);
                    $serviceType = $this->serviceType($injury);
                    $invoices = $this->invoices($injury);
                    $ct_invoices = count($invoices);
                    $value_of_invoices = $this->value_of_invoices($invoices);
                    $sumCompensation = $this->calculateCompensation($injury->compensations);
                    $injury_step = $this->injuryStep($injury);
                    $injury_step_repair = $this->injuryStepRepair($injury);
                    $value_of_estimate = $this->valueOfEstimate($injury);
                    $person_generated = $this->personGenerated($injury,$filesB);

                    $sheet->appendRow(array(
                        ($injury->client_id == 0) ? '---' : $injury->client->name,
                        ($injury->client_id == 0 || ! $injury->client->registryVoivodeship) ? '---' : $injury->client->registryVoivodeship->name,
                        $injury->vehicle->nr_contract,
                        $injury->vehicle->contract_status,
                        checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) . ' ' . checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model),
                        $injury->vehicle->registration,
                        ($injury->vehicle->cfm == 1) ? 'tak' : 'nie',
                        $injury->vehicle->owner->name,
                        $injury->case_nr,
                        substr($injury->created_at, 0, -3),
                        $injury->date_event,
                        $injury->date_end ? substr($injury->date_end, 0, -3) : '',
                        $injury->status->name,
                        $processingType,
                        $injuryType,
                        $injury->user->name,
                        $injury->leader ? $injury->leader->name : '',
                        $injury->injury_nr,
                        ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                        ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->company->name : '---',
                        ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->company->nip : '---',
                        ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->short_name : '---' ,
                        $serviceType,
                        ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->code . ' ' . $injury->branch->city . ', ' . $injury->branch->street : '---',
                        ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0 && $injury->branch->voivodeship) ? $injury->branch->voivodeship->name : '---',
                        ($injury->task_authorization == 1) ? 'tak' : 'nie',
                        $ct_invoices,
                        $value_of_invoices,
                        $sumCompensation,
                        ($injury->type_incident_id != 0 && $injury->type_incident_id != NULL) ? $injury->type_incident->name : '---',
                        $injury->injuries_type->name,
                        (isset($filesInjuryA[$injury->id])) ? 'tak' : 'nie',
                        (isset($filesInjuryA[$injury->id])) ? substr($filesInjuryA[$injury->id]->created_at, 0, -3) : '---',
                        ($injury->vehicle->owner->nip->count() > 0) ?  $injury->vehicle->owner->nip->first()->value : '',
                        $injury_step,
                        $injury_step_repair,
                        $value_of_estimate,
                        $person_generated,
	                    $injury->vehicle->owner->old_nip
                    ));
                }
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
        elseif(in_array($injury->step, ['10', '15', '17', '19', '20' ]))
            return 'częściowa';
        elseif(in_array($injury->step, ['0', '-10']))
        {
            if($injury->if_theft == 1)
                return 'kradzież';
            else
                return 'częściowa';
        }else{
            if(in_array('30', $injury->historyEntries->lists('history_type_id')) )
                return 'całkowita';
            else
                return 'kradzież';
        }
        */
//        if ($injury->step == '-5' ||
//                (
//                    ($injury->step == '-7' && $injury->prev_step == '-5')  ||
//                    ($injury->step == '-7' && $injury->total_status_id > 0) ||
//                    ($injury->step == '-7' && in_array('30', $injury->historyEntries->lists('history_type_id')) )
//                )
//            )
//            return 'całkowita';
//        elseif (in_array($injury->step, [40,41,42,43,44,45,46]) ||
//                    (
//                        ($injury->step == '-7' && in_array($injury->prev_step, [40,41,42,43,44,45,46]))  ||
//                        ($injury->step == '-7' && $injury->theft_status_id > 0) ||
//                        ($injury->step == '-7' && in_array('118', $injury->historyEntries->lists('history_type_id')) )
//                    )
//                )
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

    private function valueOfEstimate($injury){
      $estimate = $injury->estimates->first();
      if($estimate&&$estimate->net)
        return $estimate->net;
      else
        return 0;
    }


    public function generateTheads()
    {
       return array(
            'Klient',
            'Klient miasto',
            'Klient - województwo',
            'Umowa',
            'Status Umowy',
            'Marka Model',
            'Numer Rejestracyjny',
            'CFM',
            'Właściciel pojazdu',
            'Numer Szkody wewnętrzny',
            'Data Zgłoszenia',
            'Data Zdarzenia',
            'Data Zakończenia Szkody',
            'Status Szkody',
            'Etap procesowania',
            'Typ Szkody',
            'Przyjmujący Szkodę',
            'Prowadzący Szkodę',
            'Numer Szkody Towarzystwa',
            'TU',
            'Serwis',
            'NIP serwisu',
            'Warsztat',
            'Grupa serwisu',
            'Adres Serwisu',
            'Serwis - województwo',
            'Wystawiono upoważnienie',
            'Liczba Faktur Kosztowych',
            'Wartość Netto z Faktury',
            'Wysokość wypłaconego odszkodowania',
            'Rodzaj Zdarzenia',
            'Sposób Rozliczenia',
            'Wysłano zlecenie do serwisu',
            'Data wygenerowania zlecenia',
            'NIP właściciela pojazdu',
            'Etap Sprawy',
            'Etap Naprawy',
            'Wartość Netto z Kosztorysu',
            'Osoba generująca zlecenie',
	        'Uprzedni NIP właściciela'
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

    private function getDateAuthorization($injury)
    {
        if($injury->generated_authorizations->count() > 0)
        {
            return substr($injury->generated_authorizations->first()->created_at,0,-3);
        }

        return '---';
    }

    private function getDateTotalStatus($injury)
    {
        if($injury->date_total_status->count() > 0)
        {
            return substr($injury->date_total_status->first()->created_at,0,-3);
        }

        return '---';
    }

    private function getDateFinishedStatus($injury)
    {
        if($injury->date_finished_status->count() > 0)
        {
            return substr($injury->date_finished_status->first()->created_at,0,-3);
        }

        return '---';
    }

    private function getDateInvoice($injury)
    {
        if($injury->uploaded_invoices->count() > 0)
        {
            return substr($injury->uploaded_invoices->first()->created_at,0,-3);
        }

        return '---';
    }

    private function injuryStep($injury){
      if(! in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7'] ))
                if($injury->stepStage)
                    return $injury->stepStage->name;
      return '---';
    }

    private function injuryStepRepair($injury){
      if($injury->currentRepairStage){
          if($injury->currentRepairStage->value == 1)
              return $injury->currentRepairStage->stage->checked_description;
          else
              return $injury->currentRepairStage->stage->unchecked_description;
      }
      else
          return 'w oczekiwaniu na potwierdzenie przyjęcia zlecenia';
    }

    private function personGenerated($injury,$files){
      $temp_file = $files->filter(function($item) use ($injury)
      {
          return $item->injury_id==$injury->id&&$item->category==60;
      })->first();
      if($temp_file){
        if($temp_file->user){
          return $temp_file->user->name;
        }
      }
      $temp_file = $files->filter(function($item) use ($injury)
      {
          return $item->injury_id==$injury->id&&$item->category==52;
      })->first();
      if($temp_file){
        if($temp_file->user){
          return $temp_file->user->name;
        }
      }
      $temp_file = $files->filter(function($item) use ($injury)
      {
          return $item->injury_id==$injury->id&&$item->category==49;
      })->first();
      if($temp_file){
        if($temp_file->user){
          return $temp_file->user->name;
        }
      }
      $temp_file = $files->filter(function($item) use ($injury)
      {
          return $item->injury_id==$injury->id&&$item->category==6;
      })->first();
      if($temp_file){
        if($temp_file->user){
          return $temp_file->user->name;
        }
      }
      return "---";

    }

}
