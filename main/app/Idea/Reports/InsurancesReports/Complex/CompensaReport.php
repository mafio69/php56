<?php

namespace Idea\Reports\InsurancesReports\Complex;

use Idea\Reports\ReportsInterface;
use LeasingAgreementInsurance;

class CompensaReport implements ReportsInterface{

    private $params;
    private $date;
    private $notification_number;
    private $owner;
    private $headerStyle = array(
        'fill' => array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'C0C0C0')
        ),
        'font'  => array(
            'size'  => 10,
            'name'  => 'Arial',
            'bold'  => true
        )
    );

    private $borderStyle = array(
        'borders' => array(
            'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN
            )
        )
    );
    private $amountsNewLeasing = array();
    private $amountsNewLoan = array();
    private $amountsResumeLeasing = array();
    private $amountsResumeLoan = array();
    private $amountsResume2Months = array();
    private $amountRefundContribution = 0;
    private $amountAll = ['loan' => 0, 'contribution' => 0];
    private $last_report = null;
    private $version = null;
    private $filename = '';

    function __construct($params = array())
    {
        $this->params = $params;
        $this->date = \Date::createFromFormat('Y-m-d', $params['date'].'-01');

        $this->parseNotification();

        $owner = \Owners::find($this->params['owner_id']);
        $this->owner = $owner->name;
    }


    /**
     * @return mixed
     */
    public function generateReport()
    {
        set_time_limit(1000);
        \DB::disableQueryLog();
        \Session::set('avoid_query_logging', true);
        $template = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/COMPENSA_report.xls';

        $date = $this->date->format('Y_m');

        $filename = $date.'_COMPENSA';
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
            $this->generateNewLeasingSheet($excel);
            $this->generateNewLoanSheet($excel);
            $this->generateResumeLeasingSheet($excel);
            $this->generateResumeLoanSheet($excel);
            $this->generateResume2MonthsSheet($excel);
            $this->generateCorrectionSheet($excel);
            $this->generateResumeContributionSheet($excel);
            $this->generateDataChangeSheet($excel);
            $this->generateEOSheet($excel);
            $this->generateAccountancySheet($excel);

            $this->createReportEntry();
        })->setFileName($filename)->store('xls', $filePath)->download('xls');

        \Session::set('avoid_query_logging', false);
    }


    /**
     * Generowanie arkusza KSIEGOWOSC
     * @param $excel
     */
    private function generateAccountancySheet($excel)
    {
        $excel->setActiveSheetIndex(0);
        $accountancySheet = $excel->getActiveSheet();

        $date = strtoupper($this->date->format('F Y'));
        $title = "ZESTAWIENIE UMÓW NOWYCH ZAWARTYCH W MIESIĄCU ".$date." W RAMACH UG Z COMPENSA Towarzystwo Ubezpieczeń S.A. Vienna Insurance Group";
        $accountancySheet->setCellValue('A3', $title);

        $row = 6;
        //umowy nowe leasing

        $formattingFromRow = $row;
        $lp = 1;
        if(count($this->amountsNewLeasing) > 0) {
            foreach ($this->amountsNewLeasing as $rateType => $values) {
                $accountancySheet->fromArray([
                    $lp++,
                    'UMOWY NOWE ' . $date,
                    $rateType,
                    $values['loan'],
                    $values['contribution'],
                ], null, 'A' . $row);
                $row++;
            }
        }else{
            $accountancySheet->fromArray([
                $lp,
                'UMOWY NOWE ' . $date,
                '',
                '',
                '',
            ], null, 'A' . $row);
            $row++;
        }

        $accountancySheet->mergeCells('B' . $formattingFromRow . ':B' . ($row - 1))
            ->getStyle('B' . $formattingFromRow)->getAlignment()->setWrapText(true);
        $accountancySheet->setCellValue('C' . $row, 'razem')
            ->getStyle('C' . $row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('D' . $row, '=SUM(D' . $formattingFromRow . ':D' . ($row - 1) . ')');
        $accountancySheet->setCellValue('E' . $row, '=SUM(E' . $formattingFromRow . ':E' . ($row - 1) . ')');
        $accountancySheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A' . $formattingFromRow . ':E' . $row)->applyFromArray($this->borderStyle);
        $row++;

        //umowy nowe pożyczka
        $formattingFromRow = $row;
        $lp = 1;
        if(count($this->amountsNewLoan) > 0) {
            foreach ($this->amountsNewLoan as $rateType => $values) {
                $accountancySheet->fromArray([
                    $lp++,
                    'UMOWY NOWE POŻYCZKA ' . $date,
                    $rateType,
                    $values['loan'],
                    $values['contribution'],
                ], null, 'A' . $row);
                $row++;
            }
        }else{
            $accountancySheet->fromArray([
                $lp,
                'UMOWY NOWE POŻYCZKA ' . $date,
                '',
                '',
                '',
            ], null, 'A' . $row);
            $row++;
        }
        $accountancySheet->mergeCells('B'.$formattingFromRow.':B'.($row-1))
            ->getStyle('B'.$formattingFromRow)->getAlignment()->setWrapText(true);
        $accountancySheet->setCellValue('C'.$row, 'razem')
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('D'.$row, '=SUM(D'.$formattingFromRow.':D'.($row-1).')');
        $accountancySheet->setCellValue('E'.$row, '=SUM(E'.$formattingFromRow.':E'.($row-1).')');
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A'.$formattingFromRow.':E'.$row)->applyFromArray($this->borderStyle);
        $row++;

        //UMOWY WZNOWIENIE leasing
        $formattingFromRow = $row;
        $lp = 1;
        if(count($this->amountsResumeLeasing) > 0) {
            foreach ($this->amountsResumeLeasing as $rateType => $values) {
                $accountancySheet->fromArray([
                    $lp++,
                    'UMOWY WZNOWIENIE ' . $date,
                    $rateType,
                    $values['loan'],
                    $values['contribution'],
                ], null, 'A' . $row);
                $row++;
            }
        }else{
            $accountancySheet->fromArray([
                $lp,
                'UMOWY WZNOWIENIE ' . $date,
                '',
                '',
                '',
            ], null, 'A' . $row);
            $row++;
        }
        $accountancySheet->mergeCells('B'.$formattingFromRow.':B'.($row-1))->getStyle('B'.$formattingFromRow)->getAlignment()->setWrapText(true);
        $accountancySheet->setCellValue('C'.$row, 'razem')
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('D'.$row, '=SUM(D'.$formattingFromRow.':D'.($row-1).')');
        $accountancySheet->setCellValue('E'.$row, '=SUM(E'.$formattingFromRow.':E'.($row-1).')');
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A'.$formattingFromRow.':E'.$row)->applyFromArray($this->borderStyle);
        $row++;

        //UMOWY WZNOWIENIE pożyczka
        $formattingFromRow = $row;
        $lp = 1;
        if(count($this->amountsResumeLoan) > 0) {
            foreach ($this->amountsResumeLoan as $rateType => $values) {
                $accountancySheet->fromArray([
                    $lp++,
                    'UMOWY WZNOWIENIE POŻYCZKA ' . $date,
                    $rateType,
                    $values['loan'],
                    $values['contribution'],
                ], null, 'A' . $row);
                $row++;
            }
        }else{
            $accountancySheet->fromArray([
                $lp,
                'UMOWY WZNOWIENIE POŻYCZKA ' . $date,
                '',
                '',
                '',
            ], null, 'A' . $row);
            $row++;
        }
        $accountancySheet->mergeCells('B'.$formattingFromRow.':B'.($row-1))
            ->getStyle('B'.$formattingFromRow)->getAlignment()->setWrapText(true);
        $accountancySheet->setCellValue('C'.$row, 'razem')
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('D'.$row, '=SUM(D'.$formattingFromRow.':D'.($row-1).')');
        $accountancySheet->setCellValue('E'.$row, '=SUM(E'.$formattingFromRow.':E'.($row-1).')');
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A'.$formattingFromRow.':E'.$row)->applyFromArray($this->borderStyle);
        $row++;

        //UMOWY WZNOWIENIE NA OKRES 2 MIESIĘCY
        $formattingFromRow = $row;
        $lp = 1;
        if(count($this->amountsResume2Months) > 0) {
            foreach ($this->amountsResume2Months as $rateType => $values) {
                $accountancySheet->fromArray([
                    $lp++,
                    'UMOWY WZNOWIENIE NA OKRES 2 MIESIĘCY ' . $date,
                    $rateType,
                    $values['loan'],
                    $values['contribution'],
                ], null, 'A' . $row);
                $row++;
            }
        }else{
            $accountancySheet->fromArray([
                $lp,
                'UMOWY WZNOWIENIE NA OKRES 2 MIESIĘCY ' . $date,
                '',
                '',
                '',
            ], null, 'A' . $row);
            $row++;
        }
        $accountancySheet->mergeCells('B'.$formattingFromRow.':B'.($row-1))
            ->getStyle('B'.$formattingFromRow)->getAlignment()->setWrapText(true);

        $accountancySheet->setCellValue('C'.$row, 'razem')
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('D'.$row, '=SUM(D'.$formattingFromRow.':D'.($row-1).')');
        $accountancySheet->setCellValue('E'.$row, '=SUM(E'.$formattingFromRow.':E'.($row-1).')');
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A'.$formattingFromRow.':E'.$row)->applyFromArray($this->borderStyle);
        $row++;

        //KOREKTA
        $formattingFromRow = $row;
        $accountancySheet->fromArray([
            '',
            'KOREKTA '.$date,
            '',
            '',
            ''
        ], null, 'A'.$row);

        $row++;
        $accountancySheet->setCellValue('C'.$row, 'razem')
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('E'.$row, '=SUM(E'.$formattingFromRow.':E'.($row-1).')');
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A'.$formattingFromRow.':E'.$row)->applyFromArray($this->borderStyle);
        $row++;

        //ZWROT SKŁADKI
        $formattingFromRow = $row;
        $accountancySheet->fromArray([
            '',
            'ZWROT SKŁADKI '.$date,
            '',
            '',
            $this->amountRefundContribution
        ], null, 'A'.$row);
        $row++;
        $accountancySheet->setCellValue('C'.$row, 'razem')
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('E'.$row, '=SUM(E'.$formattingFromRow.':E'.($row-1).')');
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A'.$formattingFromRow.':E'.$row)->applyFromArray($this->borderStyle);
        $row++;

        //E&O
        $formattingFromRow = $row;
        $accountancySheet->fromArray([
            '',
            'E&O '.$date,
            '',
            '',
            0
        ], null, 'A'.$row);
        $row++;
        $accountancySheet->setCellValue('C'.$row, 'razem')
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('D'.$row, '=SUM(D'.$formattingFromRow.':D'.($row-1).')');
        $accountancySheet->setCellValue('E'.$row, '=SUM(E'.$formattingFromRow.':E'.($row-1).')');
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle);
        $accountancySheet->getStyle('A'.$formattingFromRow.':E'.$row)->applyFromArray($this->borderStyle);
        $row++;

        $accountancySheet->setCellValue('C'.$row, 'PODSUMOWANIE ZA MIESIĄC '.$date)
            ->getStyle('C'.$row)
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setWrapText(true);
        $accountancySheet->setCellValue('D'.$row, $this->amountAll['loan'])->setCellValue('E'.$row, $this->amountAll['contribution']);
        $accountancySheet->getStyle('A'.$row.':E'.$row)->applyFromArray($this->headerStyle)->applyFromArray($this->borderStyle);

        $accountancySheet->getStyle('A6:E'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $accountancySheet->getStyle('D6:E'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
    }


    /**
     * generowanie arkusza NOWE
     * @param $excel
     */
    private function generateNewLeasingSheet($excel)
    {
        $excel->setActiveSheetIndex(1);
        $newLeasingSheet = $excel->getActiveSheet();

        $newLeasingSheet->setCellValue('I5', 'a '.$this->owner);

        $date = strtolower($this->date->format('M-y'));
        $newLeasingSheet->setCellValue('A8', $date);

        $date = strtoupper($this->date->format('F Y'));
        $newLeasingSheet->setCellValue('G10', $date);

        $row = 17;

        LeasingAgreementInsurance::
            select('leasing_agreement_insurances.*')
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
            ->where('insurance_company_id',$this->params['insurance_company_id'])
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
            ->whereIn('leasing_agreement_insurance_type_id', [1,2])
            ->whereHas('leasingAgreement', function($query){
                $query->whereNull('withdraw');
                //$query->has('insurances', '=', 1);
                $query->where('has_yacht', '=', '0');
                $query->where('leasing_agreement_type_id', 2);
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
            })
            ->join('leasing_agreements', 'leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
            ->join('leasing_agreement_insurance_group_rows', 'leasing_agreement_insurance_group_rows.id', '=', 'leasing_agreements.leasing_agreement_insurance_group_row_id')
            ->orderBy('leasing_agreement_insurance_group_rows.leasing_agreement_insurance_group_rate_id')
            ->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$newLeasingSheet, &$row){
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

                        $rowsToInsert[$k.$k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street.' '.$agreement->client->registry_post.' '.$agreement->client->registry_city,
                            $agreement->client->NIP,
                            $agreement->client->REGON
                        );
                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->getRateName($agreement)
                        );
                    }
                    $newLeasingSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsNewLeasing = $this->calculateAmounts($this->amountsNewLeasing, $agreement, $insurance);
                }
            });

        $borderStyle = array(
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
        $newLeasingSheet->getStyle('A17:N'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $newLeasingSheet->getStyle('A17:N'.($row-1))->applyFromArray($borderStyle);

        $fromSumRow = $row+1;
        foreach($this->amountsNewLeasing as $rateType => $values)
        {
            $row++;
            $newLeasingSheet->setCellValue('A'.$row, $rateType);
            $newLeasingSheet->setCellValue('G'.$row, $values['loan']);
            $newLeasingSheet->setCellValue('J'.$row, $values['contribution']);
            $this->amountAll['loan'] += $values['loan'];
            $this->amountAll['contribution'] += $values['contribution'];
        }
        $sumStyle = array(
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
        $newLeasingSheet->getStyle('A'.$fromSumRow.':N'.$row)->applyFromArray($sumStyle);

        $row++;
        $row++;

        $newLeasingSheet->setCellValue('G'.$row, '=SUM(G'.$fromSumRow.':G'.($row-1).')');
        $newLeasingSheet->setCellValue('J'.$row, '=SUM(J'.$fromSumRow.':J'.($row-1).')');

        $newLeasingSheet->getStyle('G'.$fromSumRow.':J'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $newLeasingSheet->getStyle('G'.$row)->applyFromArray($sumStyle);
        $newLeasingSheet->getStyle('J'.$row)->applyFromArray($sumStyle);
        $newLeasingSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    /**
     * generowanie arkusza NOWE POZYCZKA
     * @param $excel
     */
    private function generateNewLoanSheet($excel)
    {
        $excel->setActiveSheetIndex(2);
        $newLoanSheet = $excel->getActiveSheet();

        $newLoanSheet->setCellValue('I6', 'a '.$this->owner);

        $date = strtolower($this->date->format('M-y'));
        $newLoanSheet->setCellValue('A9', $date);

        $date = strtoupper($this->date->format('F Y'));
        $newLoanSheet->setCellValue('H11', $date);

        $row = 17;

        LeasingAgreementInsurance::
            select('leasing_agreement_insurances.*')
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
            ->where('insurance_company_id',$this->params['insurance_company_id'])
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
            ->whereIn('leasing_agreement_insurance_type_id', [1,2])
            ->whereHas('leasingAgreement', function($query){
                $query->whereNull('withdraw');
                //$query->has('insurances', '=', 1);
                $query->where('has_yacht', '=', '0');
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
            })
            ->join('leasing_agreements', 'leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
            ->join('leasing_agreement_insurance_group_rows', 'leasing_agreement_insurance_group_rows.id', '=', 'leasing_agreements.leasing_agreement_insurance_group_row_id')
            ->orderBy('leasing_agreement_insurance_group_rows.leasing_agreement_insurance_group_rate_id')
            ->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$newLoanSheet, &$row){
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

                        $rowsToInsert[$k.$k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street . ' ' . $agreement->client->registry_post . ' ' . $agreement->client->registry_city,
                            $agreement->client->NIP,
                            $agreement->client->REGON
                        );

                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->getRateName($agreement)
                        );
                    }
                    $newLoanSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsNewLoan = $this->calculateAmounts($this->amountsNewLoan, $agreement, $insurance);
                }
            });
        $borderStyle = array(
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
        $newLoanSheet->getStyle('A17:N'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $newLoanSheet->getStyle('A17:N'.($row-1))->applyFromArray($borderStyle);

        $fromSumRow = $row+1;
        foreach($this->amountsNewLoan as $rateType => $values)
        {
            $row++;
            $newLoanSheet->setCellValue('A'.$row, $rateType);
            $newLoanSheet->setCellValue('G'.$row, $values['loan']);
            $newLoanSheet->setCellValue('J'.$row, $values['contribution']);
            $this->amountAll['loan'] += $values['loan'];
            $this->amountAll['contribution'] += $values['contribution'];
        }
        $sumStyle = array(
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
        $newLoanSheet->getStyle('A'.$fromSumRow.':N'.$row)->applyFromArray($sumStyle);

        $row++;
        $row++;

        $newLoanSheet->setCellValue('G'.$row, '=SUM(G'.$fromSumRow.':G'.($row-1).')');
        $newLoanSheet->setCellValue('J'.$row, '=SUM(J'.$fromSumRow.':J'.($row-1).')');

        $newLoanSheet->getStyle('G'.$fromSumRow.':J'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $newLoanSheet->getStyle('G'.$row)->applyFromArray($sumStyle);
        $newLoanSheet->getStyle('J'.$row)->applyFromArray($sumStyle);
        $newLoanSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    /**
     * generowanie arkusza WZNOW
     * @param $excel
     */
    private function generateResumeLeasingSheet($excel)
    {
        $excel->setActiveSheetIndex(3);
        $resumeLeasingSheet = $excel->getActiveSheet();

        $resumeLeasingSheet->setCellValue('J5', 'a '.$this->owner);

        $date = strtolower($this->date->format('M-y'));
        $resumeLeasingSheet->setCellValue('A8', $date);

        $date = strtoupper($this->date->format('F Y'));
        $resumeLeasingSheet->setCellValue('J10', $date);

        $row = 16;

        LeasingAgreementInsurance::
            select('leasing_agreement_insurances.*')
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
            ->where('insurance_company_id',$this->params['insurance_company_id'])
            ->where('notification_number', $this->notification_number )
            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->whereIn('leasing_agreement_insurance_type_id', [1,2])

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
            })
            ->join('leasing_agreements', 'leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
            ->join('leasing_agreement_insurance_group_rows', 'leasing_agreement_insurance_group_rows.id', '=', 'leasing_agreements.leasing_agreement_insurance_group_row_id')
            ->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$resumeLeasingSheet, &$row){
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

                        $rowsToInsert[$k.$k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street.' '.$agreement->client->registry_post.' '.$agreement->client->registry_city,
                            $agreement->client->NIP,
                            ''
                        );

                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->getRateName($agreement)
                        );
                    }
                    $resumeLeasingSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsResumeLeasing = $this->calculateAmounts($this->amountsResumeLeasing, $agreement, $insurance);
                }
            });
        $borderStyle = array(
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
        $resumeLeasingSheet->getStyle('A16:N'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $resumeLeasingSheet->getStyle('A16:N'.($row-1))->applyFromArray($borderStyle);

        $fromSumRow = $row+1;
        foreach($this->amountsResumeLeasing as $rateType => $values)
        {
            $row++;
            $resumeLeasingSheet->setCellValue('A'.$row, $rateType);
            $resumeLeasingSheet->setCellValue('G'.$row, $values['loan']);
            $resumeLeasingSheet->setCellValue('J'.$row, $values['contribution']);
            $this->amountAll['loan'] += $values['loan'];
            $this->amountAll['contribution'] += $values['contribution'];
        }
        $sumStyle = array(
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
        $resumeLeasingSheet->getStyle('A'.$fromSumRow.':N'.$row)->applyFromArray($sumStyle);

        $row++;
        $row++;

        $resumeLeasingSheet->setCellValue('G'.$row, '=SUM(G'.$fromSumRow.':G'.($row-1).')');
        $resumeLeasingSheet->setCellValue('J'.$row, '=SUM(J'.$fromSumRow.':J'.($row-1).')');

        $resumeLeasingSheet->getStyle('G'.$fromSumRow.':J'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $resumeLeasingSheet->getStyle('G'.$row)->applyFromArray($sumStyle);
        $resumeLeasingSheet->getStyle('J'.$row)->applyFromArray($sumStyle);
        $resumeLeasingSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    /**
     * generowanie arkusza WZNO POZYCZKA
     * @param $excel
     */
    private function generateResumeLoanSheet($excel)
    {
        $excel->setActiveSheetIndex(4);
        $resumeLoanSheet = $excel->getActiveSheet();

        $resumeLoanSheet->setCellValue('J4', 'a '.$this->owner);

        $date = strtolower($this->date->format('M-y'));
        $resumeLoanSheet->setCellValue('A9', $date);

        $date = strtoupper($this->date->format('F Y'));
        $title = "ZESTAWIENIE MIENIA OBJĘTEGO OCHRONĄ UBEZPIECZENIOWĄ W MIESIĄCU:".$date." UMOWY CZYNNE NA OKRES 12 MIESIĘCY  - POZYCZKA";
        $resumeLoanSheet->setCellValue('E11', $title);

        $row = 17;

        LeasingAgreementInsurance::
            select('leasing_agreement_insurances.*')
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
            ->where('insurance_company_id',$this->params['insurance_company_id'])
            ->where('notification_number', $this->notification_number )
            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->whereIn('leasing_agreement_insurance_type_id', [1,2])

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
            })
            ->join('leasing_agreements', 'leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
            ->join('leasing_agreement_insurance_group_rows', 'leasing_agreement_insurance_group_rows.id', '=', 'leasing_agreements.leasing_agreement_insurance_group_row_id')
            ->orderBy('leasing_agreement_insurance_group_rows.leasing_agreement_insurance_group_rate_id')
            ->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$resumeLoanSheet, &$row){
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

                        $rowsToInsert[$k.$k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street . ' ' . $agreement->client->registry_post . ' ' . $agreement->client->registry_city,
                            $agreement->client->NIP
                        );

                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->getRateName($agreement)
                        );
                    }
                    $resumeLoanSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsResumeLoan = $this->calculateAmounts($this->amountsResumeLoan, $agreement, $insurance);
                }
            });
        $borderStyle = array(
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
        $resumeLoanSheet->getStyle('A17:M'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $resumeLoanSheet->getStyle('A17:M'.($row-1))->applyFromArray($borderStyle);

        $fromSumRow = $row+1;
        foreach($this->amountsResumeLoan as $rateType => $values)
        {
            $row++;
            $resumeLoanSheet->setCellValue('A'.$row, $rateType);
            $resumeLoanSheet->setCellValue('F'.$row, $values['loan']);
            $resumeLoanSheet->setCellValue('I'.$row, $values['contribution']);
            $this->amountAll['loan'] += $values['loan'];
            $this->amountAll['contribution'] += $values['contribution'];
        }
        $sumStyle = array(
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
        $resumeLoanSheet->getStyle('A'.$fromSumRow.':M'.$row)->applyFromArray($sumStyle);

        $row++;
        $row++;

        $resumeLoanSheet->setCellValue('F'.$row, '=SUM(F'.$fromSumRow.':F'.($row-1).')');
        $resumeLoanSheet->setCellValue('I'.$row, '=SUM(I'.$fromSumRow.':I'.($row-1).')');

        $resumeLoanSheet->getStyle('F'.$fromSumRow.':I'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $resumeLoanSheet->getStyle('F'.$row)->applyFromArray($sumStyle);
        $resumeLoanSheet->getStyle('I'.$row)->applyFromArray($sumStyle);
        $resumeLoanSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    /**
     * generowanie arkusza WZNOW NA 2 MCE
     * @param $excel
     */
    private function generateResume2MonthsSheet($excel)
    {
        $excel->setActiveSheetIndex(5);
        $resume2MonthsSheet = $excel->getActiveSheet();

        $resume2MonthsSheet->setCellValue('I6', 'a '.$this->owner);

        $date = strtolower($this->date->format('M-y'));
        $resume2MonthsSheet->setCellValue('A9', $date);

        $date = strtoupper($this->date->format('F Y'));
        $resume2MonthsSheet->setCellValue('H11', $date);

        $row = 18;
        LeasingAgreementInsurance::
            select('leasing_agreement_insurances.*')
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
            ->where('insurance_company_id',$this->params['insurance_company_id'])
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
            ->join('leasing_agreements', 'leasing_agreements.id', '=', 'leasing_agreement_insurances.leasing_agreement_id')
            ->join('leasing_agreement_insurance_group_rows', 'leasing_agreement_insurance_group_rows.id', '=', 'leasing_agreements.leasing_agreement_insurance_group_row_id')
            ->orderBy('leasing_agreement_insurance_group_rows.leasing_agreement_insurance_group_rate_id')
            ->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$resume2MonthsSheet, &$row){
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

                        $rowsToInsert[$k.$k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street.' '.$agreement->client->registry_post.' '.$agreement->client->registry_city,
                            $agreement->client->NIP,
                            ''
                        );
                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push($rowsToInsert[$k.$k2],
                            $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->getRateName($agreement)
                        );
                    }
                    $resume2MonthsSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountsResume2Months = $this->calculateAmounts($this->amountsResume2Months, $agreement, $insurance);
                }
            });
        $borderStyle = array(
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
        $resume2MonthsSheet->getStyle('A18:N'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $resume2MonthsSheet->getStyle('A18:N'.($row-1))->applyFromArray($borderStyle);

        $fromSumRow = $row+1;
        foreach($this->amountsResume2Months as $rateType => $values)
        {
            $row++;
            $resume2MonthsSheet->setCellValue('A'.$row, $rateType);
            $resume2MonthsSheet->setCellValue('G'.$row, $values['loan']);
            $resume2MonthsSheet->setCellValue('J'.$row, $values['contribution']);
            $this->amountAll['loan'] += $values['loan'];
            $this->amountAll['contribution'] += $values['contribution'];
        }
        $sumStyle = array(
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
        $resume2MonthsSheet->getStyle('A'.$fromSumRow.':N'.$row)->applyFromArray($sumStyle);

        $row++;
        $row++;

        $resume2MonthsSheet->setCellValue('G'.$row, '=SUM(G'.$fromSumRow.':G'.($row-1).')');
        $resume2MonthsSheet->setCellValue('J'.$row, '=SUM(J'.$fromSumRow.':J'.($row-1).')');

        $resume2MonthsSheet->getStyle('G'.$fromSumRow.':J'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $resume2MonthsSheet->getStyle('G'.$row)->applyFromArray($sumStyle);
        $resume2MonthsSheet->getStyle('J'.$row)->applyFromArray($sumStyle);
        $resume2MonthsSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    /**
     * generowanie arkusza KOREKTA
     * @param $excel
     */
    private function generateCorrectionSheet($excel)
    {
        $excel->setActiveSheetIndex(6);
        $correctionSheet = $excel->getActiveSheet();

        $correctionSheet->setCellValue('K4', 'a '.$this->owner);

        $date = strtoupper($this->date->format('F Y'));
        $title = "KOREKTY W MIESIĄCU ".$date;
        $correctionSheet->setCellValue('A8', $title);
        $correctionSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    /**
     * generowanie arkusza ZWROT SKŁADKI
     * @param $excel
     */
    private function generateResumeContributionSheet($excel)
    {
        $excel->setActiveSheetIndex(7);
        $resumeContributionSheet = $excel->getActiveSheet();

        $resumeContributionSheet->setCellValue('O4', 'a '.$this->owner);

        $date = strtoupper($this->date->format('F Y'));
        $title = "ZWROT SKŁADKI DO UMÓW ZA MIESIĄC ".$date." WZNOWIENIE ";
        $resumeContributionSheet->setCellValue('A8', $title);

        $row = 12;

        LeasingAgreementInsurance::where('insurance_company_id',$this->params['insurance_company_id'])
            ->where(function($query){
                if($this->params['refunds_type'] == 1)
                {
                    $query->whereRaw('(select count(*) from `leasing_agreement_insurances` local_leasing_agreement_insurances where `leasing_agreement_insurances`.`refunded_insurance_id` = `id` and `date_from` >= "2015-01-20" and `leasing_agreement_insurances`.`deleted_at` is null) >= 1');
                }else{
                    $query->whereRaw('(select count(*) from `leasing_agreement_insurances` local_leasing_agreement_insurances where `leasing_agreement_insurances`.`refunded_insurance_id` = `id` and `date_from` < "2015-01-20" and `leasing_agreement_insurances`.`deleted_at` is null) >= 1');
                }

                if($this->params['if_foreign_policy'] == 0)
                {
                    $query->where('if_foreign_policy', 0);
                }else{
                    $query->where('if_foreign_policy', 1);
                }
            })
            ->where('notification_number', $this->notification_number)
            ->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_insurances.created_at', '>', $this->last_report->created_at);
                }
            })
            ->where('if_refund_contribution', 1)
            //->where('if_cession', 0)
            ->whereHas('leasingAgreement', function($query) {
                $query->whereNull('withdraw');
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
            })->with('refundedInsurance', 'leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$resumeContributionSheet, &$row){
                foreach ($insurances as $k => $insurance) {
                    $rowsToInsert = array();
                    $count_rows = 0;
                    $agreement = $insurance->leasingAgreement;
                    $refundedAgreement = $insurance->refundedInsurance;
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

                        $rowsToInsert[$k.$k2] = array(
                            $object->name,
                            $agreement->nr_contract,
                            $agreement->client->name,
                            $agreement->client->registry_street.' '.$agreement->client->registry_post.' '.$agreement->client->registry_city,
                            $agreement->client->NIP
                        );

                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        if($insurance->date_from != $insurance->date_to)
                            $refund_from = \Date::createFromFormat('Y-m-d', $insurance->date_from)->addDay()->format('Y-m-d');
                        else
                            $refund_from = $insurance->date_from;

                        array_push($rowsToInsert[$k.$k2],
                            ($refundedAgreement) ? $refundedAgreement->date_from : $insurance->date_from,
                            $insurance->date_to,
                            $insurance->months,
                            $this->calculateUsedInsuranceTimeAfterRefund($insurance, $refundedAgreement),
                            $refund_from." DO ".$insurance->date_to,
                            $this->calculateMonths($insurance->date_from, $insurance->date_to)
                        );

                        if($k2 == 0)
                        {
                            array_push($rowsToInsert[$k.$k2], $insurance->refund, '');
                        }else{
                            array_push($rowsToInsert[$k.$k2], '', '');
                        }
                    }
                    $resumeContributionSheet->fromArray($rowsToInsert, null, 'A'.$row);
                    $row+=$count_rows;
                    $this->amountRefundContribution -= $insurance->refund;
                }
            });

        $this->amountAll['contribution'] += $this->amountRefundContribution;

        $borderStyle = array(
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
        $resumeContributionSheet->getStyle('A12:Q'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $resumeContributionSheet->getStyle('A12:Q'.$row)->applyFromArray($borderStyle);

        $resumeContributionSheet->setCellValue('O'.$row,'RAZEM')->setCellValue('P'.$row,'=SUM(P12:P'.($row-1).')');
        $borderStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'size'  => 9,
                'name'  => 'Arial',
                'bold'  => true
            )
        );
        $resumeContributionSheet->getStyle('O'.$row.':P'.$row)->applyFromArray($borderStyle);
        $resumeContributionSheet->getStyle('P'.$row)
            ->getNumberFormat()
            ->setFormatCode('_-* #,##0.00\ [$zł-415]_-');
        $resumeContributionSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    /**
     * generowanie arkusza ZMIANA DANYCH
     * @param $excel
     */
    private function generateDataChangeSheet($excel) {

        $excel->setActiveSheetIndex(8);
        $dataChangeSheet = $excel->getActiveSheet();

        $dataChangeSheet->setCellValue('F5', 'a '.$this->owner);

        $date = strtoupper($this->date->format('F Y'));
        $title = "ZMIANA DANYCH LB - MIESIĄC ".$date;
        $dataChangeSheet->setCellValue('A5', $title);

        $row = 10;
        \LeasingAgreementHistoryLog::whereHas('history', function($query){
            $query->where('notification_number', $this->notification_number)->where(function($query){
                if(!is_null($this->last_report))
                {
                    $query->where('leasing_agreement_histories.created_at', '>', $this->last_report->created_at);
                }
            });

            $query->whereHas('agreement', function($subQuery){
                $subQuery->whereNull('withdraw');
                $subQuery->where('has_yacht', '=', '0');
                $subQuery->where('owner_id', $this->params['owner_id']);
                $subQuery->whereHas('insurances', function($subQuery2){
                    $subQuery2->where(function($query){
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
                    });
                    $subQuery2->where('insurance_company_id',$this->params['insurance_company_id']);
                });

                if($this->params['if_sk'] == 1){
                    $subQuery->where(function($query){
                        $query->where('nr_contract', 'like', '%/SK')
                            ->orWhere('nr_contract', 'like', '%/SK/%');
                    });
                }elseif($this->params['if_sk'] == 2){
                    $subQuery->where('nr_contract', 'not like', '%/SK')
                        ->where('nr_contract', 'not like', '%/SK/%');
                }
            });
        })->chunk(100, function($logs) use (&$dataChangeSheet, &$row) {
            $rowsToInsert = array();
            $count_rows = 0;
            foreach ($logs as $k => $log) {
                $count_rows++;
                $rowsToInsert[] = array(
                    $log->object_name,
                    $log->nr_contract,
                    $log->client_name,
                    $log->client_address,
                    $log->client_NIP,
                    $log->client_REGON,
                    $log->loan_value,
                    $log->net_gross,
                    $log->rate,
                    $log->contribution,
                    $log->insurance_from,
                    $log->insurance_to,
                    $log->months,
                    $log->log_new
                );
            }
            $dataChangeSheet->fromArray($rowsToInsert, null, 'A'.$row);
            $row+=$count_rows;
        });

        $dataChangeSheet->getStyle('A10:N'.$row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)->setWrapText(true);
        $dataChangeSheet->getDefaultRowDimension()->setRowHeight(-1);
    }


    /**
     * generowanie arkusza E&O
     * @param $excel
     */
    private function generateEOSheet($excel)
    {
        $excel->setActiveSheetIndex(9);
        $eoSheet = $excel->getActiveSheet();

        $eoSheet->setCellValue('I5', 'a '.$this->owner);

        $date = strtolower($this->date->format('M-y'));
        $eoSheet->setCellValue('A8', $date);

        $date = strtoupper($this->date->format('F Y'));
        $eoSheet->setCellValue('G10', $date);
        $eoSheet->getDefaultRowDimension()->setRowHeight(-1);
    }

    protected function parseDate($date, $modify_days)
    {
        if($date != '') {
            $date_from = new DateTime($date);
            $date_from->modify($modify_days.' day');
            return $date_from->format('Y-m-d');
        }
        return 0;
    }

    private function getRateName($agreement)
    {
        if($agreement->insurance_group_row && $agreement->insurance_group_row->rate)
            return $agreement->insurance_group_row->rate->name;

        return '';
    }

    private function calculateUsedInsuranceTimeAfterRefund($agreement, $refundedAgreement)
    {
        $date_to = $agreement->date_from;
        if($refundedAgreement)
        {
            $date_from = $refundedAgreement->date_from;
            return $date_from." DO ".$date_to;
        }
        return "DO ".$date_to;
    }

    private function calculateMonths($date_from, $date_to)
    {
        $date_from = \Date::createFromFormat('Y-m-d', $date_from);
        $date_to = \Date::createFromFormat('Y-m-d', $date_to);
        return $date_from->diffInMonths($date_to);
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
            'if_sk'     => $this->params['if_sk']
        ]);
    }

}
