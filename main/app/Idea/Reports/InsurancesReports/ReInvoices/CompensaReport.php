<?php

namespace Idea\Reports\InsurancesReports\ReInvoices;


use Idea\Reports\ReportsInterface;
use LeasingAgreementInsurance;

class CompensaReport implements ReportsInterface{

    private $params;
    private $date;
    private $notification_number;

    private $amountsNew = array();
    private $amountsResumeLeasing = array();
    private $amountsResumeLoan = array();
    private $amountsResume2Months = array();
    private $last_report = null;
    private $version = null;
    private $filename = '';

    private $borderStyleSmall = array(
        'borders' => array(
            'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN
            )
        ),
        'font'  => array(
            'size'  => 8,
            'name'  => 'Arial'
        )
    );

    private $sumStyleSmall = array(
        'fill' => array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'C0C0C0')
        ),
        'font'  => array(
            'size'  => 8,
            'name'  => 'Arial',
            'bold'  => true
        )
    );

    function __construct($params = array())
    {
        $this->params = $params;
        $this->date = \Date::createFromFormat('Y-m-d', $params['date'].'-01');
        $this->parseNotification();
    }

    public function generateReport()
    {
        set_time_limit(1000);
        \DB::disableQueryLog();
        \Session::set('avoid_query_logging', true);
        $template = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/COMPENSA_reinvoice_report.xlsx';

        $insuranceCompany = \Insurance_companies::find($this->params['insurance_company_id']);

        $date = $this->date->format('Y_m');
        $filename = 'Księgowość_'.$date.'_'.$insuranceCompany->name;

        if($this->params['if_trial'] == 1)
        {
            $filename .= '_próbny';
        }elseif(!is_null($this->version))
        {
            $filename .= '_'.$this->version;
        }

        $filename .= '-'.time();
        $this->filename = $filename;

        $filePath = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/reports/generated';

        \CustomLog::info('reports', 'CompensaReport '.$filename, [$this->params, $this->notification_number]);

        \Excel::load($template, function($excel)  {
            $this->generateNewSheet($excel);
            $this->generateEOSheet($excel);
            $this->generateResumeLeasingSheet($excel);
            $this->generateResumeLoanSheet($excel);
            $this->generateResume2MonthsSheet($excel);

            $this->createReportEntry();
        })->setFileName($filename)->store('xls', $filePath)->download('xls');

        \Session::set('avoid_query_logging', false);
    }

    /**
     * generowanie arkusza NOWE
     * @param $excel
     */
    private function generateNewSheet($excel)
    {
        $excel->setActiveSheetIndex(0);
        $newSheet = $excel->getActiveSheet();

        $date = strtolower($this->date->format('M-y'));
        $newSheet->setCellValue('A8', $date);

        $row = 17;
        LeasingAgreementInsurance::where('insurance_company_id',$this->params['insurance_company_id'])
            ->where(function($query){
                if($this->params['refunds_type'] == 1)
                {
                    $query->where('date_from', '>=', '2015-01-20');
                }else{
                    $query->where('date_from', '<', '2015-01-20');
                }

                if($this->params['if_foreign_policy'] == 0)
                {
                    $query->where('if_foreign_policy', 0);
                }else{
                    $query->where('if_foreign_policy', 1);
                }
            })
            ->where('notification_number', $this->notification_number )

            ->where('if_continuation', 0)
            ->where('if_cession', 0)
            ->where('if_refund_contribution', 0)

            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->whereHas('leasingAgreement', function($query){
                $query->whereNull('withdraw');
                //$query->has('insurances', '=', 1);
                $query->where('leasing_agreement_type_id', 2);
                $query->where('owner_id', $this->params['owner_id']);
                $query->where('has_yacht', '=', '0');
                if($this->params['if_sk'] == 1){
                    $query->where(function($query){
                        $query->where('nr_contract', 'like', '%/SK')
                            ->orWhere('nr_contract', 'like', '%/SK/%');
                    });
                }elseif($this->params['if_sk'] == 2){
                    $query->where('nr_contract', 'not like', '%/SK')
                        ->where('nr_contract', 'not like', '%/SK/%');
                }
            })->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$newSheet, &$row){

                foreach ($insurances as $k => $insurance) {
                    $rowsToInsert = array();
                    $count_rows = 0;
                    $agreement = $insurance->leasingAgreement;
                    foreach ($agreement->objects as $k2 => $object) {
                        $count_rows++;
                        if($k2 == 0)
                        {
                            $amounts = [
                                ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value,
                                ($agreement->net_gross == 1) ? 'netto' : 'brutto',
                                $insurance->rate,
                                $insurance->contribution,
                            ];
                        }else{
                            $amounts = ['', '', '', ''];
                        }

                        $rowsToInsert[$k.$k2] = [
                            $object->name,
                            $agreement->nr_contract,
                        ];
                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $insurance->rate_lessor
                        );

                        $remarks = [];
                        if($insurance->leasing_agreement_payment_way_id == 2) {
                            array_push($rowsToInsert[$k . $k2],
                                $insurance->contribution_lessor,
                                '',
                                '',
                                '',
                                '',
                                ''
                            );
                            if(! $insurance->leasingAgreement->if_reportable == 0) $remarks[] = 'Jednorazowo';
                        }elseif(!in_array($insurance->leasing_agreement_insurance_type_id, [1,2]) || $agreement->months < 12){
                            array_push($rowsToInsert[$k . $k2],
                                $insurance->contribution_lessor,
                                '',
                                '',
                                '',
                                '',
                                ''
                            );
                        }else{
                            $times_ceil = ceil($agreement->months / 12 );
                            $times_floor = floor($agreement->months / 12);

                            for($i = 0; $i < $times_floor; $i++)
                                array_push($rowsToInsert[$k.$k2], $insurance->contribution_lessor);

                            if($times_ceil != $times_floor)
                                array_push($rowsToInsert[$k.$k2], $insurance->last_year_lessor_contribution);

                            $left = 7 - $times_ceil - 1;
                            for($i = 0; $i < $left; $i++)
                                array_push($rowsToInsert[$k.$k2], '');
                        }

                        if($insurance->leasingAgreement->if_reportable == 0) $remarks[] = 'nie refakturować – ubezpieczenie w ratach';
                        array_push($rowsToInsert[$k.$k2], implode('; ', $remarks));
                    }
                    $newSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    for($i = 'C'; $i <= 'Q'; $i++)
                        $newSheet->mergeCells($i.$row.":".$i.($row+$count_rows-1));

                    $row+=$count_rows;
                }

            });

        $newSheet->getStyle('A17:Q'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $newSheet->getStyle('A17:Q'.($row-1))->applyFromArray($this->borderStyleSmall);

        $row+=3;
        $newSheet->setCellValue('A'.$row, 'POŻYCZKA');
        $newSheet->getStyle('A'.$row)->applyFromArray($this->sumStyleSmall);
        $row++;
        $startingRow = $row;
        LeasingAgreementInsurance::where('insurance_company_id',$this->params['insurance_company_id'])
            ->where('active', 1)
            ->where(function($query){
                if($this->params['refunds_type'] == 1)
                {
                    $query->where('date_from', '>=', '2015-01-20');
                }else{
                    $query->where('date_from', '<', '2015-01-20');
                }
            })
            ->where('notification_number', $this->notification_number )
            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->whereHas('leasingAgreement', function($query){
                $query->whereNull('withdraw');
                $query->has('insurances', '=', 1);
                $query->where('leasing_agreement_type_id', 1);
                $query->where('owner_id', $this->params['owner_id']);
                if($this->params['if_sk'] == 1){
                    $query->where(function($query){
                        $query->where('nr_contract', 'like', '%/SK')
                            ->orWhere('nr_contract', 'like', '%/SK/%');
                    });
                }elseif($this->params['if_sk'] == 2){
                    $query->where('nr_contract', 'not like', '%/SK')
                        ->where('nr_contract', 'not like', '%/SK/%');
                }
            })->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$newSheet, &$row){

                foreach ($insurances as $k => $insurance) {
                    $rowsToInsert = array();
                    $count_rows = 0;
                    $agreement = $insurance->leasingAgreement;
                    foreach ($agreement->objects as $k2 => $object) {
                        $count_rows++;
                        if($k2 == 0)
                        {
                            $amounts = [
                                ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value,
                                ($agreement->net_gross == 1) ? 'netto' : 'brutto',
                                $insurance->rate,
                                $insurance->contribution,
                            ];
                        }else{
                            $amounts = ['', '', '', ''];
                        }

                        $rowsToInsert[$k.$k2] = [
                            $object->name,
                            $agreement->nr_contract,
                        ];
                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $insurance->rate_lessor
                        );

                        $remarks = [];
                        if($insurance->leasing_agreement_payment_way_id == 2)
                        {
                            array_push($rowsToInsert[$k.$k2],
                                $insurance->contribution_lessor,
                                '',
                                '',
                                '',
                                '',
                                ''
                            );

                            if(! $insurance->leasingAgreement->if_reportable == 0) $remarks[] = 'Jednorazowo';
                        }elseif(!in_array($insurance->leasing_agreement_insurance_type_id, [1,2]) || $agreement->months < 12){
                            array_push($rowsToInsert[$k . $k2],
                                $insurance->contribution_lessor,
                                '',
                                '',
                                '',
                                '',
                                ''
                            );
                        }else{
                            $times_ceil = ceil($agreement->months / 12 );
                            $times_floor = floor($agreement->months / 12);

                            for($i = 0; $i < $times_floor; $i++)
                                array_push($rowsToInsert[$k.$k2], $insurance->contribution_lessor);

                            if($times_ceil != $times_floor)
                                array_push($rowsToInsert[$k.$k2], $insurance->last_year_lessor_contribution);

                            $left = 7 - $times_ceil - 1;
                            for($i = 0; $i < $left; $i++)
                                array_push($rowsToInsert[$k.$k2], '');

                        }

                        if($insurance->leasingAgreement->if_reportable == 0) $remarks[] = 'nie refakturować – ubezpieczenie w ratach';

                        array_push($rowsToInsert[$k.$k2], implode('; ', $remarks));
                    }
                    $newSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    for($i = 'C'; $i <= 'Q'; $i++)
                        $newSheet->mergeCells($i.$row.":".$i.($row+$count_rows-1));

                    $row+=$count_rows;
                }

            });

        $newSheet->getStyle('A'.$startingRow.':Q'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $newSheet->getStyle('A'.$startingRow.':Q'.($row-1))->applyFromArray($this->borderStyleSmall);
    }

    /**
     * generowanie arkusza E&O
     * @param $excel
     */
    private function generateEOSheet($excel)
    {
        $excel->setActiveSheetIndex(1);
        $EOSheet = $excel->getActiveSheet();

        $date = strtolower($this->date->format('M-y'));
        $EOSheet->setCellValue('A9', $date);

        $date = strtoupper($this->date->format('F Y'));
        $EOSheet->setCellValue('G11', $date);
    }

    /**
     * generowanie arkusza WZNOW
     * @param $excel
     */
    private function generateResumeLeasingSheet($excel)
    {
        $excel->setActiveSheetIndex(2);
        $resumeLeasingSheet = $excel->getActiveSheet();

        $date = strtolower($this->date->format('M-y'));
        $resumeLeasingSheet->setCellValue('A9', $date);

        $date = strtoupper($this->date->format('F Y'));
        $resumeLeasingSheet->setCellValue('B11', 'ZESTAWIENIE MIENIA OBJĘTEGO OCHRONĄ UBEZPIECZENIOWĄ: UMOWY WZNOWIONE Z DECYZJĄ OBCIĄŻENIA ZA MIESIĄC: '.$date);

        $row = 18;

        LeasingAgreementInsurance::where('insurance_company_id',$this->params['insurance_company_id'])
            ->where(function($query){
                if($this->params['refunds_type'] == 1)
                {
                    $query->where('date_from', '>=', '2015-01-20');
                }else{
                    $query->where('date_from', '<', '2015-01-20');
                }

                if($this->params['if_foreign_policy'] == 0)
                {
                    $query->where('if_foreign_policy', 0);
                }else{
                    $query->where('if_foreign_policy', 1);
                }
            })
            ->where('notification_number', $this->notification_number )
            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->whereIn('leasing_agreement_insurance_type_id', [1,2,14])

            ->where('if_refund_contribution', 0)
            ->where('if_cession', 0)
            ->where('if_continuation', 1)

            ->whereHas('leasingAgreement', function($query){
                $query->whereNull('withdraw');
                $query->has('insurances', '>', 1);
                $query->where('leasing_agreement_type_id', 2);
                $query->where('owner_id', $this->params['owner_id']);
                $query->where('has_yacht', '=', '0');
                if($this->params['if_sk'] == 1){
                    $query->where(function($query){
                        $query->where('nr_contract', 'like', '%/SK')
                            ->orWhere('nr_contract', 'like', '%/SK/%');
                    });
                }elseif($this->params['if_sk'] == 2){
                    $query->where('nr_contract', 'not like', '%/SK')
                        ->where('nr_contract', 'not like', '%/SK/%');
                }
            })->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$resumeLeasingSheet, &$row){
                foreach ($insurances as $k => $insurance) {
                    $rowsToInsert = array();
                    $count_rows = 0;
                    $agreement = $insurance->leasingAgreement;
                    foreach ($agreement->objects as $k2 => $object) {
                        $remarks = [];
                        $count_rows++;
                        if($k2 == 0)
                        {
                            $amounts = [
                                ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value,
                                ($agreement->net_gross == 1) ? 'netto' : 'brutto',
                                $insurance->rate,
                                $insurance->contribution,
                            ];
                        }else{
                            $amounts = ['', '', '', ''];
                        }

                        $rowsToInsert[$k.$k2] = [
                                $object->name,
                                $agreement->nr_contract,
                                $agreement->client->name,
                                $agreement->client->registry_street . ' ' . $agreement->client->registry_post . ' ' . $agreement->client->registry_city,
                                $agreement->client->NIP,
                                ''
                            ];
                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                                $insurance->date_from,
                                $insurance->date_to,
                                $insurance->months,
                                $this->getRateName($agreement)
                            );

                        if($insurance->if_load_decision == 1) $remarks[] = 'Była dyspozycja obciążenia';
                        if($insurance->leasingAgreement->if_reportable == 0) $remarks[] = 'nie refakturować – ubezpieczenie w ratach';
                        array_push($rowsToInsert[$k.$k2], implode('; ', $remarks));
                    }
                    $resumeLeasingSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsResumeLeasing = $this->calculateAmounts($this->amountsResumeLeasing, $agreement, $insurance);
                }
            });

        $resumeLeasingSheet->getStyle('A18:O'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $resumeLeasingSheet->getStyle('A18:O'.($row-1))->applyFromArray($this->borderStyleSmall);

        $fromSumRow = $row+1;
        foreach($this->amountsResumeLeasing as $rateType => $values)
        {
            $row++;
            $resumeLeasingSheet->setCellValue('A'.$row, $rateType);
            $resumeLeasingSheet->setCellValue('G'.$row, $values['loan']);
            $resumeLeasingSheet->setCellValue('J'.$row, $values['contribution']);
        }

        $resumeLeasingSheet->getStyle('A'.$fromSumRow.':N'.$row)->applyFromArray($this->sumStyleSmall);

        $row++;
        $row++;

        $resumeLeasingSheet->setCellValue('G'.$row, '=SUM(G'.$fromSumRow.':G'.($row-1).')');
        $resumeLeasingSheet->setCellValue('J'.$row, '=SUM(J'.$fromSumRow.':J'.($row-1).')');

        $resumeLeasingSheet->getStyle('G'.$fromSumRow.':J'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $resumeLeasingSheet->getStyle('G'.$row)->applyFromArray($this->sumStyleSmall);
        $resumeLeasingSheet->getStyle('J'.$row)->applyFromArray($this->sumStyleSmall);
    }

    /**
     * generowanie arkusza WZNO POZYCZKA
     * @param $excel
     */
    private function generateResumeLoanSheet($excel)
    {
        $excel->setActiveSheetIndex(3);
        $resumeLoanSheet = $excel->getActiveSheet();

        $date = strtolower($this->date->format('M-y'));
        $resumeLoanSheet->setCellValue('A9', $date);

        $date = strtoupper($this->date->format('F Y'));
        $title = "ZESTAWIENIE MIENIA OBJĘTEGO OCHRONĄ UBEZPIECZENIOWĄ: UMOWY WZNOWIONE Z DECYZJĄ OBCIĄŻENIA ZA MIESIĄC: ".$date;
        $resumeLoanSheet->setCellValue('B11', $title);

        $row = 18;

        LeasingAgreementInsurance::where('insurance_company_id',$this->params['insurance_company_id'])
            ->where(function($query){
                if($this->params['refunds_type'] == 1)
                {
                    $query->where('date_from', '>=', '2015-01-20');
                }else{
                    $query->where('date_from', '<', '2015-01-20');
                }

                if($this->params['if_foreign_policy'] == 0)
                {
                    $query->where('if_foreign_policy', 0);
                }else{
                    $query->where('if_foreign_policy', 1);
                }
            })
            ->where('notification_number', $this->notification_number )
            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->whereIn('leasing_agreement_insurance_type_id', [1,2,14])

            ->where('if_refund_contribution', 0)
            ->where('if_cession', 0)
            ->where('if_continuation', 1)

            ->whereHas('leasingAgreement', function($query){
                $query->whereNull('withdraw');
                $query->has('insurances', '>', 1);
                $query->where('leasing_agreement_type_id', 1);
                $query->where('owner_id', $this->params['owner_id']);
                $query->where('has_yacht', '=', '0');
                if($this->params['if_sk'] == 1){
                    $query->where(function($query){
                        $query->where('nr_contract', 'like', '%/SK')
                            ->orWhere('nr_contract', 'like', '%/SK/%');
                    });
                }elseif($this->params['if_sk'] == 2){
                    $query->where('nr_contract', 'not like', '%/SK')
                        ->where('nr_contract', 'not like', '%/SK/%');
                }
            })->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$resumeLoanSheet, &$row){
                foreach ($insurances as $k => $insurance) {
                    $rowsToInsert = array();
                    $count_rows = 0;
                    $agreement = $insurance->leasingAgreement;
                    foreach ($agreement->objects as $k2 => $object) {
                        $remarks = [];
                        $count_rows++;
                        if($k2 == 0)
                        {
                            $amounts = [
                                ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value,
                                ($agreement->net_gross == 1) ? 'netto' : 'brutto',
                                $insurance->rate,
                                $insurance->contribution,
                            ];
                        }else{
                            $amounts = ['', '', '', ''];
                        }

                        $rowsToInsert[$k.$k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street . ' ' . $agreement->client->registry_post . ' ' . $agreement->client->registry_city,
                            $agreement->client->NIP,
                            '',
                        );
                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->getRateName($agreement)
                        );

                        if($insurance->if_load_decision == 1)  $remarks[] =  'Była dyspozycja obciążenia' ;
                        if($insurance->leasingAgreement->if_reportable == 0) $remarks[] = 'nie refakturować – ubezpieczenie w ratach';
                        array_push($rowsToInsert[$k.$k2], implode('; ', $remarks));
                    }
                    $resumeLoanSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsResumeLoan = $this->calculateAmounts($this->amountsResumeLoan, $agreement, $insurance);
                }
            });

        $resumeLoanSheet->getStyle('A18:O'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $resumeLoanSheet->getStyle('A18:O'.($row-1))->applyFromArray($this->borderStyleSmall);

        $fromSumRow = $row+1;
        foreach($this->amountsResumeLoan as $rateType => $values)
        {
            $row++;
            $resumeLoanSheet->setCellValue('A'.$row, $rateType);
            $resumeLoanSheet->setCellValue('G'.$row, $values['loan']);
            $resumeLoanSheet->setCellValue('J'.$row, $values['contribution']);
        }

        $resumeLoanSheet->getStyle('A'.$fromSumRow.':O'.$row)->applyFromArray($this->sumStyleSmall);

        $row++;
        $row++;

        $resumeLoanSheet->setCellValue('G'.$row, '=SUM(G'.$fromSumRow.':G'.($row-1).')');
        $resumeLoanSheet->setCellValue('J'.$row, '=SUM(J'.$fromSumRow.':J'.($row-1).')');

        $resumeLoanSheet->getStyle('G'.$fromSumRow.':J'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $resumeLoanSheet->getStyle('G'.$row)->applyFromArray($this->sumStyleSmall);
        $resumeLoanSheet->getStyle('J'.$row)->applyFromArray($this->sumStyleSmall);
    }

    /**
     * generowanie arkusza WZNOW NA 2 MCE
     * @param $excel
     */
    private function generateResume2MonthsSheet($excel)
    {
        $excel->setActiveSheetIndex(4);
        $resume2MonthsSheet = $excel->getActiveSheet();

        $date = strtolower($this->date->format('M-y'));
        $resume2MonthsSheet->setCellValue('A9', $date);

        $date = strtoupper($this->date->format('F Y'));
        $resume2MonthsSheet->setCellValue('B11', 'ZESTAWIENIE MIENIA OBJĘTEGO OCHRONĄ UBEZPIECZENIOWĄ: UMOWY WZNOWIONE Z DECYZJĄ OBCIĄŻENIA ZA MIESIĄC: '.$date);

        $row = 18;
        LeasingAgreementInsurance::where('insurance_company_id',$this->params['insurance_company_id'])
            ->where(function($query){
                if($this->params['refunds_type'] == 1)
                {
                    $query->where('date_from', '>=', '2015-01-20');
                }else{
                    $query->where('date_from', '<', '2015-01-20');
                }

                if($this->params['if_foreign_policy'] == 0)
                {
                    $query->where('if_foreign_policy', 0);
                }else{
                    $query->where('if_foreign_policy', 1);
                }
            })
            ->where('notification_number', $this->notification_number )
            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->whereIn('leasing_agreement_insurance_type_id', [7])

            ->where('if_refund_contribution', 0)
            ->where('if_cession', 0)
            ->where('if_continuation', 1)

            ->whereHas('leasingAgreement', function($query) {
                $query->whereNull('withdraw');
                $query->has('insurances', '>', 1);
                $query->where('owner_id', $this->params['owner_id']);
                $query->where('has_yacht', '=', '0');
                if($this->params['if_sk'] == 1){
                    $query->where(function($query){
                        $query->where('nr_contract', 'like', '%/SK')
                            ->orWhere('nr_contract', 'like', '%/SK/%');
                    });
                }elseif($this->params['if_sk'] == 2){
                    $query->where('nr_contract', 'not like', '%/SK')
                        ->where('nr_contract', 'not like', '%/SK/%');
                }
            })
            ->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$resume2MonthsSheet, &$row){
                foreach ($insurances as $k => $insurance) {
                    $rowsToInsert = array();
                    $count_rows = 0;
                    $agreement = $insurance->leasingAgreement;
                    foreach ($agreement->objects as $k2 => $object) {
                        $remarks = [];
                        $count_rows++;
                        if($k2 == 0)
                        {
                            $amounts = [
                                ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value,
                                ($agreement->net_gross == 1) ? 'netto' : 'brutto',
                                valueIfNotNull($insurance->rate,  0),
                                valueIfNotNull($insurance->contribution,  0)
                            ];
                        }else{
                            $amounts = ['', '', '', ''];
                        }

                        $rowsToInsert[$k . $k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street.' '.$agreement->client->registry_post.' '.$agreement->client->registry_city,
                            $agreement->client->NIP,
                            '',
                        );

                        foreach($amounts as $amount) {
                            array_push($rowsToInsert[$k . $k2], $amount);
                        }

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->getRateName($agreement)
                        );

                        if($insurance->if_load_decision == 1)  $remarks[] =  'Była dyspozycja obciążenia' ;
                        if($insurance->leasingAgreement->if_reportable == 0) $remarks[] = 'nie refakturować – ubezpieczenie w ratach';
                        array_push($rowsToInsert[$k.$k2], implode('; ', $remarks));
                    }
                    $resume2MonthsSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsResume2Months = $this->calculateAmounts($this->amountsResume2Months, $agreement, $insurance);
                }
            });

        $resume2MonthsSheet->getStyle('A18:O'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $resume2MonthsSheet->getStyle('A18:O'.($row-1))->applyFromArray($this->borderStyleSmall);

        $fromSumRow = $row+1;
        foreach($this->amountsResume2Months as $rateType => $values)
        {
            $row++;
            $resume2MonthsSheet->setCellValue('A'.$row, $rateType);
            $resume2MonthsSheet->setCellValue('G'.$row, $values['loan']);
            $resume2MonthsSheet->setCellValue('J'.$row, $values['contribution']);
        }

        $resume2MonthsSheet->getStyle('A'.$fromSumRow.':N'.$row)->applyFromArray($this->sumStyleSmall);

        $row++;
        $row++;

        $resume2MonthsSheet->setCellValue('G'.$row, '=SUM(G'.$fromSumRow.':G'.($row-1).')');
        $resume2MonthsSheet->setCellValue('J'.$row, '=SUM(J'.$fromSumRow.':J'.($row-1).')');

        $resume2MonthsSheet->getStyle('G'.$fromSumRow.':J'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $resume2MonthsSheet->getStyle('G'.$row)->applyFromArray($this->sumStyleSmall);
        $resume2MonthsSheet->getStyle('J'.$row)->applyFromArray($this->sumStyleSmall);
    }

    private function getRateName($agreement)
    {
        if($agreement->insurance_group_row && $agreement->insurance_group_row->rate)
            return $agreement->insurance_group_row->rate->name;

        return '';
    }

    private function calculateAmounts($amounts, $agreement, $insurance)
    {
        if ($agreement->insurance_group_row && $agreement->insurance_group_row->rate)
        {
            if (!isset($amounts[$agreement->insurance_group_row->rate->name])) {
                $amounts[$agreement->insurance_group_row->rate->name]['loan'] = 0;
                $amounts[$agreement->insurance_group_row->rate->name]['contribution'] = 0;
            }
            $amounts[$agreement->insurance_group_row->rate->name]['loan'] += ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value;
            $amounts[$agreement->insurance_group_row->rate->name]['contribution'] += $insurance->contribution;
        }else{
            if (!isset($amounts['---'])) {
                $amounts['---']['loan'] = 0;
                $amounts['---']['contribution'] = 0;
            }
            $amounts['---']['loan'] += ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value;
            $amounts['---']['contribution'] += $insurance->contribution;
        }
        return $amounts;
    }

    private function parseNotification()
    {
        $notification_number = $this->date->format('m/Y');
        $this->notification_number = $notification_number;
        $last_report_db = \LeasingAgreementReport::where('type', $this->params['report_type'])
            ->where('insurance_company_id', $this->params['insurance_company_id'])
            ->where('insurances_global_nr', $notification_number)
            ->where('owner_id', $this->params['owner_id'])
            ->where('refunds_type_id', $this->params['refunds_type'])
            ->where('if_foreign_policy', $this->params['if_foreign_policy'])
            ->where('if_sk', $this->params['if_sk'])
            ->where('if_trial', 0)->latest()->first();
        if($last_report_db)
        {
            $this->last_report = $last_report_db;

            if(is_null($last_report_db->version))
            {
                $this->version = 'a';
            }else{
                $this->version = chr(ord($last_report_db->version) + 1);
            }
        }
    }

    private function createReportEntry()
    {
        \LeasingAgreementReport::create([
            'type'      => $this->params['report_type'],
            'insurance_company_id'  => $this->params['insurance_company_id'],
            'owner_id'  => $this->params['owner_id'],
            'if_trial'  => $this->params['if_trial'],
            'insurances_global_nr' => $this->notification_number,
            'version'   => $this->version,
            'filename'  => $this->filename,
            'user_id'   => \Auth::user()->id,
            'refunds_type_id' => $this->params['refunds_type'],
            'if_foreign_policy' => $this->params['if_foreign_policy'],
            'if_sk'     =>  $this->params['if_sk']
        ]);
    }
}
