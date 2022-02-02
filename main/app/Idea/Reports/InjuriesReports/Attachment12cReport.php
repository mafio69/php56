<?php
namespace Idea\Reports\InjuriesReports;


use Carbon\Carbon;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Injury;
use InjuryFiles;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Attachment12cReport extends BaseReport implements ReportsInterface
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
        set_time_limit(500);
        \DB::disableQueryLog();

        if (ob_get_length() > 0) {
            ob_clean();
        }
        $response = new StreamedResponse(function(){
            // Open output stream
            $handle = fopen('php://output', 'w');

            fputs( $handle, "\xEF\xBB\xBF" );

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            fputcsv($handle, $this->generateTheads(), ';');

            Injury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                ->whereIn('step', [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46,47, '-7'])
                ->chunk(500, function($injuries) use(&$handle){

                $injuries->load('user', 'compensations', 'vehicle', 'vehicle.owner', 'vehicle.insurance_company', 'client', 'injuries_type', 'wreck', 'totalStatus', 'theftStatus', 'status', 'status.injuryGroup', 'leader', 'chat', 'chat.messages', 'injuryGap.insuranceCompany');

                foreach ($injuries as $injury)
                {
                    $injuryType = $this->injuryType($injury);
                    $compensation = $this->calcCompensation($injury);
                    $processingType = $this->processingType($injury);
                    $gap = $this->calcGap($injury);
                    $dateCompensation = $this->findDateCompensation($injury);
                    $days = $this->calcDays($injury);
                    $liquidationPeriod = $this->calcLiquidationPeriod($injury);

                    $rowsToInsert = array(
                        ($injury->client_id == 0) ? '---' : $injury->client->name,
                        $injury->vehicle->registration,
                        $injury->vehicle->nr_contract,
                        $injury->vehicle->owner->name,
                        $injury->vehicle->owner->old_nip,
                        $injury->created_at->format('Y-m-d H:i'),
                        $injury->date_event,
                        $injuryType,
                        ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                        $injury->injury_nr,
                        $injury->injuries_type->name,
                        ($injury->wreck) ? $injury->wreck->value_undamaged : 0,
                        ($injury->wreck) ? $injury->wreck->value_repurchase : 0,
                        ($injury->wreck) ? $injury->wreck->value_compensation : $compensation,
                        $processingType,
                        $injury->vehicle->insurance,
                        $gap,
                        $compensation,
                        $dateCompensation,
                        ($injury->theft) ? $injury->theft->deregistration_vehicle : '',
                        $this->getLastActionDate($injury),
                        $days,
                        $liquidationPeriod,
                        $injury->status->name,
                        ($injury->leader) ? $injury->leader->name : '',
                        $this->isGap($injury),
                        $this->value_compensation_real($injury),
                        $this->value_compensation_real_gap($injury),
                        $this->gap_forecast($injury),
                        $injury->date_end ? substr($injury->date_end, 0, -9) : '',
                        $injury->case_nr,
                        $injury->date_total_theft_register,
                        $injury->leader_assign_date,
                        $injury->sap_rodzszk,
                        $injury->injuryGap ? $injury->injuryGap->insurance_company_id ? $injury->injuryGap->insuranceCompany->name : '' : '',
                        $this->scrapped($injury),
                        $injury->wreck && $injury->wreck->buyer > 0 ? \Config::get('definition.wreck_buyers')[$injury->wreck->buyer] : ''
                    );

                    fputcsv($handle, $rowsToInsert, ';');
                }
            });
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'.csv"',
        ]);

        return $response;

        Excel::load($template, function($excel)  {
            ob_start();
            $excel->setActiveSheetIndex(0);
            $sheet = $excel->getActiveSheet();

            $row = 2;

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            $injuries = Injury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                ->whereIn('step', [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46,47, '-7'])
                ->with('user', 'compensations', 'vehicle', 'vehicle.owner', 'vehicle.insurance_company', 'client', 'injuries_type', 'wreck', 'totalStatus', 'theftStatus', 'status', 'status.injuryGroup', 'leader', 'chat', 'chat.messages', 'injuryGap.insuranceCompany' )->get();
            $limit_date_01022018 = Carbon::createFromFormat('Y-m-d', '2018-02-01');

            ob_clean();
            foreach ($injuries as $k => $injury) {
                $injuryType = $this->injuryType($injury);
                $compensation = $this->calcCompensation($injury);
                $processingType = $this->processingType($injury);
                $gap = $this->calcGap($injury);
                $dateCompensation = $this->findDateCompensation($injury);
                $days = $this->calcDays($injury);
                $liquidationPeriod = $this->calcLiquidationPeriod($injury);

                $rowsToInsert = array(
                    ($injury->client_id == 0) ? '---' : $injury->client->name,
                    $injury->vehicle->registration,
                    $injury->vehicle->nr_contract,
                    $injury->vehicle->owner->name,
	                $injury->vehicle->owner->old_nip,
                    $injury->created_at->format('Y-m-d H:i'),
                    $injury->date_event,
                    $injuryType,
                    ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                    $injury->injury_nr,
                    $injury->injuries_type->name,
                    ($injury->wreck) ? $injury->wreck->value_undamaged : 0,
                    ($injury->wreck) ? $injury->wreck->value_repurchase : 0,
                    ($injury->wreck) ? $injury->wreck->value_compensation : $compensation,
                    $processingType,
                    $injury->vehicle->insurance,
                    $gap,
                    $compensation,
                    $dateCompensation,
                    ($injury->theft) ? $injury->theft->deregistration_vehicle : '',
                    $this->getLastActionDate($injury),
                    $days,
                    $liquidationPeriod,
                    $injury->status->name,
                    ($injury->leader) ? $injury->leader->name : '',
                    $this->isGap($injury),
                    $this->value_compensation_real($injury),
                    $this->value_compensation_real_gap($injury),
                    $this->gap_forecast($injury),
                    $injury->date_total_theft_register,
                    $injury->leader_assign_date,
                    $injury->sap_rodzszk,
                    $injury->injuryGap ? $injury->injuryGap->insurance_company_id ? $injury->injuryGap->insuranceCompany->name : '' : ''
                );
                $sheet->fromArray($rowsToInsert, null, 'A' . $row);
                $row++;
            }

        })->setFileName($this->filename)->export('xls');
    }

    private function injuryType($injury)
    {
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

    private function calcCompensation($injury)
    {
        $sumCompensation = 0;
        foreach ($injury->compensations as $compensation) {
            if(!is_null($compensation->compensation))
                if($compensation->injury_compensation_decision_type_id == 7)
                    $compensation->compensation = abs($compensation->compensation) * -1;
            $sumCompensation+=$compensation->compensation;
        }

        return $sumCompensation;
    }

    private function processingType($injury)
    {
        if ($injury->totalStatus)
            return $injury->totalStatus->name;
        elseif ($injury->theftStatus)
            return $injury->theftStatus->name;

        return '---';
    }

    private function calcGap($injury)
    {
        $sumGap = 0;
        foreach ($injury->compensations as $compensation) {
            if($compensation->injury_compensation_decision_type_id == 9)
                $sumGap+=$compensation->compensation;
        }

        return $sumGap;
    }

    private function findDateCompensation($injury)
    {
        foreach ($injury->compensations as $compensation) {
            if(in_array($compensation->injury_compensation_decision_type_id, [1, 2, 3]) )
                return $compensation->date_decision;
        }

        return '';
    }

    private function calcDays($injury)
    {
        if($injury->date_end && $injury->step == '-7')
        {
            $date_end = Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end);

            return $injury->created_at->diffInDays($date_end);
        }
        return '';
    }

    private function calcLiquidationPeriod($injury)
    {
        if($injury->date_total_theft_register)
        {
            $date_end = Carbon::now();
            if($injury->date_end) $date_end = Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end);

            return Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_total_theft_register)->diffInDays($date_end);
        }
        return '';
    }

    private function isGap($injury){
      return \Config::get('definition.insurance_options_definition.'.$injury->injuryPolicy->gap);
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

    private function generateTheads()
    {
        return [
            'klient',
            'nr rej',
            'nr umowy',
            'własciciel pojazdu',
            'uprzedni NIP właściciela',
            'data zgłoszenia do edb',
            'data zdarzenia',
            'typ szkody',
            'nazwa TU',
            'Nr szkody w TU',
            'rodzaj szkody (OC/AC)',
            'wartość pojazdu na dzień szkody',
            'Wartośc pozostałosci',
            'odszkodowanie wg wyliczeń TU',
            'Etap procesowania',
            'Suma ubezpieczenia',
            'GAP kwota odszkodowania',
            'kwota odszkodowania wypłaconego',
            'data wypłaty (data decyzji)',
            'Data wyrejestrowania (przy kradzieży)',
            'Data ostatniej modyfikacji',
            'Czas szkody w dniach',
            'Czas likwidacji',
            'Status szkody',
            'Osoba prowadząca',
            'Polisa GAP',
            'Kwota wypłaconego odszkodowania na właściciela',
            'Kwota wypłaconego odszkodowania GAP',
            'Prognoza GAP',
            'Data zakończenia szkody',
            'Numerem szkody wewnętrzny',
            'Data przejścia na szkodę całkowitą',
            'Data przypisania procesującego',
            'SAP rodzszk',
            'ZU GAP',
            'Złomowanie pojazdu',
            'Nazwa nabywcy',
        ];
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
