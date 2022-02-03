<?php
namespace Idea\Reports\InsurancesReports\Sheets;

use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class OtherReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

                $date_from = $this->parseDate($this->params['date_from'], '0');
                $date_to = $this->parseDate($this->params['date_to'], '+1');

                $sheet->appendRow(array('Zestawienie umów obcych '.$this->params['date_from'].' - '.$this->params['date_to']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                \LeasingAgreement::
                    whereBetween('created_at', array($date_from, $date_to))
                    ->where(function($query){
                        $query->where('import_insurance_company', 'like', '%obc%')
                            ->orWhereHas('insurances', function($query){
                                $query->where('if_foreign_policy', 1);
                            });
                    })
                    ->with('client', 'insurances', 'insurances.insuranceCompany', 'withCurrentResume', 'withArchiveResume')
                    ->chunk(100, function($agreements) use (&$sheet){
                        foreach ($agreements as $k => $leasingAgreement) {
                            $status = '';
                            if($leasingAgreement->insurances->isEmpty() && is_null($leasingAgreement->withdraw)){
                                $status = 'nowe';
                            }elseif(is_null($leasingAgreement->withdraw) && !is_null($leasingAgreement->archive)){
                                $status = 'archiwum';
                            }elseif( !is_null($leasingAgreement->withdraw) ){
                                $status = 'wycofana';
                            }elseif($leasingAgreement->insurances->count() > 0 && is_null($leasingAgreement->withdraw) && is_null($leasingAgreement->archive) && $leasingAgreement->withCurrentResume->first()){
                                $status = 'wznowienie aktualne';
                            }elseif($leasingAgreement->insurances->count() > 0 && is_null($leasingAgreement->withdraw) && is_null($leasingAgreement->archive) && $leasingAgreement->withArchiveResume->first()){
                                $status = 'wznowienie archiwalne';
                            }elseif($leasingAgreement->insurances->count() > 0 &&
                                $leasingAgreement->whereNull('withdraw')->whereNull('archive')
                                    ->whereHas('insurances', function($query){
                                        $query->active();
                                    })->first()){
                                $status = 'trwająca';
                            }
                            $sheet->appendRow(array(
                                $leasingAgreement->nr_contract,
                                $leasingAgreement->nr_agreement,
                                $leasingAgreement->client->name,
                                substr($leasingAgreement->created_at, 0, -3),
                                $status,
                                ($leasingAgreement->insurances->count() > 0) ? $leasingAgreement->insurances->last()->date_from : '---',
                                ($leasingAgreement->insurances->count() > 0) ? $leasingAgreement->insurances->last()->date_to : '---',
                                ($leasingAgreement->insurances->count() > 0) ? $leasingAgreement->insurances->last()->insuranceCompany->name : '---'
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
            'nr zgłoszenia',
            'leasingobiorca',
            'data zgłoszenia',
            'status umowy',
            'okres ubezp. Od',
            'okres ubezp. Do',
            'ubezpieczyciel'
        ];
    }
}