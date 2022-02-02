<?php

namespace Idea\Reports\InsurancesReports\Sheets;

use Carbon\Carbon;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class RefundsReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Content-Description: File Transfer');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename={$this->filename}.csv");
        header("Expires: 0");
        header("Pragma: public");

        $fh = @fopen( 'php://output', 'w' );
        fputs( $fh, "\xEF\xBB\xBF" );

        $date_from = $this->parseDate($this->params['date_from'], '0');
        $date_to = $this->parseDate($this->params['date_to'], '+1');

        fputcsv($fh,array('Zestawienie zawartych ubezpieczeń majątkowych '.$this->params['date_from'].' - '.$this->params['date_to']), ';');
        fputcsv($fh,$this->generateTheads(),';');

        $lp = 1;
        \LeasingAgreementInsurance::
            where('if_refund_contribution', 1)
            ->where('insurance_company_id',$this->params['insurances_insurance_company_id'])
            ->whereBetween('date_from', array($date_from, $date_to))
            ->with('leasingAgreement.insurance_group_row.rate', 'refundedInsurance')
            ->chunk(300, function($insurances) use (&$sheet, &$fh, &$lp){
                foreach($insurances as $insurance){  
                    $from = new Carbon( $insurance->leasingAgreement->insurance_from );
                    $to = new Carbon( $insurance->leasingAgreement->insurance_to );
                    fputcsv($fh,array(
                        $lp++,
                        $insurance->leasingAgreement->nr_contract,
                        $insurance->insurance_number,
                        $this->getRateName($insurance->leasingAgreement),
                        $insurance->leasingAgreement->insurance_from,
                        $from->diffInMonths( $to ),
                        $insurance->leasingAgreement->insurance_to,
                        $insurance->leasingAgreement->loan_net_value,
                        $insurance->contribution,
                        number_format( ($insurance->refundedInsurance->contribution - $insurance->refund),2,".",""),
                        $insurance->refund,
                        $insurance->commission
                    ), ';');
                }
            });

        fclose($fh);
        exit;
    }

    public function generateTheads()
    {
        return [
            'L.P.',
            'numer umowy',
            'numer polisy',
            'grupa ubezpieczenia',
            'okres ubezpieczenia od',
            'okres trwania umowy leasingu',
            'data zakończenia umowy leasingu',
            'suma ubezpieczenia',
            'składka leasingodawcy',
            'składka zapłacona',
            'składka do zwrotu',
            'prowizja [%]'
        ];
    }

    private function getRateName($agreement)
    {
        if($agreement->insurance_group_row && $agreement->insurance_group_row->rate)
            return $agreement->insurance_group_row->rate->name;

        return '';
    }
}
