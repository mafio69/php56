<?php

namespace Idea\Reports\InsurancesReports\Sheets;

use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class PropertyInsurancesReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

        /*
        Excel::create($this->filename, function($excel){
            $date_from = $this->parseDate($this->params['date_from'], '0');
            $date_to = $this->parseDate($this->params['date_to'], '+1');

            $count =  \LeasingAgreement::
                    whereBetween('created_at', array($date_from, $date_to))
                    ->has('insurances', '=', 1)
                    ->whereHas('insurances', function($query){
                        $query->where('insurance_company_id',$this->params['insurances_insurance_company_id']);
                    })->count();

            $excel->sheet('Export', function($sheet) use ($date_from,$date_to){


                $sheet->appendRow(array('Zestawienie zawartych ubezpieczeń majątkowych '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                \LeasingAgreement::
                      whereBetween('created_at', array($date_from, $date_to))
                      ->has('insurances', '=', 1)
                      ->whereHas('insurances', function($query){
                          $query->where('insurance_company_id',$this->params['insurances_insurance_company_id']);
                      })
                      ->where(function($query){
                        ;
                      })->with(['insurances'=> function($query){
                        $query->with('insuranceType', 'insuranceCompany', 'leasingAgreementPaymentWay')->orderBy('id','desc');
                      }, 'client','objects','insurance_group_row'])
                    ->chunk(300, function($leasing_agreements) use (&$sheet){
                        foreach($leasing_agreements as $leasing_agreement){
                            $insurance = $this->getInsurance($leasing_agreement);
                            $sheet->appendRow(array(
                                $leasing_agreement->nr_contract,
                                ($leasing_agreement->client) ? $leasing_agreement->client->name : '',
                                ($leasing_agreement->client) ? $leasing_agreement->client->NIP : '',
                                ($leasing_agreement->client) ? $leasing_agreement->client->REGON : '',
                                ($leasing_agreement->client) ? $leasing_agreement->client->registry_post : '' ,
                                ($leasing_agreement->client) ? $leasing_agreement->client->registry_city : '' ,
                                ($leasing_agreement->client) ? $leasing_agreement->client->registry_street : '' ,
                                $this->getObject($leasing_agreement),
                                ($leasing_agreement->net_gross==1) ? number_format($leasing_agreement->loan_net_value,2,",","") : (($leasing_agreement->net_gross==2) ? number_format($leasing_agreement->loan_gross_value,2,",","") : '') ,
                                ($leasing_agreement->net_gross==1) ? 'netto' : (($leasing_agreement->net_gross==2) ? 'brutto' : 'nie zdefiniowano') ,
                                ($insurance&&$insurance->insuranceCompany) ? $insurance->insuranceCompany->name : '',
                                ($insurance) ? $insurance->date_from : '',
                                ($insurance) ? $insurance->date_to : '',
                                ($insurance) ? $insurance->months : '',
                                ($insurance) ? number_format($insurance->contribution_lessor,2,",","") : '',
                                ($insurance&&$insurance->leasingAgreementPaymentWay) ? $insurance->leasingAgreementPaymentWay->name : '',
                                ($insurance) ? number_format($insurance->rate_lessor,2,","," ") : '',
                                ($leasing_agreement->insurance_group_row()->first()) ? $leasing_agreement->insurance_group_row()->first()->rateName : '',
                                ($insurance) ? (string)$insurance->insurance_number : '',
                                ($insurance) ? $insurance->notification_number : '',
                                ($insurance) ? ($insurance->if_refund_contribution == '0') ? 'NIE' : number_format($insurance->refund,2,",","") : '',
                                ($insurance) ? number_format($insurance->contribution,2,",","") : '',
                            ));
                        }
                    });
            })->setColumnFormat(array(
                   'A3:R'.$count => '@',
                   'S3:S'.$count=>  '0',

              ));

        })->export('xls');
        */

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$this->filename}.csv");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen( 'php://output', 'w' );

        $date_from = $this->parseDate($this->params['date_from'], '0');
        $date_to = $this->parseDate($this->params['date_to'], '+1');

        fputcsv($fh,array('Zestawienie zawartych ubezpieczeń majątkowych '.$this->params['date_from'].' - '.$this->params['date_to']));
        fputcsv($fh,$this->generateTheads());

        \LeasingAgreement::
        whereBetween('created_at', array($date_from, $date_to))
            ->whereHas('insurances', function($query){
                $query->where('insurance_company_id',$this->params['insurances_insurance_company_id']);
            })
            ->with(['insurances'=> function($query){
                $query->with('insuranceType', 'insuranceCompany', 'leasingAgreementPaymentWay')->orderBy('id','desc');
            }, 'client','objects','insurance_group_row'])
            ->chunk(300, function($leasing_agreements) use (&$sheet, &$fh){
                foreach($leasing_agreements as $leasing_agreement){
                    $insurance = $this->getInsurance($leasing_agreement);
                    fputcsv($fh,array(
                        $leasing_agreement->nr_contract,
                        ($leasing_agreement->client) ? $leasing_agreement->client->name : '',
                        ($leasing_agreement->client) ? $leasing_agreement->client->NIP : '',
                        ($leasing_agreement->client) ? $leasing_agreement->client->REGON : '',
                        ($leasing_agreement->client) ? $leasing_agreement->client->registry_post : '' ,
                        ($leasing_agreement->client) ? $leasing_agreement->client->registry_city : '' ,
                        ($leasing_agreement->client) ? $leasing_agreement->client->registry_street : '' ,
                        $this->getObject($leasing_agreement),
                        ($leasing_agreement->net_gross==1) ? number_format($leasing_agreement->loan_net_value,2,",","") : (($leasing_agreement->net_gross==2) ? number_format($leasing_agreement->loan_gross_value,2,",","") : '') ,
                        ($leasing_agreement->net_gross==1) ? 'netto' : (($leasing_agreement->net_gross==2) ? 'brutto' : 'nie zdefiniowano') ,
                        ($insurance&&$insurance->insuranceCompany) ? $insurance->insuranceCompany->name : '',
                        ($insurance) ? $insurance->date_from : '',
                        ($insurance) ? $insurance->date_to : '',
                        ($insurance) ? $insurance->months : '',
                        ($insurance) ? number_format($insurance->contribution_lessor,2,",","") : '',
                        ($insurance&&$insurance->leasingAgreementPaymentWay) ? $insurance->leasingAgreementPaymentWay->name : '',
                        ($insurance) ? number_format($insurance->rate_lessor,2,","," ") : '',
                        ($leasing_agreement->insurance_group_row()->first()) ? $leasing_agreement->insurance_group_row()->first()->rateName : '',
                        ($insurance) ? (string)$insurance->insurance_number : '',
                        ($insurance) ? $insurance->notification_number : '',
                        ($insurance) ? ($insurance->if_refund_contribution == '0') ? 'NIE' : number_format($insurance->refund,2,",","") : '',
                        ($insurance) ? number_format($insurance->contribution,2,",","") : '',
                        (($insurance) ? number_format($insurance->commission,2,",","") : '').'%',
                        ($insurance) ? number_format($insurance->contribution_commission,2,",","") : '',
                    ));
                }
            });

        fclose($fh);
        exit;
    }

    public function generateTheads()
    {
        return [
            'nr umowy',
            'Nazwa leasingobiorcy',
            'NIP leasingobiorcy',
            'REGON Leasingobiorcy',
            'Kod pocztowy leasingobiorcy',
            'Miasto leasigobiorcy',
            'Ulica leasingobiorcy',
            'Przedmiot',
            'Suma ubezpieczenia',
            'Ubezpieczenie od kwoty',
            'Ubezpieczyciel',
            'Polisa od',
            'Polisa do',
            'Liczba miesięcy',
            'Składka leasingobiorcy',
            'Płatność składki',
            'Stawka',
            'Grupa ubezpieczenia',
            'nr polisy',
            'nr zgłoszenia',
            'Zwrot składki',
            'Składka leasingodawcy',
            'Prowizja',
            'Wartość prowizji'
        ];
    }

    private function getObject($leasing_agreement){
      $object =  $leasing_agreement->objects->first();
      if($object){
        return $object->name;
      }
      else{
        return '---';
      }
    }

    private function getInsurance($leasing_agreement){
      return $leasing_agreement->insurances->first();
    }
}
