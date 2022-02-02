<?php

namespace Idea\Reports\InsurancesReports\Complex;

use Idea\Reports\ReportsInterface;
use LeasingAgreementInsurance;

class EuropaReport implements ReportsInterface{

    private $params;
    private $date;
    private $notification_number;
    private $last_report = null;
    private $version = null;
    private $filename = '';

    private $borderStyle = array(
        'borders' => array(
            'allborders' => array(
                'style' => \PHPExcel_Style_Border::BORDER_THIN
            )
        )
    );


    function __construct($params = array())
    {
        $this->params = $params;
        $this->date = \Date::createFromFormat('Y-m-d', $params['date'].'-01');
        $this->parseNotification();
    }


    /**
     * @return mixed
     */
    public function generateReport()
    {
        set_time_limit(1000);
        \DB::disableQueryLog();
        \Session::set('avoid_query_logging', true);

        $date = $this->date->format('m_Y');
        $template = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/EUROPA_report.xlsx';
        $filename = 'EUROPA '.$date;

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

        \CustomLog::info('reports', 'EuropaReport '.$filename, [$this->params, $this->notification_number]);
        \Excel::load($template, function($excel)  {
            $this->generateSheet($excel);
            $this->createReportEntry();
        })->setFileName($filename)->store('xls', $filePath)->download('xls');

        \Session::set('avoid_query_logging', false);
    }

    private function generateSheet($excel)
    {
        $sheet = $excel->getActiveSheet();

        $date = strtolower($this->date->format('ym'));
        $sheet->setTitle('MIES'.$date);

        $row = 2;
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
            ->where('if_cession', 0)
            ->whereNull('refund')
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
            })->with('leasingAgreement', 'leasingAgreement.objects', 'leasingAgreement.client', 'leasingAgreement.insurance_group_row.rate', 'leasingAgreement.owner', 'leasingAgreement.leasingAgreementType')
            ->chunk(100, function($insurances) use (&$sheet, &$row){

                foreach ($insurances as $k => $insurance) {
                    $agreement = $insurance->leasingAgreement;
                    $rowsToInsert = array();
                    $count_rows = 0;
                    foreach ($agreement->objects as $k2 => $object) {
                        $count_rows++;
                        if($k2 == 0)
                        {
                            $amounts = [
                                ($agreement->net_gross == 1) ? $agreement->loan_net_value : $agreement->loan_gross_value,
                                ($agreement->net_gross == 1) ? 'tak' : 'nie',
                                'S',
                                $insurance->contribution
                            ];
                        }else{
                            $amounts = ['', '', '', ''];
                        }

                        $rowsToInsert[$k.$k2] = array(
                            ($row+$count_rows-2),
                            $agreement->nr_contract,
                            $agreement->client->REGON,
                            $agreement->client->NIP,
                            $agreement->client->registry_post,
                            $agreement->client->registry_city,
                            $agreement->client->registry_street,
                            '',
                            '',
                            '',
                            $insurance->date_from,
                            $insurance->months,
                            $insurance->date_to,
                            $object->name,
                            '',
                            $this->getRateName($agreement)
                        );

                        foreach($amounts as $amount)
                            array_push($rowsToInsert[$k.$k2], $amount);

                        array_push( $rowsToInsert[$k.$k2], $agreement->owner->name );
                        array_push( $rowsToInsert[$k.$k2], $agreement->leasingAgreementType ? $agreement->leasingAgreementType->name : '');

                    }

                    $sheet->fromArray($rowsToInsert, null, 'A'.$row);
                    /*
                    for($i = 'Q'; $i <= 'T'; $i++)
                        $sheet->mergeCells($i.$row.":".$i.($row+$count_rows-1));
                    */
                    $row+=$count_rows;
                }

            });
    }


    private function getRateName($agreement)
    {
        if($agreement->insurance_group_row && $agreement->insurance_group_row->rate)
            return $agreement->insurance_group_row->rate->name;

        return '';
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
