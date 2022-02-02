<?php
namespace Idea\Reports\InjuriesReports;

use Carbon\Carbon;
use Config;
use InjuryFiles;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use Auth;

class CustomReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

    private $params;
    private $filename;
    private $statuses;

    private $headers = [
        'nr_contract' => 'Nr umowy leasingu',
        'registration' => 'Nr rejestracyjny pojazdu',
        'vin' => 'VIN pojazdu',
        'client_name' => 'Nazwa klienta',
        'client_address' => 'Adres klienta',
        'client_nip'    =>  'NIP klienta',
        'injury_nr' => 'Nr szkody ZU',
        'injury_type' => 'Typ szkody',
        'injury_kind'   =>  'Rodzaj szkody',
        'date_event' => 'Data szkody',
        'vehicle'   => 'Przedmiot',
	    'brand' =>  'Marka pojazdu',
	    'model' =>  'Model pojazdu',
        'remarks'   => 'Opis zdarzenia',
        'damages'   => 'Uszkodzenia',
        'event_place'   =>  'Miejsce zdarzenia',
        'branch'    =>  'Serwis',
        'info'  =>  'Uwagi',
        'compensation'  =>  'Wysokość odszkodowania',
        'status'    =>  'Status szkody',
        'current_status' => 'Aktualny status',
        'owner'     =>  'Właściciel pojazdu',
        'created'   =>  'Data zgłoszenia',
        'processing_type'   =>  'Etap procesowania',
        'task_authorization'    =>  'Wystawiono upoważnienie',
        'last_action'   =>  'Data ostatniej modyfikacji',
        'date_end'  =>  'Data zakończenia szkody',
        'user'  =>  'Przyjmujący szkodę',
        'days'  =>  'Upłynęło',
        'leader' => 'Prowadzący',
        'leader_assign' =>  'Data przypisania prowadzącego',
        'on_current_step'   =>  'Dni na obecnym etapie',
        'fv_proforma'   =>  'Zlecono wystawienie FV – Proforma',
        'fv_proforma_date'  =>  'Data wygenerowania Zał. 5 c Zlecenie wystawienia FV PRO FROMA',
        'fv_proforma_number'    =>  'Numer FV proforma',
        'contractor_code'   =>  'Kod kontrahenta',
        'pro_forma_value'   =>  'Kwota brutto z FV',
        'invoice_request_confirm'   =>  'Dostarczono FV właściwą',
        'insurance_company' =>  'Nazwa TU',
        'value_undamaged'   =>  'Wartość pojazdu na dzień szkody',
        'value_repurchase'  =>  'Wartośc pozostałosci',
        'value_compensation'    =>  'Odszkodowanie wg wyliczeń TU',
        'insurance_compensation'      =>  'Suma ubezpieczenia',
        'gap'                   =>  'GAP kwota odszkodowania',
        'value_indemnified' =>  'Kwota odszkodowania wypłaconego',
        'date_indemnified'  =>  'Data wypłaty (data decyzji)',
        'date_theft'        =>  'Data wyrejestrowania (przy kradzieży)',
        'authorization_date'    =>  'Data wystawienia upoważnienia',
        'receiver'          =>  'Na kogo upoważnienie',
        'order_sent'    => 'Czy wysłano zlecenie',
        'cfm'   => 'CFM',
        'case_nr'   =>  'Numer Szkody wewnętrzny',
        'branch_type'   =>  'Grupa serwisu',
        'branch_address'    =>  'Adres Serwisu',
        'branch_voivodeship' =>  'Serwis - województwo',
        'net_invoices'  =>  'Wartość Netto z Faktury',
        'driver'    =>  'Kierowca pojazdu',
        // 'fee'   =>  'Czy naliczyć opłatę',
        'fee2016'   =>  'Czy naliczyć opłatę 2016',
        'step'   =>  'Etap Sprawy',
        'repair_step'   =>  'Etap Naprawy',
        'reported_ic'   =>  'Zgłoszona do TU',
        'is_gap' => 'Polisa GAP',
        'person_generated' => 'Osoba generująca zlecenie',
        'production_year' => 'Rok produkcji',
        'contract_status' => 'Status umowy na dzień zgłoszenia',
        'end_leasing' => 'Data ważności umowy',
        'value_compensation_real' => 'Kwota wypłaconego odszkodowania na właściciela',
        'value_compensation_real_gap' => 'Kwota wypłaconego odszkodowania GAP',
        'gap_forecast' => 'Prognoza GAP',
        'previous_nip' => 'Uprzedni NIP właściciela',
        'fv_date' => 'Data wprowadzenia FV',
        'fv_type' => 'Typ FV',
        'date_of_generate_order' => 'Data wygenerowania zlecenia',
        'cost_estimate' => 'Kosztorysowe rozliczenie',
        // 'skip_in_ending_report' => 'Opłata Naliczona',
        'injury_has_feed_document' => 'Czy naliczyć opłatę',
        'gap_number' => 'Numer szkody GAP',
        'dsp_notification' => 'Zgłoszenie DSP',
        'vindication' => 'Windykacja',
        'cas_offer_agreement' => 'Zgoda na ofertę CAS',
        'if_doc_fee_enabled' => 'zgoda na odstępstwo od opłaty za UP',
        'sap_rodzszk' => 'SAP rodzszk',
        'sales_program' => 'program sprzedazy',
        'date_total_theft_register' => 'Data przejścia na szkodę całkowitą',
        'client_phone' => 'telefon LB/PB',
        'client_city' => 'klient miasto',
        'client_voivodeship' => 'klient województwo',
        'client_email' => 'email Klienta',
        'gap_type' => 'rodzaj produktu GAP',
        'type_incident' => 'rodzaj zdarzenia',
        'request_loss_value' => 'wniosek o HUWP',
        'consent_to_invoice' => 'zgoda na FV na IGL',
        'vehicle_type' => 'rodzaj pojazdu',
        'invoicereceive' => 'odbiorca FV',
    ];

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;
        $this->now = Carbon::now();

        foreach(\DB::table('injury_steps')->get() as $item){
            $this->statuses[$item->id] = $item;
        }

        foreach(\DB::table('injury_groups')->get() as $item){
            $this->groups[$item->id] = $item->name;
        }
    }

    public function generateReport()
    {
        set_time_limit(0);
        $response = new StreamedResponse(function(){
            // Open output stream
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, $this->generateTheads());

            $injury_group_ids = [];
            if(isset($this->params['subparams'])) {
                $subparams = $this->params['subparams'];
                if(isset($subparams['injury_kind'])) {
                    if(isset($subparams['injury_kind']['partial'])) $injury_group_ids[] = 1;
                    if(isset($subparams['injury_kind']['total'])) $injury_group_ids[] = 2;
                    if(isset($subparams['injury_kind']['theft'])) $injury_group_ids[] = 3;
                }
            }

            \Injury::where('active', 0)->where(function($query){
                if($this->params['date_from'] != ''){
                    $date_from = $this->parseDate($this->params['date_from'], '0');
                    $query->where('created_at', '>', $date_from);
                }
                if($this->params['date_to'] != ''){
                    $date_to = $this->parseDate($this->params['date_to'], '+1');
                    $query->where('created_at', '<', $date_to);
                }
            })
//            ->join('injury_steps', function ($join) {
//                $join->on('step', '=', 'injury_steps.id');
//            })
            ->where(function ($query) use($injury_group_ids) {
                if(count($injury_group_ids) > 0) {
                    $query->whereHas('status', function ($query) use ($injury_group_ids) {
                        $query->whereIn('injury_group_id', $injury_group_ids);
                    });
                }
            })
            ->where('step' , '!=' , '-10')
            ->orderBy('id')->chunk(1000, function($injuries) use($handle) {

                $injuries->load(['client',
                    'injuries_type',
                    'getRemarks',
                    'getInfo',
                    'damages',
                    'damages.damage',
                    'branch',
                    'branch.company',
                    'branch.company.groups',
                    'branch.voivodeship',
                    'invoices' => function($query){
                        $query->where('active','=', '0')->with('injury_files');
                    },
                    'totalStatus',
                    'theftStatus',
                    'chat',
                    'chat.messages',
                    'user',
                    'leader',
                    'theft',
                    'theft.acceptations',
                    'totalRepair',
                    'wreck',
                    'totalStatusesHistory',
                    'theftStatusesHistory',
                    'documents',
                    'compensations',
                    'receive',
                    'driver',
                    'injuryGap',
                    'injuryGap.gapType',
                    'contractStatus',
                    'type_incident'
                ]);
                $filesA = InjuryFiles::whereActive(0)->whereType(3)->whereIn('injury_id', $injuries->lists('id'))->get();
                $filesInjuryA = array();
                foreach ($filesA as $file) {
                    if(!isset($filesInjuryA[$file->category][$file->injury_id]))
                        $filesInjuryA[$file->injury_id][$file->category] = $file;
                }

                $filesB = InjuryFiles::whereType(3)->whereIn('injury_id', $injuries->lists('id'))->orderBy('created_at')->get();

                $filesInjuryB= array();
                foreach ($filesB as $file) {
                    if(!isset($filesInjuryB[$file->injury_id][$file->category]))
                        $filesInjuryB[$file->injury_id][$file->category] = $file;
                }

                $filesFeeA = InjuryFiles::whereActive(0)->whereType(3)->whereHas('document_type', function($query) {
                    $query->where('fee', 1);
                })->where('if_fee', 1)->whereIn('injury_id', $injuries->lists('id'))->get();
                foreach ($filesFeeA as $file) {
                    if(!isset($filesInjuryA[$file->injury_id]['fee']))
                        $filesInjuryA[$file->injury_id]['fee'] = $file;
                }

                foreach ($injuries as $injury)
                {
                    $row = [];
                        foreach ($this->params['fields'] as $field_name => $v)
                        {
                            $row[] = $this->$field_name($injury, $filesInjuryA, $filesInjuryB);
                        }
                        fputcsv($handle, $row);
                }
            });

            // Close the output stream
            fclose($handle);           
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'.csv"',
        ]);
        return $response;
    }

    public function generateTheads()
    {
        $theads = [];
        foreach ($this->params['fields'] as $field_name => $v)
        {
            $theads[] = $this->headers[$field_name];
        }

        return $theads;
    }

    private function nr_contract($injury){
        return $injury->vehicle->nr_contract;
    }

    private function registration($injury){
        return $injury->vehicle->registration;
    }

    private function vin($injury){
        return ($injury->vehicle_type == 'Vehicles') ? $injury->vehicle->VIN : $injury->vehicle->vin;
    }

    private function client_name($injury){
        return ($injury->client) ? str_replace("\n",' ', $injury->client->name) : '';
    }

    private function client_address($injury){
        return ($injury->client) ? $injury->client->registry_post.' '.$injury->client->registry_city.' '. $injury->client->registry_street : '';
    }

    private function client_nip($injury){
        return ($injury->client) ? $injury->client->NIP : '';
    }

    private function injury_nr($injury){
        return $injury->injury_nr;
    }

    private function injury_type($injury){
        return $injury->injuries_type->name;
    }

    private function date_event($injury){
        return $injury->date_event;
    }

    private function vehicle($injury){
        return checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand).' '. checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model);
    }

    private function brand($injury)
    {
	    return checkObjectIfNotNull($injury->vehicle->brand, 'name', $injury->vehicle->brand);
    }

    private function model($injury)
    {
	    return checkObjectIfNotNull($injury->vehicle->model, 'name', $injury->vehicle->model);
    }

    private function remarks($injury)
    {
        return ($injury->getRemarks) ? trim(preg_replace('/\s+/', ' ',strip_tags($injury->getRemarks->content))) : '';
    }

    private function damages($injury)
    {
        if( $injury->damages->isEmpty() )
            return '';

        $result = '';
        foreach($injury->damages as $damage)
        {
            $result .= $damage->damage->name;
            if($damage->param != 0) {
                if ($damage->param == 1)
                    $result .= ' lewe/y';
                else
                    $result .= ' prawe/y';
            }
            $result .= ', ';
        }
        return trim(preg_replace('/\s+/', ' ',strip_tags($result)));
    }

    private function event_place($injury)
    {
        return trim(preg_replace('/\s+/', ' ',strip_tags($injury->event_post.' '.$injury->event_city.' '.$injury->event_street)));
    }

    private function branch($injury)
    {
        return ($injury->branch) ? $injury->branch->short_name.' - '.$injury->branch->post.' '.$injury->branch->city.', '.$injury->branch->street : '';
    }

    private function info($injury)
    {
        return ($injury->getInfo) ? trim(preg_replace('/\s+/', ' ',strip_tags($injury->getInfo->content))) : '';
    }

    private function compensation($injury)
    {
        if(!$injury->invoices->isEmpty()){
            $value = 0;
            foreach($injury->invoices as $invoice)
            {
                $value += $invoice->netto+$invoice->vat;
            }
            return money_format("%.2n",$value);
        }

        return '';
    }

    private function status($injury)
    {
        if(isset($this->statuses[$injury->step]))
            return $this->statuses[$injury->step]->name;

        return '';
    }

    private function current_status($injury){
        if($injury->contractStatus)
            return $injury->contractStatus->name;

        return '';
    }

    private function owner($injury)
    {
        return ($injury->vehicle && $injury->vehicle->owner) ? $injury->vehicle->owner->name : '';
    }

    private function created($injury)
    {
        return $injury->created_at->format('Y-m-d H:i');
    }

    private function processing_type($injury)
    {
        if ($injury->step != '-7') {
            if ($injury->totalStatus)
                return $injury->totalStatus->name;
            elseif ($injury->theftStatus)
                return $injury->theftStatus->name;
        }

        return '';
    }

    private function task_authorization($injury)
    {
        return ($injury->task_authorization == 1) ? 'tak' : 'nie';
    }

    private function last_action($injury)
    {
        $update_date = $injury->updated_at;
        $last_message = null;
        if($injury->chat->count() > 0){
            $injury->chat->each(function ($chat) use(&$last_message) {
                if($chat->messages->count() > 0) {
                    $messages = $chat->messages;
                    $message_date = $messages->last()->created_at;
                    if (!$last_message || $message_date->gte($last_message)) {
                        $last_message = $message_date;
                    }
                }
            });
        }
        if(! $last_message)
            return $update_date->format('Y-m-d H:i');

        if( $last_message->gte($update_date) )
            return $last_message->format('Y-m-d H:i');

        return $update_date->format('Y-m-d H:i');
    }

    private function date_end($injury)
    {
        return $injury->date_end ? substr($injury->date_end, 0, -3) : '';
    }

    private function user($injury)
    {
        return $injury->user->name;
    }

    private function days($injury)
    {
        if(is_null($injury->date_end) || $injury->step != '-7')
        {
            return $injury->created_at->diffInDays(Carbon::now());
        }

        return $injury->created_at->diffInDays(Carbon::createFromFormat('Y-m-d H:i:s', $injury->date_end));
    }

    private function leader($injury)
    {
        return ($injury->leader) ? $injury->leader->name : '';
    }

    private function leader_assign($injury)
    {
        return ($injury->leader) ? substr($injury->leader_assign_date, 0, -3) : '';
    }

    private function on_current_step($injury)
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
            }
        }elseif ($injury->step == '-5' && $injury->totalStatus){
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

    private function fv_proforma($injury)
    {
        foreach($injury->documents as $document)
        {
            if($document->type == 3 && $document->category == 75)
            {
                return 'TAK';
            }
        }
        return 'NIE';
    }

    private function fv_proforma_date($injury)
    {
        foreach($injury->documents as $document)
        {
            if($document->type == 3 && $document->category == 75)
            {
                return $document->created_at->format('Y-m-d h:i');
            }
        }
        return '';
    }

    private function fv_proforma_number($injury)
    {
        return ($injury->wreck) ? $injury->wreck->pro_forma_number : '';
    }

    private function contractor_code($injury)
    {
        // return ($injury->wreck) ? $injury->wreck->contractor_code : ''; task 2350
        return ($injury->client) ? $injury->client->firmID : '';
    }

    private function pro_forma_value($injury)
    {
        return ($injury->wreck) ? $injury->wreck->pro_forma_value : '';
    }

    private function invoice_request_confirm($injury)
    {
        return ($injury->wreck && $injury->wreck->invoice_request_confirm != '0000-00-00') ? $injury->wreck->invoice_request_confirm : '';
    }

    private function insurance_company($injury)
    {
        return ($injury->vehicle->insurance_company_id != 0) ? $injury->vehicle->insurance_company()->first()->name : '';
    }

    private function value_undamaged($injury)
    {
        return ($injury->wreck) ? $injury->wreck->value_undamaged : 0;
    }

    private function value_repurchase($injury)
    {
        return ($injury->wreck) ? $injury->wreck->value_repurchase : 0;
    }

    private function value_compensation($injury)
    {
        return ($injury->wreck) ? $injury->wreck->value_compensation : $this->value_indemnified($injury);
    }

    private function insurance_compensation($injury)
    {
        return $injury->vehicle->insurance;
    }

    private function gap($injury)
    {
        $sumGap = 0;
        foreach ($injury->compensations as $compensation) {
            if($compensation->injury_compensation_decision_type_id == 9)
                $sumGap+=$compensation->compensation;
        }

        return $sumGap;
    }

    private function value_indemnified($injury)
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

    private function date_indemnified($injury)
    {
        foreach ($injury->compensations as $compensation) {
            if(in_array($compensation->injury_compensation_decision_type_id, [1, 2, 3]) )
                return $compensation->date_decision;
        }

        return '';
    }

    private function date_theft($injury)
    {
        return ($injury->theft) ? $injury->theft->deregistration_vehicle : '';
    }

    private function authorization_date($injury, $files)
    {
        if( isset($files[$injury->id][71]) )
            return $files[$injury->id][71]->created_at->format('Y-m-d H:i');

        if( isset($files[$injury->id][72]) )
            return $files[$injury->id][72]->created_at->format('Y-m-d H:i');

        if( isset($files[$injury->id][73]) )
            return $files[$injury->id][73]->created_at->format('Y-m-d H:i');

        if( isset($files[$injury->id][79]) )
            return $files[$injury->id][79]->created_at->format('Y-m-d H:i');

        if( isset($files[$injury->id][68]) )
            return $files[$injury->id][68]->created_at->format('Y-m-d H:i');

        return '';
    }

    private function receiver($injury)
    {
        return ($injury->task_authorization == 0 || $injury->receive_id == 0) ? '' : $injury->receive->name;
    }

    private function order_sent($injury, $files)
    {
        if( isset($files[$injury->id][6]) || isset($files[$injury->id][49]) || isset($files[$injury->id][60]) || isset($files[$injury->id][52]))
            return 'tak';

        return 'nie';
    }

    private function cfm($injury)
    {
        return ($injury->vehicle->cfm == 1) ? 'tak' : 'nie';
    }

    private function case_nr($injury)
    {
        return $injury->case_nr;
    }

    private function branch_type($injury)
    {
        if($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0 && $injury->branch->company && $injury->branch->company->groups && $injury->branch->company->groups->count() > 0) {
            return 'Serwis w grupie: '.implode(',', $injury->branch->company->groups->lists('name'));
        }elseif($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0 ) {
            return 'Serwis inny - poza grupą';
        }

        return  '';
    }

    private function branch_address($injury)
    {
        return ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0) ? $injury->branch->code . ' ' . $injury->branch->city . ', ' . $injury->branch->street : '';
    }

    private function branch_voivodeship($injury)
    {
        return ($injury->branch && $injury->branch_id != 0 && $injury->branch_id != '-1' && $injury->branch_id > 0 && $injury->branch->voivodeship) ? $injury->branch->voivodeship->name : '';
    }

    private function driver($injury)
    {
        return ($injury->driver_id != '') ? $injury->driver->surname . ' ' . $injury->driver->name . ' ' . $injury->driver->phone . ' ' . $injury->driver->email : '';
    }

    private function fee($injury)
    {
        if ($injury->task_authorization == 1) {
            if ($injury->issue_fee == '-1')
                return 'nie';
            else
                return 'tak';
        }

        return '';
    }

    private function fee2016($injury, $files)
    {
        return (isset($files[$injury->id]['fee'])) ? 'tak' : 'nie';
    }

    private function injury_kind($injury)
    {
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

    private function net_invoices($injury)
    {
        if (count($injury->invoices) == 0) {
            return 0;
        } else {
            $sum = 0;
            foreach ($injury->invoices as $k => $v) {
                $sum += $v->netto;
            }
        }

        return $sum;
    }

    private function reported_ic($injury){
      if( $injury->reported_ic != 1 )
        return 'nie';
      else
        return 'tak';
    }

    private function step($injury){
      if(! in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7'] ))
        if($injury->stepStage)
            return $injury->stepStage->name;
      return '---';
    }

    private function repair_step($injury,$filesInjuryA){
      $branch = $injury->branch()->first();
      if(
          $branch
          &&
          (
              $branch->company && ( $branch->company->groups->contains(1) || ( $branch->company->groups->contains(5) && $injury->vehicle && $injury->vehicle->cfm == 1 ) )
              &&
              ( isset($filesInjuryA[$injury->id][60]) || isset($filesInjuryA[$injury->id][52]) || isset($filesInjuryA[$injury->id][6]) || isset($filesInjuryA[$injury->id][49]))
          )
          &&
          ! in_array( $injury->step, [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46, '-7'] )

      ){
      if($injury->currentRepairStage){
          if($injury->currentRepairStage->value == 1)
              return $injury->currentRepairStage->stage->checked_description;
          else
              return $injury->currentRepairStage->stage->unchecked_description;
          if($injury->currentRepairStage->date_value)
              return '('.$injury->currentRepairStage->date_value->format('Y-m-d').')';
      }
      else
          return 'w oczekiwaniu na potwierdzenie przyjęcia zlecenia';
      }
      else{
          return '---';
      }

    }

    private function is_gap($injury){
    //   return \Config::get('definition.insurance_options_definition.'.$injury->vehicle->gap);
      return \Config::get('definition.insurance_options_definition.'.$injury->injuryPolicy->gap);
    }

    private function person_generated($injury,$files1,$files){
      if(isset($files[$injury->id][60])){
        if($files[$injury->id][60]->user){
          return $files[$injury->id][60]->user->name;
        }
      }
      if(isset($files[$injury->id][49])){
        if($files[$injury->id][49]->user){
          return $files[$injury->id][49]->user->name;
        }
      }
      if(isset($files[$injury->id][6])){
        if($files[$injury->id][6]->user){
          return $files[$injury->id][6]->user->name;
        }
      }
      if(isset($files[$injury->id][52])){
        if($files[$injury->id][52]->user){
          return $files[$injury->id][52]->user->name;
        }
      }
      return "---";

    }

    private function production_year($injury){
      if($injury->vehicle){
        return $injury->vehicle->year_production;
      }
      return "---";
    }

    private function contract_status($injury){
      if($injury->vehicle){
        return $injury->vehicle->contract_status;
      }
      return "---";
    }

    private function end_leasing($injury){
      if($injury->vehicle){
        return $injury->vehicle->end_leasing;
      }
      return "---";
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
        // if($injury->vehicle&&$injury->vehicle->gap==1){
        if($injury->vehicle&&$injury->injuryPolicy->gap==1){
            if($injury->injuryGap && $injury->injuryGap->forecast){
                return number_format($injury->injuryGap->forecast, 2, ",", "");
            }
        }
        return "---";
    }

    private function gap_number($injury)
    {
        // if($injury->vehicle&&$injury->vehicle->gap==1){
        if($injury->vehicle&&$injury->injuryPolicy->gap==1){
            if($injury->injuryGap && $injury->injuryGap->injury_number){
                return $injury->injuryGap->injury_number;
            }
        }
        return "";
    }

    private function previous_nip($injury){
        return ( $injury->vehicle && $injury->vehicle->owner) ? $injury->vehicle->owner->old_nip : '';
    }

    private function date_of_generate_order($injury, $filesInjuryA)
    {
        if(isset($filesInjuryA[$injury->id][6]))
        {
            return substr($filesInjuryA[$injury->id][6]->created_at, 0, -3);
        }

        if(isset($filesInjuryA[$injury->id][49]))
        {
            return substr($filesInjuryA[$injury->id][49]->created_at, 0, -3);
        }

        if(isset($filesInjuryA[$injury->id][60]))
        {
            return substr($filesInjuryA[$injury->id][60]->created_at, 0, -3);
        }

        return '';
    }

    private function cost_estimate($injury)
    {
        if($injury->settlement_cost_estimate == 1)
            return 'tak';

        return 'nie';
    }

    private function fv_date($injury)
    {
        $result = [];
        foreach($injury->invoices as $invoice)
        {
            if($invoice->active == 0)
            {
                $result[] = substr($invoice->created_at, 0, -3);
            }
        }

        return implode('; ', $result);
    }

    private function fv_type($injury)
    {
        $result = [];
        foreach($injury->invoices as $invoice)
        {
            if($invoice->active == 0)
            {
                $result[] = ( $invoice->injury_files && $invoice->injury_files->category == 3) ? Config::get('definition.fileCategory.3') : Config::get('definition.fileCategory.4');
            }
        }

        return implode('; ', $result);
    }

    private function skip_in_ending_report($injury)
    {
        if($injury->skip_in_ending_report == 0){
            return 'nie';
        }

        return 'tak';
    }

    private function dsp_notification($injury){
        if($injury->dsp_notification == 1){
            return 'tak';
        }

        return 'nie';
    }

    private function if_doc_fee_enabled($injury){
        if($injury->if_doc_fee_enabled == 1){
            return 'nie';
        }

        return 'tak';
    }

    private function sap_rodzszk($injury){
        return is_null($injury->sap_rodzszk)?'brak':$injury->sap_rodzszk;
    }


    private function vindication($injury){
        if($injury->vindication == 1){
            return 'tak';
        }

        return 'nie';
    }

    private function cas_offer_agreement($injury){
        if($injury->cas_offer_agreement === 1){
            return 'tak';
        }

        return 'nie';
    }

    private function injury_has_feed_document($injury)
    {
        $documents = $injury->documents;
        foreach ($documents as $doc) {
            if ($doc->if_fee_collected && $doc->active == 0) return 'tak';
        }
        return 'nie';
    }

    private function sales_program($injury){
        if($injury->vehicle){
            return $injury->vehicle->program;
        }

        return '---';
    }

    private function date_total_theft_register($injury){
        return $injury->date_total_theft_register;
    }

    private function client_phone($injury){
        return is_null($injury->client)?null:$injury->client->phone;
    }

    private function client_city($injury){
        return is_null($injury->client)?null:$injury->client->registry_city;
    }

    private function client_voivodeship($injury){
        return is_null($injury->client) || is_null($injury->client->registryVoivodeship)?null:$injury->client->registryVoivodeship->name;
    }

    private function gap_type($injury){
        if($injury->injuryGap && $injury->injuryGap->gapType){
            return $injury->injuryGap->gapType->name;
        }

        return '';
    }

    private function client_email($injury){
        return is_null($injury->client)?null:$injury->client->email;
    }

    private function type_incident($injury)
    {
        if( $injury->type_incident_id != 0 && $injury->type_incident_id != null){
            return $injury->type_incident->name;
        }

        return '';
    }

    private function request_loss_value($injury, $filesInjuryA)
    {
        if(isset($filesInjuryA[$injury->id][87]))
        {
            return substr($filesInjuryA[$injury->id][87]->created_at, 0, -3);
        }
    }

    private function consent_to_invoice($injury, $filesInjuryA)
    {
        if(isset($filesInjuryA[$injury->id][81]))
        {
            return 'tak';
        }

        return 'nie';
    }

    private function vehicle_type($injury)
    {
        return $injury->vehicle ? $injury->vehicle->object_type : '';
    }

    private function invoicereceive($injury)
    {
        if($injury->invoicereceives_id == 0){
            return 'nieustalone';
        }

        return $injury->invoicereceive()->first()->name;
    }
}
