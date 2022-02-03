<?php
namespace Idea\Commissions;


use Config;
use PHPExcel_Style_NumberFormat;

class CompanyReport
{
    private $report;
    private $company;

    /**
     * CompanyReport constructor.
     */
    public function __construct($report_id, $company_id)
    {
        $this->report = \CommissionReport::find($report_id);
        $this->company = \Company::find($company_id);
    }

    public function generate()
    {
        $filename = 'raport_prowizji_serwisu_'.date('Y_m_d_h_i_s');

        \Excel::create($filename, function($excel) {
            $excel->sheet('raport', function ($sheet) {
                $sheet->appendRow([
                    'Nr faktury',
                    'Typ faktury',
                    'Data wprowadzenia faktury',
                    'Właściciel pojazdu',
                    'Serwis',
                    'Miasto',
                    'Ulica',
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
                    'Nr rejestracyjny'
                ]);
                $row = 1;

                $this->report->commissions()->where('company_id', $this->company->id)->chunk(200, function($commissions) use(&$sheet, &$row)
                {
                    $commissions->load('invoice.injury.vehicle.owner', 'invoice.serviceType', 'invoice.injury_files');


                    foreach($commissions as $k => $commission)
                    {
                        $vehicle = $commission->invoice->injury->vehicle;
                        $sheet->appendRow([
                            $commission->invoice->invoice_nr,
                            $commission->invoice->injury_files->category == 4 ? \Config::get('definition.fileCategory.4') : \Config::get('definition.fileCategory.3'),
                            substr($commission->invoice->created_at, 0, -3),
                            $commission->invoice->injury->vehicle->owner ? $commission->invoice->injury->vehicle->owner->name :'',
                            $this->company->name,
                            $this->company->city,
                            $this->company->street,
                            $this->company->nip,
                            $commission->invoice->netto,
                            $commission->invoice->injury->case_nr,
                            $commission->invoice->injury->injury_nr,
                            $commission->invoice->injury->vehicle->insurance_company()->first() ? $commission->invoice->injury->vehicle->insurance_company()->first()->name : '',
                            'tak',
                            $commission->invoice->base_netto,
                            $commission->commission_percentage,
                            $commission->commission,
                            checkObjectIfNotNull($vehicle->brand, 'name', $vehicle->brand),
                            checkObjectIfNotNull($vehicle->model, 'name', $vehicle->model),
                            $commission->invoice->injury->vehicle->registration,
                            $commission->invoice->serviceType ? $commission->invoice->serviceType->name : ''
                        ]);
                        $row++;
                    }


                });

                $sheet
                    ->setCellValue(
                        'O' . ($row+2),
                        'razem'
                    );

                $sheet
                    ->setCellValue(
                        'P' . ($row+2),
                        '=SUM(P2:P'.$row.')'
                    );
                $sheet
                    ->setCellValue(
                        'Q' . ($row+2),
                        'netto'
                    );

                $sheet->getStyle('O2:O'.$row)
                    ->getNumberFormat()->applyFromArray(
                        array(
                            'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
                        )
                    );

            });
        })->store('xlsx', Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/commissions', true);

        return Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/commissions/'.$filename.'.xlsx';
    }
}

