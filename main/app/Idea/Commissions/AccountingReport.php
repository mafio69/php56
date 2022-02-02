<?php

namespace Idea\Commissions;

use CommissionReport;
use Config;
use PHPExcel_Style_NumberFormat;

class AccountingReport
{
    private $report;
    private $commissions;

    public function __construct($report_id)
    {
        $this->report = CommissionReport::find($report_id);
    }

    public function generate()
    {
        $this->prepareData();
        $filename = 'raport_ksiegowy_'.date('Y_m_d_h_i_s');
        \Excel::create($filename, function($excel) {
            $excel->sheet('raport', function ($sheet) {
                $sheet->appendRow([
                    'LP',
                    'Typ faktury',
                    'Data faktury',
                    'Data Sprzedaży',
                    'Forma płatności',
                    'Kod Kontrahenta',
                    'Nazwa kontrahenta',
                    'NIP',
                    'Adres',
                    'Kod towaru',
                    'Opis pozycji (drukowany na fakturze)',
                    'Ilość',
                    'Cena netto',
                    'Wartość netto',
                    'Vat %',
                    'Vat',
                    'Brutto',
                    'Opis dokumentu',
                    'Rejestr'
                ]);
                $row = 1;

                foreach($this->commissions as $companies) {
                    foreach($companies as $commission) {
                        $sheet->appendRow([
                            $row,
                            'FVS',
                            $commission['object']->acceptation_date->format('Y-m-d'),
                            $commission['object']->acceptation_date->subMonth()->lastOfMonth()->format('Y-m-d'),
                            'przelew',
                            '',
                            $commission['object']->company->name,
                            $commission['object']->company->nip,
                            $commission['object']->company->address,
                            '',
                            '',
                            1,
                            $commission['commission'],
                            $commission['commission'],
                            0.23,
                            round(0.23 * $commission['commission']),
                            round(1.23 * $commission['commission']),
                            $commission['object']->acceptation_date->format('m/Y'),
                            'Bank'
                        ]);
                        $row++;
                    }
                }

                $sheet->getStyle('O2:O'.$row)
                    ->getNumberFormat()->applyFromArray(
                        array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                    );

            });
        })->store('xlsx', Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/commissions', true);

        return $filename.'.xlsx';
    }

    private function prepareData()
    {
        $this->report->commissions()->chunk(200, function($commissions) use(&$sheet, &$row)
        {
            $commissions->load('invoice',  'company');

            foreach($commissions as $k => $commission)
            {
                if(! isset($this->commissions[$commission->company_id]) ){
                    $this->commissions[$commission->company_id] = [];
                }

                if(! isset($this->commissions[$commission->company_id][$commission->acceptation_date->format('Y-m-d')]) ){
                    $this->commissions[$commission->company_id][$commission->acceptation_date->format('Y-m-d')] = ['commission' => 0];
                }

                $this->commissions[$commission->company_id][$commission->acceptation_date->format('Y-m-d')] = [
                    'object' => $commission,
                    'commission' =>  $this->commissions[$commission->company_id][$commission->acceptation_date->format('Y-m-d')]['commission'] + $commission->commission
                ];
            }
        });
    }
}
