<?php
namespace Idea\Reports\InsurancesReports\Sheets;

use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class YachtsSimpleReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

    private $filename;

    function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function generateReport()
    {
        set_time_limit(500);

        Excel::create($this->filename, function($excel) {

            $excel->sheet('Export', function($sheet) {

                $sheet->appendRow(array('Zestawienie jachtów'));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                \LeasingAgreementInsurance::
                    whereHas('leasingAgreement', function($query){
                        $query->where('has_yacht', '=', '1');
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
                                $insurance->insurance_date,
                                $insurance->date_from,
                                $insurance->date_to,
                                $insurance->contribution_lessor,
                                $status
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
            'data zawarcia polisy',
            'polisa od',
            'polisa do',
            'wysokość składki',
            'status'
        ];
    }
}