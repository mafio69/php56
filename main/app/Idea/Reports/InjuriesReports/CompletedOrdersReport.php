<?php
namespace Idea\Reports\InjuriesReports;

use Carbon\Carbon;
use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use Injury;
use InjurySteps;
use InjuryStepHistory;
use InjuryFiles;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompletedOrdersReport extends BaseReport implements ReportsInterface, ReportsCsvInterface
{

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
        set_time_limit(5000);
        \DB::disableQueryLog();

        $response = new StreamedResponse(function() {
            // Open output stream
            $handle = fopen('php://output', 'w');

            fputs( $handle, "\xEF\xBB\xBF" );

            $headers = [
                'Umowa',
                'Marka Model',
                'Numer Rejestracyjny',
                'VIN',
                'Nazwa klienta',
                'Właściciel pojazdu',
                'Numer Szkody wewnętrzny',
                'Aktualny status Szkody',
                'Typ Szkody',
                'Rodzaj Szkody',
                'Nr szkody TU',
                'GAP (TAK/NIE)',
                'Data Zakończenia Szkody CZĘŚCIOWEJ',
                'Data Zakończenia Szkody CAŁKOWITEJ',
                'Data Zakończenia Szkody KRADZIEŻOWEJ',
                'Status na dzień zakończenia Szkody'
            ];

            fputcsv($handle, $headers, ';');

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            Injury::where('active', '=', '0')
                ->whereBetween('date_end', array($date_from, $date_to))
                ->where('skip_in_ending_report', 0)
                ->where('step' , '!=' , '-10')
                ->chunk(500, function($injuries) use (&$handle){
                    $injuries->load('vehicle', 'vehicle.owner', 'status.injuryGroup', 'injuries_type', 'client');
                    foreach($injuries as $injury)
                    {
                        $row = [
                            $injury->vehicle->nr_contract,
                            checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) . ' ' . checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model),
                            $injury->vehicle->registration,

                            ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin,
                            $injury->client ? $injury->client->name : '',

                            $injury->vehicle->owner->name,
                            $injury->case_nr,
                            $injury->status->name,
                            $injury->injuries_type->name,
                            $injury->status->injuryGroup->name,
                            $injury->injury_nr,
                            // \Config::get('definition.insurance_options_definition.'.$injury->vehicle->gap),
                            \Config::get('definition.insurance_options_definition.'.$injury->injuryPolicy->gap),
                            $injury->date_end_normal ? substr($injury->date_end_normal, 0, -3) : '',
                            $injury->date_end_total ? substr($injury->date_end_total, 0, -3) : '',
                            $injury->date_end_theft ? substr($injury->date_end_theft, 0, -3) : '',
                            $this->stepOnDate($injury->id, $injury->date_end),
                        ];

                        fputcsv($handle, $row, ';');
                    }
                });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.($this->filename).'.csv"',
        ]);

        return $response;
    }

    public function _generateReport()
    {
        set_time_limit(5000);
        \DB::disableQueryLog();

        Excel::create($this->filename, function($excel) {

            $excel->sheet('Export', function($sheet) {
                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');



                $sheet->appendRow(array('Raport zleceń zakończonych '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                Injury::where('active', '=', '0')
                    ->whereBetween('date_end', array($date_from, $date_to))
                    ->with('user', 'vehicle', 'vehicle.owner', 'vehicle.owner.nip', 'vehicle.insurance_company',  'client', 'client.registryVoivodeship', 'branch', 'branch.voivodeship', 'branch.company', 'branch.company.groups', 'driver', 'type_incident', 'injuries_type',
                            'invoices', 'totalStatus', 'theftStatus', 'compensations', 'generated_authorizations', 'uploaded_invoices', 'date_total_status', 'date_finished_status',
                            'historyEntries', 'status', 'status.injuryGroup', 'repairInformation')->with(['estimates'=>function($query){
                              $query->where('report',1)->orderBy('created_at','desc');
                            }])
                    ->chunk(200, function($injuries) use ($sheet){

                        $filesA = InjuryFiles::whereActive(0)->whereType(3)->where(function($query){
                            $query->whereIn('category', [6,49,60]);
                        })->with('user')->whereIn('injury_id', $injuries->lists('id'))->get();

                        $filesB = InjuryFiles::whereType(3)->where(function($query){
                            $query->whereIn('category', [60,52,72,68,49,6,26,27,3,2,7,31,32]);
                        })->with('user')->whereIn('injury_id', $injuries->lists('id'))->orderBy('created_at')->get();

                        $filesInjuryA = array();
                        foreach ($filesA as $file) {
                            if(!isset($filesInjuryA[$file->injury_id]))
                                $filesInjuryA[$file->injury_id] = $file;
                        }

                        $filesFeeA = InjuryFiles::whereActive(0)->whereType(3)->whereHas('document_type', function($query) {
                            $query->where('fee', 1);
                        })->where('if_fee', 1)->whereIn('injury_id', $injuries->lists('id'))->get();
                        $filesInjuryFeeA = array();
                        foreach ($filesFeeA as $file) {
                            if(!isset($filesInjuryFeeA[$file->injury_id]))
                                $filesInjuryFeeA[$file->injury_id] = $file;
                        }

	                    //$limit_date_01022018 = Carbon::createFromFormat('Y-m-d', '2018-02-01');

                        foreach ($injuries as $k => $injury) {
                            $processingType = $this->processingType($injury);
                            $injuryType = $this->injuryType($injury);
                            $serviceType = $this->serviceType($injury);
                            $fee = $this->fee($injury);
                            $invoices = $this->invoices($injury);
                            $ct_invoices = count($invoices);
                            $value_of_invoices = $this->value_of_invoices($invoices);
                            $sumCompensation = $this->calculateCompensation($injury->compensations);
                            $authorization = $this->getDateAuthorization($injury);
                            $date_total = $this->getDateTotalStatus($injury);
                            $date_finished_normal = $this->getDateFinishedStatus($injury);
                            $date_invoice = $this->getDateInvoice($injury);
                            $injury_step = $this->injuryStep($injury);
                            $il_repair_info = $this->ilRepairInfo($injury);
                            $value_of_estimate = $this->valueOfEstimate($injury);
                            $end_date =  $injury->date_end ? substr($injury->date_end, 0, -3) : '';
                            $person_generated = $this->personGenerated($injury,$filesB);

                            $sheet->appendRow(array(
                                ($injury->client_id == 0) ? '---' : $injury->client->name,
                                ($injury->client_id == 0 || ! $injury->client->registryVoivodeship) ? '---' : $injury->client->registryVoivodeship->name,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->contract_status,
                                checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand) . ' ' . checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model),
                                $injury->vehicle->registration,
                                ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin,
                                ($injury->vehicle->cfm == 1) ? 'tak' : 'nie',
                                $injury->vehicle->owner->name,
                                $injury->case_nr,
                                substr($injury->created_at, 0, -3),
                                $injury->date_event,
                                $end_date,
                                $injury->vehicle->mileage,
                                $injury->status->name,
                                $processingType,
                                $injuryType,
                                ($injury->driver_id != '') ? $injury->driver->surname . ' ' . $injury->driver->name . ' ' . $injury->driver->phone . ' ' . $injury->driver->email : '---',
                                $injury->user->name,
                                $injury->user->name,
                                $injury->injury_nr,
                                ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                                ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->short_name : '---' ,
                                $serviceType,
                                ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->code . ' ' . $injury->branch->city . ', ' . $injury->branch->street : '---',
                                ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0 && $injury->branch->voivodeship) ? $injury->branch->voivodeship->name : '---',
                                ($injury->task_authorization == 1) ? 'tak' : 'nie',
                                $fee,
                                (isset($filesInjuryFeeA[$injury->id])) ? 'tak' : 'nie',
                                $ct_invoices,
                                $value_of_invoices,
                                $sumCompensation,
                                ($injury->type_incident_id != 0 && $injury->type_incident_id != NULL) ? $injury->type_incident->name : '---',
                                $injury->injuries_type->name,
                                (isset($filesInjuryA[$injury->id])) ? 'tak' : 'nie',
                                (isset($filesInjuryA[$injury->id])) ? substr($filesInjuryA[$injury->id]->created_at, 0, -3) : '---',
                                $authorization,
                                $date_total,
                                $date_finished_normal,
                                $date_invoice,
                                ($injury->vehicle->owner->nip->count() > 0) ?  $injury->vehicle->owner->nip->first()->value : '',
                                $injury_step,
                                ($injury->reported_ic != 1) ? 'NIE' : 'TAK',
                                ($injury->if_il_repair == 1) ? 'TAK' : (($injury->if_il_repair == 0) ?  'NIE' : 'NIE USTALONO'),
                                $il_repair_info,
                                $value_of_estimate,
                                $person_generated,
	                            $injury->vehicle->owner->old_nip,
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
//        if (in_array($injury->step,[30,31,32,33,34,35,36,37]) ||
//            (
//                ($injury->step == '-7' && in_array($injury->prev_step,[30,31,32,33,34,35,36,37]))  ||
//                ($injury->step == '-7' && $injury->total_status_id > 0) ||
//                ($injury->step == '-7' && in_array('30', $injury->historyEntries->lists('history_type_id')) )
//            ))
//            return 'całkowita';
//        elseif (in_array($injury->step, [40,41,42,43,44,45,46]) ||
//            (
//                ($injury->step == '-7' && in_array($injury->prev_step, [40,41,42,43,44,45,46]))  ||
//                ($injury->step == '-7' && $injury->theft_status_id > 0) ||
//                ($injury->step == '-7' && in_array('118', $injury->historyEntries->lists('history_type_id')) )
//            ))
//            return 'kradzież';
//        elseif($injury->step == '-7')
//            return '---';
//        else
//            return 'częściowa';

        if(isset($this->groups[ $this->statuses[$injury->step]->injury_group_id ])) {
            if($injury->total_status_source == 1){
                return $this->groups[3];
            }
            return $this->groups[$this->statuses[$injury->step]->injury_group_id];
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
            'Klient - województwo',
            'Umowa',
            'Status Umowy',
            'Marka Model',
            'Numer Rejestracyjny',
            'VIN',
            'CFM',
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
            'TU',
            'Warsztat',
            'Grupa serwisu',
            'Adres Serwisu',
            'Serwis - województwo',
            'Wystawiono upoważnienie',
            'Czy naliczyć opłatę',
            'Czy naliczyć opłatę 2016',
            'Liczba Faktur Kosztowych',
            'Wartość Netto z Faktury',
            'Wysokość wypłaconego odszkodowania',
            'Rodzaj Zdarzenia',
            'Sposób Rozliczenia',
            'Wysłano zlecenie do serwisu',
            'Data wygenerowania zlecenia',
            'Data wydania upoważnienia',
            'Data zmiany statusu na szkoda całkowita',
            'Data zmiany statusu na zakończona w normalnym trybie',
            'Data podpięcia FV',
            'NIP właściciela pojazdu',
            'Etap Sprawy',
            'Zgłoszona do TU',
            'Naprawa w sieci IL',
            'Przyczyna naprawy poza siecią IL',
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

    private function ilRepairInfo($injury){
     $il_repair_info=$injury->repairInformation;
      if($il_repair_info){
        $info= $il_repair_info->name;
        if(isset($injury->il_repair_info_description))
          $info.= ' - '.$injury->il_repair_info_description;
        return $info;
      }
      return '---';
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
          return $item->injury_id==$injury->id&&$item->category==47;
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

    private function endDate($injury,$files){
      $end_date=null;
      $history=$injury->historyEntries->keyBy('history_type_id')->toArray();
      $helper = array(
        //15=>,
        16=>163,
        17=>115,
        19=>116,
        //21=>,
        23=>174,
        24=>173,
        25=>74,
        34=>180,
        35=>181,
        45=>178,
        44=>179,
        46=>194,
        37=>183,
      );
      if($injury->step&&in_array($injury->step,[15,16,17,19,23,24,25,34,35,45,44,46,37,21])){
          foreach($helper as $key => $item){
            if($injury->step==$key){
              if(isset($history[$item])){
                  $end_date=substr($history[$item]['created_at'],0,-3);
                  return $end_date;
              }
            }
          }
          if(isset($history[140])){
              $end_date=substr($history[140]['created_at'],0,-3);
              return $end_date;
          }
          if(isset($history[114])){
              $end_date=substr($history[114]['created_at'],0,-3);
              return $end_date;
          }

          if(!$end_date&&($injury->step==23||$injury->step==15)){
            $temp_file = $files->filter(function($item) use ($injury)
            {
                return $item->injury_id==$injury->id&&$item->active==0&&(in_array($item->category,[72,68,26,27,3,2,7,31,32]));
            })->first();
            if($temp_file){
              if($temp_file->created_at){
                $end_date=substr($temp_file->created_at,0,-3);
                return $end_date;
              }
            }
          }
      }
      if(!$end_date){
        if(!is_null($injury->date_end) && in_array($injury->step, [-7,15,16,17,19,21,23,24,25,26,34,35,37,38,44,45,46])){
          $end_date = substr($injury->date_end, 0, -3);
        }else{
          $end_date = '---';
        }
      }
      return $end_date;
    }

    private function stepOnDate($injury_id, $date){
        $history_row = InjuryStepHistory::where('injury_id', $injury_id)->where('updated_at', '<=', $date)->orderBy('updated_at', 'desc')->first();
        if($history_row) {
            $step = InjurySteps::find($history_row->next_step_id);
            return $step->name;
        }
    }

}
