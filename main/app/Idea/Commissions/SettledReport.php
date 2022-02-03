<?php

namespace Idea\Commissions;


use CommissionReport;

class SettledReport
{
    /**
     * @var CommissionReport
     */
    private $report;

    /**
     * SettledReport constructor.
     */
    public function __construct($report_id)
    {

        $this->report = CommissionReport::find($report_id);
    }

    public function generate()
    {
        $filename = 'raport_prowizji_rozliczonych_'.date('Y_m_d_h_i_s');

        \Excel::create($filename, function ($excel){
            $excel->sheet('raport', function($sheet){
                $sheet->appendRow([
                    'LP',
                    'nr faktury',
                    'nr szkody',
                    'kwota netto',
                    'prowizja',
                    'właściciel pojazdu',
                    'NIP właściciela',
                    'serwis'
                ]);

                $row = 1;

                $this->report->commissions()->chunk(200, function($commissions) use(&$sheet, &$row){
                     $commissions->load('invoice', 'invoice.injury.vehicle.owner', 'company');

                     foreach($commissions as $commission)
                     {
                         $sheet->appendRow([
                             $row,
                             $commission->invoice->invoice_nr,
                             $commission->invoice->injury->injury_nr,
                             $commission->invoice->netto,
                             $commission->commission,
                             $commission->invoice->injury->vehicle->owner->name,
                             $commission->invoice->injury->vehicle->owner->nip->first()->value,
                             $commission->company->name
                         ]);
                         $row++;
                     }
                });
            });
        })->store('xlsx', \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/commissions', true);

        return $filename.'.xlsx';
    }
}
