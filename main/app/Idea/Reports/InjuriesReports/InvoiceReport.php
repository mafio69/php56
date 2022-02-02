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

class InvoiceReport extends BaseReport implements ReportsInterface, ReportsCsvInterface {

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

        Excel::create($this->filename, function($excel) {
            $excel->sheet('Raport faktur', function($sheet) {

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $sheet->appendRow(array('Raport faktur '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                InjuryInvoices::where('active','=','0')->where('step' , '!=' , '-10')->whereBetween('created_at', array( $date_from, $date_to ) )
                    ->with('injury', 'injury_files', 'injury_files.user', 'invoicereceive', 'injury.branch', 'injury.branch.company', 'injury.branch.company.groups', 'injury.vehicle.insurance_company', 'injury.vehicle', 'injury.vehicle.owner','injury.vehicle.owner.nip', 'serviceType')
                    ->chunk(200, function($invoices) use (&$sheet) {

                        $filesA = InjuryFiles::whereActive(0)->whereType(3)->where(function($query){
                            $query->whereIn('category', [6,49,60]);
                        })->whereIn('injury_id', $invoices->lists('injury_id'))->get();

                        $filesInjuryA = array();
                        foreach ($filesA as $file) {
                            if(!isset($filesInjuryA[$file->injury_id]))
                                $filesInjuryA[$file->injury_id] = $file;
                        }

	                    $limit_date_01022018 = Carbon::createFromFormat('Y-m-d', '2018-02-01');

                        foreach ($invoices as $k => $invoice) {
                            $sheet->appendRow(array(
                                $invoice->invoice_nr,
                                ( $invoice->injury_files->category == 3) ? Config::get('definition.fileCategory.3') : Config::get('definition.fileCategory.4'),
                                substr($invoice->created_at, 0, -3),
                                $invoice->injury_files->user->name,
                                $invoice->injury->date_end ? substr($invoice->injury->date_end, 0, -3) : '',
                                $invoice->injury->vehicle->owner->name,
                                ($invoice->branch_id != 0 && $invoice->branch_id != '-1' ) ? $invoice->branch->short_name : '---',
                                ($invoice->branch_id != 0 && $invoice->branch_id != '-1' ) ? $invoice->branch->city : '---',
                                ($invoice->branch_id != 0 && $invoice->branch_id != '-1' ) ? $invoice->branch->street : '---',
                                $this->serviceType($invoice->injury),
                                ($invoice->branch_id != 0 && $invoice->branch_id != '-1' && $invoice->branch->company) ? $invoice->branch->company->nip : '---',
                                ($invoice->netto == 0) ? '---' : $invoice->netto,
                                $invoice->injury->case_nr,
                                ($invoice->injury->injury_nr != '' ) ? $invoice->injury->injury_nr : '---',
                                ($invoice->injury->vehicle->insurance_company_id != 0) ? $invoice->injury->vehicle->insurance_company()->first()->name : '---',
                                ($invoice->commission == 1) ? 'tak' : 'nie',
                                ($invoice->commission == 1) ? $invoice->base_netto : 0,
                                ($invoice->branch_id != 0 && $invoice->branch_id != '-1' && $invoice->branch->company) ? ($invoice->branch->company->commission / 100) : '---',
                                $this->valueOfCommission($invoice),
                                checkObjectIfNotNull($invoice->injury->vehicle->brand, 'name', $invoice->injury->vehicle->brand),
                                checkObjectIfNotNull($invoice->injury->vehicle->model, 'name', $invoice->injury->vehicle->model),
                                $invoice->injury->vehicle->registration,
                                ($invoice->injury->vehicle->owner->nip->count() > 0) ?  $invoice->injury->vehicle->owner->nip->first()->value : '',
                                (isset($filesInjuryA[$invoice->injury_id])) ? 'tak' : 'nie',
                                ($invoice->serviceType) ? $invoice->serviceType->name : '',
	                            $invoice->injury->vehicle->owner->old_nip
                            ));
                        }
                    });
            });

        })->export('xls');
    }

    public function generateTheads()
    {
        return array(
            'Nr faktury',
            'Typ faktury',
            'Data wprowadzenia faktury',
            'Nazwisko osoby wprowadzającej',
            'Data zakończenia szkody',
            'Właściciel pojazdu',
            'Serwis',
            'Miasto',
            'Ulica',
            'Typ warsztatu',
            'NIP warsztatu',
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
	        'Uprzedni NIP właściciela'
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

}