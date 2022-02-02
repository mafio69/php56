<?php
namespace Idea\Reports\InjuriesReports;

use Company;
use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class CompaniesWithGuardiansReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

                $sheet->appendRow($this->generateTheads());

                Company::whereNotNull('guardian_id')->chunk(100, function($companies) use(&$sheet){
                    // $companies->load('guardian');

                    foreach($companies as $company) {
                            $sheet->appendRow(array(
                                $company->name,
                                $company->code,
                                $company->city,
                                $company->street,
                                $company->nip,
                                $company->guardian->user->name,
                                $company->guardian->user->email,
                                $company->guardian->phone,

                            ));
                    }
                });
            });

        })->export('xls');
    }

    public function generateTheads()
    {
        return array(
            'nazwa serwisu',
            'kod serwisu',
            'miasto serwisu',
            'ulica serwisu',
            'NIP serwisu',
            'Opiekun',
            'E-mail',
            'Telefon',
        );
    }
}