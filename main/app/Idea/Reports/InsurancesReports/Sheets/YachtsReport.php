<?php
namespace Idea\Reports\InsurancesReports\Sheets;

use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class YachtsReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

            $excel->sheet('Export', function($sheet) {

                $sheet->appendRow(array('Zestawienie jachtów '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                \LeasingAgreementInsurance::
                    where(function($query){
                        $date_from = $this->parseDate($this->params['date_from'], '0');
                        $date_to = $this->parseDate($this->params['date_to'], '+1');

                        if($this->params['from_type'] == 'created_at') {
                            $query->whereBetween('created_at', array($date_from, $date_to));
                        }elseif($this->params['from_type'] == 'insurance_date') {
                            $query->whereBetween('insurance_date', array($date_from, $date_to));
                        }
                    })
                    ->whereHas('leasingAgreement', function($query){
                        $query->where('has_yacht', '=', '1');
                        if($this->params['from_type'] == 'agreement_created_at') {
                            $date_from = $this->parseDate($this->params['date_from'], '0');
                            $date_to = $this->parseDate($this->params['date_to'], '+1');

                            $query->whereBetween('created_at', array($date_from, $date_to));
                        }
                    })
                    ->with('leasingAgreementPaymentWay', 'insuranceCompany', 'leasingAgreement', 'leasingAgreement.client', 'leasingAgreement.insurances')
                    ->chunk(200, function($insurances) use (&$sheet){
                        foreach ($insurances as $k => $insurance) {
                            if($insurance->leasingAgreement->insurances->isEmpty() && is_null($insurance->leasingAgreement->withdraw)){
                                $status = 'nowe';
                            }elseif(is_null($insurance->leasingAgreement->withdraw) && !is_null($insurance->leasingAgreement->archive)){
                                $status = 'archiwum';
                            }elseif( !is_null($insurance->leasingAgreement->withdraw) ){
                                $status = 'wycofana';
                            }elseif($insurance->leasingAgreement->insurances->count() > 0 && is_null($insurance->leasingAgreement->withdraw) && is_null($insurance->leasingAgreement->archive) && $insurance->leasingAgreement->withCurrentResume->first()){
                                $status = 'wznowienie aktualne';
                            }elseif($insurance->leasingAgreement->insurances->count() > 0 && is_null($insurance->leasingAgreement->withdraw) && is_null($insurance->leasingAgreement->archive) && $insurance->leasingAgreement->withArchiveResume->first()){
                                $status = 'wznowienie archiwalne';
                            }elseif($insurance->leasingAgreement->insurances->count() > 0 &&
                                $insurance->leasingAgreement->whereNull('withdraw')->whereNull('archive')
                                    ->whereHas('insurances', function($query){
                                        $query->active();
                                    })->first()){
                                $status = 'trwająca';
                            }
                            $sheet->appendRow(array(
                                $insurance->leasingAgreement->nr_contract,
                                $insurance->insurance_number,
                                ($insurance->leasingAgreement->client) ? $insurance->leasingAgreement->client->name : '---',
                                $insurance->insurance_date,
                                $insurance->date_from,
                                $insurance->date_to,
                                $insurance->contribution_lessor,
                                '',
                                '',
                                ($insurance->leasingAgreementPaymentWay) ? $insurance->leasingAgreementPaymentWay->name : '---',
                                ($insurance->insuranceCompany) ? $insurance->insuranceCompany->name : '---',
                                $status,
                                $insurance->commission_value,
                                $insurance->commission_date,
                                $insurance->commission_refund_value
                            ));
                        }
                    });
            });

        })->export('xls');
    }

    public function generateTheads()
    {
        return [
            'nr umowy',
            'nr polisy',
            'nazwa leasingobiorcy',
            'data zawarcia polisy',
            'polisa od',
            'polisa do',
            'wysokość składki',
            'termin płatności składki',
            'składkę zapłacono',
            'typ płatności',
            'towarzystwo',
            'status',
            'kwota prowizji',
            'data prowizji',
            'zwrot prowizji'
        ];
    }
}