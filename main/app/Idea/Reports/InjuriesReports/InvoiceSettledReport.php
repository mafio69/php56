<?php
namespace Idea\Reports\InjuriesReports;

use Carbon\Carbon;
use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;
use InjuryFiles;
use InjuryInvoices;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceSettledReport extends BaseReport implements ReportsInterface, ReportsCsvInterface {

    private $params;
    private $filename;
    protected $commissions = ['quarterly' => [], 'monthly' => []];
    protected $brands = [];
    /**
     * @var |null
     */
    private $branch;

    function __construct($filename, $params = array())
    {
        $this->params = $params;
        $this->filename = $filename;

        $this->brands = \Brands::lists('name','id');
        $this->brands = array_map("mb_strtoupper", $this->brands);
        $this->brands = array_flip($this->brands);
    }

    public function generateReport()
    {
        set_time_limit(500);
        \DB::disableQueryLog();

        $response = new StreamedResponse(function(){
            $handle = fopen('php://output', 'w');

            fputs( $handle, "\xEF\xBB\xBF" );

            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            fputcsv($handle,$this->generateTheads());

            InjuryInvoices::select('injury_invoices.*')->distinct()
                ->leftJoin('injury', 'injury.id', '=', 'injury_invoices.injury_id')
                ->join('injury_history', 'injury_history.injury_id', '=', 'injury_invoices.injury_id')
                ->where('injury.step' , '!=' , '-10')
                ->where('injury_invoices.active', 0)->where(function($query) use($date_from, $date_to){
                    $query->where(function($query) use($date_from, $date_to){
                            $query->whereBetween('injury_invoices.forward_date', array( $date_from, $date_to ) );
                    });
                    $query->orWhere(function($query) use($date_from, $date_to){
                        $query->where('injury_history.history_type_id', 163)
                            ->whereBetween('injury_history.created_at', array( $date_from, $date_to ) );
                    });
                })->chunk(200, function($invoices) use (&$handle) {
                    $invoices->load('injury', 'injury_files', 'injury_files.user', 'invoicereceive',
                        'injury.branch', 'injury.branch.company', 'injury.branch.company.groups', 'injury.branch.company.contractorGroup',
                        'injury.vehicle.insurance_company', 'injury.vehicle', 'injury.vehicle.owner',
                        'injury.vehicle.owner.nip', 'injury.vehicle.seller', 'serviceType', 'relatedCommission', 'invoicereceive');

                    $filesA = InjuryFiles::whereActive(0)->whereType(3)->where(function($query){
                        $query->whereIn('category', [6,49,60]);
                    })->whereIn('injury_id', $invoices->lists('injury_id'))->get();

                    $filesInjuryA = array();
                    foreach ($filesA as $file) {
                        if(!isset($filesInjuryA[$file->injury_id]))
                            $filesInjuryA[$file->injury_id] = $file;
                    }

                    foreach ($invoices as $k => $invoice) {
                        $commission = $this->generateCommission($invoice);

                        fputcsv($handle,array(
                            $invoice->invoice_nr,
                            ( $invoice->injury_files->category == 3) ? Config::get('definition.fileCategory.3') : Config::get('definition.fileCategory.4'),
                            substr($invoice->created_at, 0, -3),
                            $invoice->injury_files->user->name,
                            $invoice->invoicereceive ? $invoice->invoicereceive->name : '',
                            $invoice->injury->date_end ? substr($invoice->injury->date_end, 0, -3) : '',
                            $invoice->injury->vehicle->owner->name,
                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' && $invoice->branch->company ) ? $invoice->branch->company->name : '---',
                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' && $invoice->branch->company ) ? $invoice->branch->company->city : '---',
                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' && $invoice->branch->company ) ? $invoice->branch->company->street : '---',

                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' ) ? $invoice->branch->short_name : '---',
                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' ) ? $invoice->branch->city : '---',
                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' ) ? $invoice->branch->street : '---',

                            $this-> serviceTypeBranch($invoice),
                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' && $invoice->branch->company ) ? $invoice->branch->company->nip : '---',
                            ($invoice->branch_id != 0 && $invoice->branch_id != '-1' && $invoice->branch->company && $invoice->branch->company->contractorGroup ) ? $invoice->branch->company->contractorGroup->name : '---',
                            $this-> serviceTypeInjuryBranch($invoice),
                            ($this->branch) ? $this->branch->short_name : '---',
                            ($this->branch && $this->branch->company) ? $this->branch->company->nip : '---',
                            ($invoice->netto == 0) ? '---' : $invoice->netto,
                            $invoice->injury->case_nr,
                            ($invoice->injury->injury_nr != '' ) ? $invoice->injury->injury_nr : '---',
                            ($invoice->injury->vehicle->insurance_company_id != 0) ? $invoice->injury->vehicle->insurance_company()->first()->name : '---',
                            ($invoice->commission == 1) ? 'tak' : 'nie',
                            ($invoice->commission == 1) ? $invoice->base_netto : 0,
                            ($commission) ? $commission['commission_percentage'] : '',
                            ($commission) ? $commission['commission'] : '',
                            checkObjectIfNotNull($invoice->injury->vehicle->brand, 'name', $invoice->injury->vehicle->brand),
                            checkObjectIfNotNull($invoice->injury->vehicle->model, 'name', $invoice->injury->vehicle->model),
                            $invoice->injury->vehicle->registration,
                            ($invoice->injury->vehicle->owner->nip->count() > 0) ?  $invoice->injury->vehicle->owner->nip->first()->value : '',
                            (isset($filesInjuryA[$invoice->injury_id])) ? 'tak' : 'nie',
                            ($invoice->serviceType) ? $invoice->serviceType->name : '',
                            $invoice->injury->vehicle->owner->old_nip,
                            $invoice->forward_date,                      
                            $invoice->forward_again_date,

                            $invoice->injury->vehicle->seller ? $invoice->injury->vehicle->seller->name : '',
                            $invoice->injury->dsp_notification == 1 ? 'tak' : 'nie',
                            $invoice->injury->vindication == 1 ? 'tak' : 'nie',
                            $invoice->injury->vehicle->program,
                            (!$invoice->invoice_date || $invoice->invoice_date == '0000-00-00') ? '-' : $invoice->invoice_date,

                        ));
                    }
                });
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'.csv"',
        ]);

        return $response;
    }

    public function generateTheads()
    {
        return array(
            'Nr faktury',
            'Typ faktury',
            'Data wprowadzenia faktury',
            'Nazwisko osoby wprowadzającej',
            'Odbiorca faktury',
            'Data zakończenia szkody',
            'Właściciel pojazdu',
            'Serwis',
            'Miasto',
            'Ulica',
            'Warsztat nazwa',
            'Warsztat miasto',
            'Warsztat ulica',

            'Typ warsztatu',
            'NIP warsztatu',
            'Grupa kontrahenta',

            'Typ serwisu przypisanego',
            'Serwis przypisany',
            'NIP serwisu przypisanego',

            'Netto',
            'Nr szkody',
            'Nr szkody ZU',
            'TU',
            'Czy prowizja',
            'Kwota bazowa',
            'Procent',
            'Wartość prowizji',
            'Marka',
            'Model',
            'Nr rejestracyjny',
            'NIP właściciela pojazdu',
            'Wysłano zlecenie do serwisu” TAK/NIE',
            'Rodzaj Usługi',
            'Uprzedni NIP właściciela',
            'Data przekazania',         
            'Data ponownego przekazania',

            'Dostawca pojazdu',
            'Zgłoszenie DSP',
            'Windykacja',
            'Program sprzedaży',
            'Data wystawienia faktury',
        );
    }

    private function valueOfCommission($invoice)
    {
        if($invoice->commission == 1) {
            if ($invoice->branch_id != 0 && $invoice->branch_id != '-1') {
                if( $invoice->branch->company->commission != NULL && $invoice->branch->company->commission != ''){
                    if($invoice->injury_files->category == 3){
                        return $invoice->branch->company->commission * $invoice->base_netto * 0.01;
                    }elseif( $invoice->parent_id != 0 ){
                        $korekta = $invoice->branch->company->commission * $invoice->base_netto * 0.01;
                        $parent = $invoice->branch->company->commission * $invoice->parent->base_netto * 0.01;
                        $diff = $korekta - $parent;
                        return $diff;
                    }else{
                        return $invoice->branch->company->commission * $invoice->base_netto * 0.01;
                    }
                }else{
                    if($invoice->injury_files->category == 4)
                        return 0;
                    else
                        return 0;
                }
            }else{
                return '---';
            }
        }elseif($invoice->injury_files->category == 4 && $invoice->parent_id != 0 && $invoice->parent->commission == 1){
            return 0;
        }else{
            return 0;
        }
    }

    private function generateCommission($invoice)
    {
        if($invoice->branch_id != 0 && $invoice->branch_id != '-1'&& $invoice->branch && $invoice->branch->company && $invoice->branch->company->commission_type_id == 1){
            $base_netto = $this->calculateBaseNetto($invoice);

            $companyCommission = $invoice->branch->company->commissions->first();
            $commission = ($companyCommission->commission / 100) * $base_netto;

            return [
                'commission' => $commission,
                'commission_percentage' => $companyCommission->commission
            ];
        }

        return null;
    }

    private function calculateBaseNetto($invoice)
    {
        if($invoice->injury_files->category == 3){
            return $invoice->base_netto;
        }elseif( $invoice->parent_id != 0 ){
            $korekta = $invoice->base_netto;
            $parent = $invoice->parent->base_netto;
            return $korekta - $parent;
        }

        return $invoice->base_netto;
    }

    private function serviceTypeBranch($invoice)
    {
        $branch = null;

        if( $invoice->branch_id > 0){
            $branch = $invoice->branch;
        }

        if($branch)
        {
            if($branch->company){
                $companyGroups = $branch->company->allGroups;
                $groups = [];
                $invoice_date = $invoice->invoice_date ? $invoice->invoice_date.' 00:00:00' : $invoice->created_at;

                foreach($companyGroups as $companyGroup)
                {
                    if( ($companyGroup->created_at <= $invoice_date && is_null($companyGroup->deleted_at)) || $companyGroup->deleted_at >= $invoice_date){
                        if(! in_array($companyGroup->name, $groups)){
                            $groups[] = $companyGroup->name;
                        }
                    }
                }

                if(count($groups) > 0){
                    return 'Serwis w grupie: '.implode(',', $groups);
                }
            }elseif( $branch->company && $branch->company->groups && $branch->company->groups->count() > 0){
                return 'Serwis w grupie: '.implode(',', $branch->company->groups->lists('name'));
            }

            return 'Serwis inny - poza grupą';
        }

        return '---';
    }

    private function serviceTypeInjuryBranch($invoice)
    {
        $this->branch = null;
        $branch = null;
        $injuryBranch = null;
        if($invoice->injury->branches->count() > 0){
            $invoice_date = $invoice->invoice_date ? $invoice->invoice_date.' 00:00:00' : $invoice->created_at;
            $injuryBranch = $invoice->injury->branches()->where('created_at', '<=', $invoice_date)->orderBy('created_at', 'desc')->first();
            if ($injuryBranch) {
                $branch = $injuryBranch->branch;
            }
        }

        if($branch)
        {
            $this->branch = $branch;
            if($injuryBranch && $branch->company){
                $companyGroups = $branch->company->allGroups;
                $groups = [];
                foreach($companyGroups as $companyGroup)
                {
                    if( ($companyGroup->created_at <= $injuryBranch->created_at && is_null($companyGroup->deleted_at)) || $companyGroup->deleted_at >= $injuryBranch->created_at){
                        if(! in_array($companyGroup->name, $groups)){
                            $groups[] = $companyGroup->name;
                        }
                    }
                }

                if(count($groups) > 0){
                    return 'Serwis w grupie: '.implode(',', $groups);
                }
            }elseif( $branch->company && $branch->company->groups && $branch->company->groups->count() > 0){
                return 'Serwis w grupie: '.implode(',', $branch->company->groups->lists('name'));
            }

            return 'Serwis inny - poza grupą';
        }

        return '---';
    }

}
