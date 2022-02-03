<?php

namespace Idea\Reports\CustomReports;


use Carbon\Carbon;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportTheftTotal extends BaseReport implements ReportsInterface{

    private $params;
    private $filename;
    private $reportType;
    private $now;

    function __construct($reportType, $filename, $params = array())
    {
        $this->reportType = $reportType;
        $this->params = $params;
        $this->filename = $filename;
        $this->now = Carbon::now();
    }

    public function generateReport()
    {
        set_time_limit(500);
        \DB::disableQueryLog();
        \Debugbar::disable();
        $response = new StreamedResponse(function(){
            $handle = fopen('php://output', 'w');

            fputs( $handle, "\xEF\xBB\xBF" );
            fputcsv($handle, $this->generateTheads());

            \Injury::whereIn('step', array(30,31,32,33,34,35,36,37,40,41,42,43,44,45,46,47, '-7'))->whereActive(0)
                ->chunk(200, function($injuries) use ($handle) {
                    $injuries->load(['documents' => function($query)
                    {
                        $query->where('type', 3)->where('category', 75);
                    },
                        'client', 'vehicle', 'vehicle.insurance_company', 'vehicle.owner', 'chat', 'chat.messages',
                        'user', 'status', 'leader', 'theftStatus', 'totalStatus', 'theft', 'theft.acceptations', 'totalRepair',
                        'wreck', 'totalStatusesHistory', 'theftStatusesHistory', 'historyEntries','compensations','wreck','compensations','injuries_type',
                        'injuryGap'
                        ]);
                    $limit_date_01022018 = Carbon::createFromFormat('Y-m-d', '2018-02-01');
                    foreach ($injuries as $k => $injury) {

                        fputcsv($handle, array(
                            ($injury->client) ? $injury->client->name : '---',
                            ($injury->vehicle) ? $injury->vehicle->nr_contract : '---',
                            ($injury->vehicle) ? $injury->vehicle->owner->name : '---',
                            ( $injury->vehicle ) ? $injury->vehicle->owner->old_nip : '',
                            substr($injury->created_at, 0, -3),
                            $injury->date_event,
                            $this->processingType($injury),
                            $this->injuryType($injury),
                            $injury->injury_nr,
                            ($injury->task_authorization == 1) ? 'tak' : 'nie',
                            $this->getLastActionDate($injury),
                            $injury->status->name,
                            $injury->date_end ? substr($injury->date_end, 0, -3) : '',
                            $injury->user->name,
                            $this->days($injury),
                            $this->daysTotal($injury),

                            ($injury->vehicle) ? $injury->vehicle->registration : '---',
                            $injury->case_nr,
                            ($injury->leader) ? $injury->leader->name : '---',
                            ($injury->leader) ? substr($injury->leader_assign_date, 0, -3) : '---',
                            $this->daysOnCurrentStep($injury),

                            ($injury->documents->count() > 0) ? 'TAK' : 'NIE',
                            ($injury->documents->count() > 0) ? $injury->documents->first()->created_at->format('Y-m-d h:i') : '',

                            ($injury->wreck) ? $injury->wreck->pro_forma_number : '',
                            ($injury->wreck) ? $injury->wreck->contractor_code : '',
                            ($injury->wreck) ? $injury->wreck->pro_forma_value : '',
                            ($injury->wreck && $injury->wreck->invoice_request_confirm != '0000-00-00') ? $injury->wreck->invoice_request_confirm : '',
                            $this->isGap($injury),
                            $this->value_compensation_real($injury),
                            $this->value_compensation_real_gap($injury),
                            $this->gap_forecast($injury),

                            ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                            ($injury->wreck) ? $injury->wreck->value_undamaged : 0,
                            ($injury->wreck) ? $injury->wreck->value_repurchase : 0,
                            $this->findDateCompensation($injury),
                            $injury->injuries_type->name,
                            $this->scrapped($injury),
                        ));
                    }
            });

            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$this->filename.'.csv"',
            ]);

        return $response;
        /*return \Excel::create($this->filename, function($excel) {

            $excel->sheet('Export', function($sheet) {

                $sheet->appendRow(array('Raport szkód całkowitych i kradzieżowych '));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                \Injury::whereIn('step', array(30,31,32,33,34,35,36,37,40,41,42,43,44,45,46))->whereActive(0)
                    ->with('client', 'vehicle', 'vehicle.owner', 'chat', 'chat.messages',
                        'user', 'status', 'leader', 'theftStatus', 'totalStatus', 'theft', 'theft.acceptations', 'totalRepair',
                        'wreck', 'totalStatusesHistory', 'theftStatusesHistory', 'historyEntries')
                    ->chunk(200, function($injuries) use ($sheet) {
                        $injuries->load(['documents' => function($query)
                        {
                            $query->where('type', 3)->where('category', 75);
                        }]);
                        foreach ($injuries as $k => $injury) {
                            $end_date=null;
                            $history=$injury->historyEntries->keyBy('history_type_id')->toArray();
                            if($injury->step){
                              if($injury->step==34){
                                if(isset($history[180])){
                                  $end_date=substr($history[180]['created_at'],0,-3);
                                }elseif(isset($history[140])){
                                    $end_date=substr($history[140]['created_at'],0,-3);
                                }
                              }
                              elseif($injury->step==35){
                                if(isset($history[181])){
                                  $end_date=substr($history[181]['created_at'],0,-3);
                                }elseif(isset($history[140])){
                                    $end_date=substr($history[140]['created_at'],0,-3);
                                }
                              }
                              elseif($injury->step==45){
                                if(isset($history[178])){
                                  $end_date=substr($history[178]['created_at'],0,-3);
                                }elseif(isset($history[140])){
                                    $end_date=substr($history[140]['created_at'],0,-3);
                                }
                              }
                              elseif($injury->step==44){
                                if(isset($history[179])){
                                  $end_date=substr($history[179]['created_at'],0,-3);
                                }elseif(isset($history[140])){
                                    $end_date=substr($history[140]['created_at'],0,-3);
                                }
                              }
                              elseif($injury->step==46){
                                if(isset($history[194])){
                                  $end_date=substr($history[194]['created_at'],0,-3);
                                }elseif(isset($history[140])){
                                    $end_date=substr($history[140]['created_at'],0,-3);
                                }
                              }
                              elseif($injury->step==37){
                                if(isset($history[183])){
                                  $end_date=substr($history[183]['created_at'],0,-3);
                                }elseif(isset($history[140])){
                                    $end_date=substr($history[140]['created_at'],0,-3);
                                }
                              }
                            }
                            if(!$end_date)
                              $end_date=(is_null($injury->date_end) || $injury->step != '-7') ? '---' : substr($injury->date_end, 0, -3);

                            $sheet->appendRow(array(
                                ($injury->client) ? $injury->client->name : '---',
                                ($injury->vehicle) ? $injury->vehicle->nr_contract : '---',
                                ($injury->vehicle) ? $injury->vehicle->owner->name : '---',
                                substr($injury->created_at, 0, -3),
                                $injury->date_event,
                                $this->processingType($injury),
                                $this->injuryType($injury),
                                $injury->injury_nr,
                                ($injury->task_authorization == 1) ? 'tak' : 'nie',
                                $this->getLastActionDate($injury),
                                $injury->status->name,
                                $end_date,
                                $injury->user->name,
                                $this->days($injury),
                                $this->daysTotal($injury),

                                ($injury->vehicle) ? $injury->vehicle->registration : '---',
                                $injury->case_nr,
                                ($injury->leader) ? $injury->leader->name : '---',
                                ($injury->leader) ? substr($injury->leader_assign_date, 0, -3) : '---',
                                $this->daysOnCurrentStep($injury),

                                ($injury->documents->count() > 0) ? 'TAK' : 'NIE',
                                ($injury->documents->count() > 0) ? $injury->documents->first()->created_at->format('Y-m-d h:i') : '',

                                ($injury->wreck) ? $injury->wreck->pro_forma_number : '',
                                ($injury->wreck) ? $injury->wreck->contractor_code : '',
                                ($injury->wreck) ? $injury->wreck->pro_forma_value : '',
                                ($injury->wreck && $injury->wreck->invoice_request_confirm != '0000-00-00') ? $injury->wreck->invoice_request_confirm : ''
                            ));
                        }
                    });
            });

        })->store('csv', \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/'.$this->reportType->name, true);*/

    }

    private function generateTheads()
    {
        return array(
            'Klient',
            'Umowa',
            'Właściciel pojazdu',
	        'Uprzedni NIP właściciela',
            'Data Zgłoszenia',
            'Data Zdarzenia',
            'Etap procesowania',
            'Typ Szkody',
            'Numer Szkody Towarzystwa',
            'Wystawiono upoważnienie',
            'Data ostatniej modyfikacji',
            'Status szkody ',
            'Data zakończenia szkody',
            'Przyjmujący szkodę',
            'Upłynęło',
            'Czas likwidacji',

            'Nr rejetracyjny',
            'Nr sprawy',
            'Prowadzący',
            'Data przypisania prowadzącego',
            'Dni na obecnym etapie',

            'Zlecono wystawienie FV – Proforma',
            'Data wygenerowania Zał. 5 c Zlecenie wystawienia FV PRO FROMA',

            'Numer FV proforma',
            'Kod kontrahenta',
            'Kwota brutto z FV',
            'Dostarczono FV właściwą',
            'Polisa GAP',
            'Kwota wypłaconego odszkodowania na właściciela',
            'Kwota wypłaconego odszkodowania GAP',
            'Prognoza GAP',

            'Nazwa TU',
            'Wartość pojazdu na dzień szkody',
            'Wartość pozostałości',
            'Data wypłaty(data decyzji)',
            'Rodzaj szkody (OC/AC)',
            'Złomowanie pojazdu'
        );
    }

    private function processingType($injury)
    {
        if ($injury->step != '-7') {
            if ($injury->totalStatus)
                return $injury->totalStatus->name;
            elseif ($injury->theftStatus)
                return $injury->theftStatus->name;
        }

        return '---';
    }

    private function injuryType($injury)
    {
        if (in_array($injury->step,[30,31,32,33,34,35,36,37]))
            return 'całkowita';
        elseif (in_array($injury->step, [40,41,42,43,44,45,46]))
            return 'kradzież';

        return '---';
    }

    private function lastModification($chat)
    {
        if(! $chat->isEmpty()){
            foreach($chat as $convarsations)
            {
                if($convarsations->messages) {
                    if (isset($last) && $convarsations->messages->last()) {
                        if ($convarsations->messages->last()->created_at > $last)
                            $last = $convarsations->messages->last()->created_at;
                    } else if($convarsations->messages->last())
                        $last = $convarsations->messages->last()->created_at;
                }
            }
            if(isset($last))
                return substr($last, 0 , -3);
        }

        return '---';
    }

    private function days($injury)
    {
        if(is_null($injury->date_end) || $injury->step != '-7')
        {
            return $injury->created_at->diffInDays(Carbon::now());
        }

        return $injury->created_at->diffInDays(Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end));
    }

    private function daysTotal($injury)
    {
        if(is_null($injury->date_end) || is_null($injury->date_total_theft_register))
        {
            return '';
        }
        $date_total_theft_register = Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_total_theft_register);

        return $date_total_theft_register->diffInDays(Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end));
    }

    private function daysOnCurrentStep($injury)
    {
        if (in_array($injury->step, [40,41,42,43,44,45,46]) && $injury->theftStatus)
        {
            if( $injury->theftStatusesHistory->count() > 0 )
            {
                $status = $injury->theftStatusesHistory->sortByDesc('id')->first()->pivot->created_at;
                return $status->diffInDays($this->now);
            }elseif($injury->theft) {
                switch ($injury->theftStatus->id) {
                    case "1":
                        return $injury->theft->created_at->diffInDays($this->now);
                        break;
                    case "2":
                        if ($injury->theft->send_zu_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->send_zu_confirm)->diffInDays($this->now);
                        break;
                    case "3":
                        if ($injury->theft->police_memo_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->police_memo_confirm)->diffInDays($this->now);
                        break;
                    case "4":
                        $lastAcceptation = $injury->theft->acceptations()->orderBy('date_acceptation', 'desc')->first()->date_acceptation;
                        return Carbon::createFromFormat('Y-m-d H:i:s', $lastAcceptation)->diffInDays($this->now);
                        break;
                    case "5":
                        if ($injury->theft->redemption_investigation_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->redemption_investigation_confirm)->diffInDays($this->now);
                        break;
                    case "6":
                        if ($injury->theft->deregistration_vehicle_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->deregistration_vehicle_confirm)->diffInDays($this->now);
                        break;
                    case "7":
                        if ($injury->theft->compensation_payment_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->compensation_payment_confirm)->diffInDays($this->now);
                        elseif ($injury->theft->compensation_payment_deny != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->compensation_payment_deny)->diffInDays($this->now);
                        elseif ($injury->theft->gap_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->gap_confirm)->diffInDays($this->now);
                        else
                            return '';
                        break;
                    case "8":
                        if ($injury->theft->compensation_payment_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->compensation_payment_confirm)->diffInDays($this->now);
                        elseif ($injury->theft->compensation_payment_deny != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->compensation_payment_deny)->diffInDays($this->now);
                        elseif ($injury->theft->gap_confirm != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->gap_confirm)->diffInDays($this->now);
                        else
                            return '';
                        break;
                    case "9":
                        if ($injury->theft->send_to_dok_date != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->send_to_dok_date)->diffInDays($this->now);
                        break;
                    case "10":
                        if ($injury->theft->punishable != '0000-00-00')
                            return Carbon::createFromFormat('Y-m-d', $injury->theft->punishable)->diffInDays($this->now);
                        break;
                }
            }else {
                if($injury->date_end){
                    return Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end)->diffInDays($this->now);
                }else{
                    return '';
                }
            }
        }elseif (in_array($injury->step, [30,31,32,33,34,35,36,37]) && $injury->totalStatus){
            if( $injury->totalStatusesHistory->count() > 0 )
            {
                $status = $injury->totalStatusesHistory->sortByDesc('id')->first()->pivot->created_at;
                return $status->diffInDays($this->now);
            }else {
                switch ($injury->totalStatus->id) {
                    case "6":
                        return Carbon::createFromFormat('Y-m-d', $injury->wreck->invoice_request_confirm)->diffInDays($this->now);
                        break;
                    case "12":
                        return Carbon::createFromFormat('Y-m-d', $injury->wreck->scrapped)->diffInDays($this->now);
                        break;
                    case "13":
                        return Carbon::createFromFormat('Y-m-d', $injury->wreck->off_register_vehicle_confirm)->diffInDays($this->now);
                        break;
                    case "7":
                        return Carbon::createFromFormat('Y-m-d', $injury->wreck->dok_transfer)->diffInDays($this->now);
                        break;
                    case "14":
                        $history = $injury->historyEntries()->where('history_type_id', 161)->orderBy('created_at', 'desc')->first();
                        return Carbon::createFromFormat('Y-m-d H:i:s', $history->created_at)->diffInDays($this->now);
                        break;
                    case "11":
                        if($injury->date_end)
                            return Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end)->diffInDays($this->now);
                        break;
                    case "1":
                        $doc = $injury->getDocument(3, 11)->orderBy('created_at', 'desc')->first();
                        return Carbon::createFromFormat('Y-m-d H:i:s', $doc->created_at)->diffInDays($this->now);
                        break;
                    case "9":
                        $doc = $injury->getDocument(3, 15)->orderBy('created_at', 'desc')->first();
                        return Carbon::createFromFormat('Y-m-d H:i:s', $doc->created_at)->diffInDays($this->now);
                        break;
                }
            }
        }

        return '';
    }

    private function isGap($injury){
      return \Config::get('definition.insurance_options_definition.'.$injury->vehicle->gap);
    }

    private function value_compensation_real($injury){
      $sumCompensation = 0;
      $compensations_types = [1,2,3,4,5,6,7,8];
      foreach($injury->compensations as $compensation){
        if(in_array($compensation->injury_compensation_decision_type_id,$compensations_types)&&$compensation->receive_id==2){
          if(!is_null($compensation->compensation)){
              if($compensation->injury_compensation_decision_type_id == 7)
                $compensation->compensation = abs($compensation->compensation) * -1;
              $sumCompensation+=$compensation->compensation;
          }
        }
      }
      return number_format($sumCompensation, 2, ",", "");
    }

    private function value_compensation_real_gap($injury){
      $sumCompensation = 0;
      $compensations_types = [9];
      foreach($injury->compensations as $compensation){
        if(in_array($compensation->injury_compensation_decision_type_id,$compensations_types)&&$compensation->receive_id==2){
          if(!is_null($compensation->compensation)){
              $sumCompensation+=$compensation->compensation;
          }
        }
      }
      return number_format($sumCompensation, 2, ",", "");
    }

    private function gap_forecast($injury){
      if($injury->vehicle&&$injury->vehicle->gap==1){
        if($injury->injuryGap && $injury->injuryGap->forecast){
          return number_format($injury->injuryGap->forecast, 2, ",", "");
        }
      }
      return "---";
    }

    private function findDateCompensation($injury)
    {
        foreach ($injury->compensations as $compensation) {
            if(in_array($compensation->injury_compensation_decision_type_id, [1, 2, 3]) )
                return $compensation->date_decision;
        }

        return '';
    }

    private function endDate($injury)
    {
        return $injury->date_end;
        $end_date=null;
        $histories = array_reverse($injury->historyEntries->toArray());
        $helper = array(
            '-10'=>29,
            15=>114,
            16=>163,
            17=>115,
            18=>164,
            21=>163,
            23=>174,
            24=>173,
            25=>74,
            34=>180,
            35=>181,
            45=>178,
            44=>179,
            36=>182,
            37=>183,
        );

        foreach($histories as $history)
        {
            if( in_array($history['history_type_id'], $helper) ){
                return  substr($history['created_at'],0,-3);
            }
        }

        return $end_date;
    }

    private function scrapped($injury)
    {
        if(! $injury->wreck){
            return '';
        }

        if( $injury->wreck->scrapped && !is_null($injury->wreck->scrapped)){
            return 'tak';
        }

        return 'nie';
    }

}
