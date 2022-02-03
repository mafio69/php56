<?php
namespace Idea\Reports\InsurancesReports\Sheets;

use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class ObjectsReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

                $sheet->appendRow(array('Zestawienie po przedmiotach zaczynających się od '.$this->params['object_name']));
                $sheet->appendRow(array());
                $sheet->appendRow($this->generateTheads());

                \LeasingAgreementObject::
                    where('name', 'like', $this->params['object_name'].'%')
                    ->with('leasing_agreement',  'leasing_agreement.insurances.insuranceCompany', 'leasing_agreement.insurances', 'leasing_agreement.client')
                    ->chunk(500, function($objects) use (&$sheet){
                        foreach($objects as $object){
                            if(! $object->leasing_agreement->insurances)
                            {
                                $sheet->appendRow(array(
                                    $object->name,
                                    $object->leasing_agreement->nr_contract,
                                    '---',
                                    '---',
                                    '---',
                                    '---',
                                    '---',
                                    '---',
                                    '---',
                                ));
                            }else {
                                foreach ($object->leasing_agreement->insurances as $insurance) {
                                    $sheet->appendRow(array(
                                        $object->name,
                                        $object->leasing_agreement->nr_contract,
                                        $insurance->insurance_number,
                                        ($object->leasing_agreement->client) ? $object->leasing_agreement->client->name : '---',
                                        $insurance->insurance_date,
                                        $insurance->date_from,
                                        $insurance->date_to,
                                        $insurance->contribution_lessor,
                                        ($insurance->insuranceCompany) ? $insurance->insuranceCompany->name : '---'
                                    ));
                                }
                            }
                        }

                    });
            });

        })->export('xls');
    }

    public function generateTheads()
    {
        return [
            'nazwa przemdmiotu',
            'nr umowy',
            'nr polisy',
            'nazwa leasingobiorcy',
            'data zawarcia polisy',
            'polisa od',
            'polisa do',
            'wysokość składki',
            'towarzystwo'
        ];
    }
}