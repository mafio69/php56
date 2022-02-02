<?php

namespace Idea\Reports\InjuriesReports;


use Idea\Reports\BaseReport;
use Idea\Reports\ReportsInterface;
use Injury;

class IdeaBankLoanReport extends BaseReport implements ReportsInterface
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

        \Excel::create($this->filename, function($excel) {
            $excel->sheet('Export', function($sheet) {
                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $sheet->appendRow( $this->generateHeaders() );

                Injury::where('active', '=', '0')->whereBetween('created_at', array($date_from, $date_to))
                    ->where('step', '!=', '-10')
                    ->vehicleExists('nr_contract', '/PK', 'where')
                    ->vehicleOwnerData('8', $this->params['nip'])
                    ->with('vehicle', 'status', 'status.injuryGroup', 'vehicle.insurance_company', 'vehicle.brand', 'vehicle.model', 'client', 'wreck', 'theft', 'historyEntries', 'compensations', 'documents', 'injuries_type')
                    ->chunk(100, function($injuries) use (&$sheet){
                        foreach ($injuries as $k => $injury) {
                            $sheet->appendRow(array(
                                $injury->injury_nr,
                                ($injury->client) ? $injury->client->name : '---',
                                $injury->vehicle->nr_contract,
                                $injury->date_event,
                                ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '---',
                                checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand).' '.checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model),
                                $injury->vehicle->registration,
                                $injury->vehicle->insurance,
                                ($injury->wreck) ? $injury->wreck->value_undamaged : '---',
                                $this->injuryType($injury),
                                ($injury->wreck) ? $injury->wreck->value_repurchase : '---',
                                '',
                                $this->compensation($injury),
                                $this->compensationDate($injury),
                                ($injury->theft) ? $injury->theft->deregistration_vehicle : '---',
                                '',
                                $injury->status->name,
                                '',

                                ($injury->task_authorization == 1) ? 'TAK' : 'NIE',
                                ($injury->task_authorization == 1) ? $this->taskAuthorizationDate($injury) : '',
                                $injury->injuries_type->name
                            ));
                        }
                    });
            });

        })->export('xls');
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

    private function generateHeaders()
    {
        return [
            'nr',
            'użytkownik',
            'nr umowy',
            'data powstania szkody',
            'zakład ubezpieczeń',
            'pojazd',
            'nr rej.',
            'suma ubezpieczenia',
            'wartość pojazdu na dzień szkody',
            'kwalifikacja szkody',
            'wartości pozostałości',
            'prognozowane odszkodowanie',
            'wypłacone odszkodowanie',
            'data wypłaty odszkodowania',
            'data wyrejestrowania',
            'gdzie jest pojazd',
            'status szkody',
            'uwagi',

            'wystawiono upoważnienie',
            'data wystawienia upoważnienia',
            'ryzyko (OC/AC)'
        ];
    }

    private function compensation($injury)
    {
        $compensation_val = 0;
        foreach($injury->compensations as $compensation)
        {
            if(in_array($compensation->injury_compensation_decision_type_id, [1,2,3,9]))
            {
                $compensation_val += $compensation->compensation;
            }
        }

        if($compensation_val > 0 )
            return  $compensation_val;

        return '';
    }

    private function compensationDate($injury)
    {
        $compensation_date = '';
        foreach($injury->compensations as $compensation)
        {
            if(in_array($compensation->injury_compensation_decision_type_id, [1,2,3,9]))
            {
                $compensation_date = $compensation->date_decision;
            }
        }

        return $compensation_date;
    }

    private function taskAuthorizationDate($injury)
    {
        foreach ($injury->documents as $document)
        {
            if($document->type == 3 && $document->document_type()->first()->task_authorization == 1){
                return $document->created_at->format('Y-m-d');
            }
        }
    }
}