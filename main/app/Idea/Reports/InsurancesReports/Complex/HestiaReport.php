<?php

namespace Idea\Reports\InsurancesReports\Complex;

use Carbon\Carbon;
use Idea\Reports\ReportsInterface;
use Idea_data;
use LeasingAgreementInsurance;

class HestiaReport implements ReportsInterface{

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
        $template = \Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/HESTIA_report.xlsx';

        $filename = 'HESTIA '.$date.'_';

        $owner = \Owners::find($this->params['owner_id']);

        $filename .= $owner->name.' '.preg_replace('/[^a-zA-Z0-9\-\._]/','-', $this->params['general_contract']);

//        if($this->params['general_contract'] == '30/GLK2/IDEA/2015'){
//            $filename .= ' GL50_000638_18_A';
//        }else{
//            $filename .= ' GL50_000639_18_A';
//        }

        if($this->params['if_trial'] == 1)
        {
            $filename .= '_próbny-'.rand(100, 999);
        }elseif(!is_null($this->version))
        {
            $filename .= '_'.$this->version;
        }

        $this->filename = $filename;

        $filePath = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/reports/generated';

        \CustomLog::info('reports', 'HestiaReport '.$filename, [$this->params, $this->notification_number]);

        \Excel::load($template, function($excel)  {
            $this->generateSheet($excel);
            $this->createReportEntry();
        })->setFileName($filename)->store('xls', $filePath)->download('xls');

        \Session::set('avoid_query_logging', false);
    }

    private function generateSheet($excel)
    {
        $sheet = $excel->getActiveSheet();

        $sheet->setTitle('Raport');

        $row = 4;

        $idea = Idea_data::get();
        $ideaA = array();
        foreach ($idea as $setting) {
            $ideaA[$setting->owner_id][$setting->parameter_id] = $setting->value;
        }

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
            //->where('if_cession', 0)
            ->whereNull('refund')
            ->whereHas('leasingAgreement', function($query) {
                $query->whereNull('withdraw');
                $query->where('owner_id', $this->params['owner_id']);
                $query->where('has_yacht', '=', '0');
                $query->whereHas('insurance_group_row', function($query){
                    $query->where('general_contract', $this->params['general_contract']);
                });
                if($this->params['if_sk'] == 1){
                    $query->where(function($query){
                        $query->where('nr_contract', 'like', '%/SK')
                            ->orWhere('nr_contract', 'like', '%/SK/%');
                    });
                }elseif($this->params['if_sk'] == 2){
                    $query->where('nr_contract', 'not like', '%/SK')
                        ->where('nr_contract', 'not like', '%/SK/%');
                }
            })->with('leasingAgreement',  'leasingAgreement.insurance_group_row.rate')
            ->chunk(100, function($insurances) use (&$sheet, &$row, $ideaA){

                foreach ($insurances as $k => $insurance) {
                    $agreement = $insurance->leasingAgreement;

                    $rowToInsert = array(
                        $insurance->insurance_number,   //a
                        ($insurance->insurance_date == '0000-00-00') ? '' : $insurance->insurance_date, //b
                        ($insurance->date_from == '0000-00-00') ? '' : $insurance->date_from, //c
                        ($insurance->date_to == '0000-00-00') ? '' : $insurance->date_to, //d
                        'N', //typ e
                        '', //pesel f
                        (isset($ideaA[$agreement->owner_id][8])) ? $ideaA[$agreement->owner_id][8] : '', // nip g
                        (isset($ideaA[$agreement->owner_id][15])) ? $ideaA[$agreement->owner_id][15] : '', // regon h
                        (isset($ideaA[$agreement->owner_id][1])) ? $ideaA[$agreement->owner_id][1] : '', // nazwa i
                        '', //nazwisko j
                        '', //data urodzenia k
                        '', //płeć l
                        (isset($ideaA[$agreement->owner_id][17])) ? $ideaA[$agreement->owner_id][17] : '', //przed m
                        (isset($ideaA[$agreement->owner_id][18])) ? $ideaA[$agreement->owner_id][18] : '', //ulica n
                        (isset($ideaA[$agreement->owner_id][19])) ? $ideaA[$agreement->owner_id][19] : '', //dom o
                        (isset($ideaA[$agreement->owner_id][20])) ? $ideaA[$agreement->owner_id][20] : '', //lokal p
                        (isset($ideaA[$agreement->owner_id][3])) ? $ideaA[$agreement->owner_id][3] : '', //kod q
                        (isset($ideaA[$agreement->owner_id][21])) ? $ideaA[$agreement->owner_id][21] : '', //poczta r
                        (isset($ideaA[$agreement->owner_id][13])) ? $ideaA[$agreement->owner_id][13] : '', //miasto s
                        $this->getGeneralContract($insurance), //t
                        $agreement->insurance_group_row->symbol_product, //u
                        $agreement->insurance_group_row->symbol_element, //v
                        ($agreement->net_gross == 2) ?  $agreement->loan_gross_value : $agreement->loan_net_value, //w
                        $insurance->contribution, //x
                        '', //y
                        '', //z
                        '', //aa
                        '', //ab
                        '', //ac
                        '', //ad
                        '', //ae
                        '', //af
                        '', //ag
                        '', //ah
                        '', //ai
                        '', //aj
                        'wpłata osobista', //ak
                        $this->calculateDatePayment($insurance->notification_number), //al
                        $insurance->contribution, //am
                        '', //an
                        '', //ao
                        '', //ap
                        '', //aq
                        '', //ar
                        '', //as
                        'N', //at
                        '', //au
                        (isset($ideaA[8])) ? $ideaA[$agreement->owner_id][8] : '', //av
                        (isset($ideaA[15])) ? $ideaA[$agreement->owner_id][15] : '', //aw
                        (isset($ideaA[1])) ? $ideaA[$agreement->owner_id][1] : '', //ax
                        '', //ay
                        '', //az
                        (isset($ideaA[$agreement->owner_id][17])) ? $ideaA[$agreement->owner_id][17] : '', //ba
                        (isset($ideaA[$agreement->owner_id][18])) ? $ideaA[$agreement->owner_id][18] : '', //bb
                        (isset($ideaA[$agreement->owner_id][19])) ? $ideaA[$agreement->owner_id][19] : '', //bc
                        (isset($ideaA[$agreement->owner_id][20])) ? $ideaA[$agreement->owner_id][20] : '', //bd
                        (isset($ideaA[$agreement->owner_id][3])) ? $ideaA[$agreement->owner_id][3] : '', //be
                        (isset($ideaA[$agreement->owner_id][21])) ? $ideaA[$agreement->owner_id][21] : '', //bf
                        (isset($ideaA[$agreement->owner_id][13])) ? $ideaA[$agreement->owner_id][13] : '', //bg
                        ($this->params['insurance_company_id']=='320') ? '26083' : '027310', //bh
                        'GL50', //bi
                        '="00164"', //bj
                        sprintf("%.2f%%", $insurance->commission), //bk
                        'A', //bl
                        ($this->params['insurance_company_id']=='320') ? 'BOS026083' : 'NWR000283', //bm
                        '', //bn
                        '', //bo
                        '', //bp
                        '', //bq
                        '', //br
                        '', //bs
                        '', //bt
                        '', //bu
                        '', //bv
                        '', //bw
                        '', //bx
                        '', //by
                        '', //bz
                        '', //ca
                        '', //cb
                        '',  //cc
                        $agreement->nr_contract,
                        $this->paymentInfo($agreement, $insurance)
                    );


                    $sheet->fromArray($rowToInsert, null, 'A'.$row);

                    $row+=1;
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
            ->where('general_contract', $this->params['general_contract'])
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

    private function calculateDatePayment($notification_number)
    {
        $dateInsurance = Carbon::createFromFormat('m/Y', $notification_number);
        $dateInsurance->addMonths(2);
        $dateInsurance->day(7);
        return $dateInsurance->format('Y-m-d');
    }

    private function getProvision()
    {
        if($this->params['general_contract'] == '30/GLK2/IDEA/2015'){
            return '45%';
        }

        return '53%';
    }

    private function getGeneralContract($insurance)
    {
        return $insurance->leasingAgreement->insurance_group_row->general_contract;
        if($this->params['general_contract'] == '30/GLK2/IDEA/2015'){
            return 'GL50/000638/18/A';
        }

        return 'GL50/000639/18/A';
    }

    private function paymentInfo($agreement, $insurance)
    {
        if($agreement->leasingAgreementPaymentWay) {
            if ($insurance->if_continuation == 0 && $agreement->leasingAgreementPaymentWay->id == 2 && $agreement->if_reportable == 0) {
                return 'Wielolatka';
            }
        }

        return '';
    }

}
