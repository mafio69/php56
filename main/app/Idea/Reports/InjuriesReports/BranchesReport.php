<?php
namespace Idea\Reports\InjuriesReports;

use Company;
use Config;
use Excel;
use Idea\Reports\BaseReport;
use Idea\Reports\ReportsCsvInterface;
use Idea\Reports\ReportsInterface;

class BranchesReport extends BaseReport implements ReportsInterface, ReportsCsvInterface{

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

                Company::whereHas('groups', function ($query)  {
                    $query->whereIn('company_groups.id', [1, 5]);
                })->chunk(100, function($companies) use(&$sheet){
                    $companies->load('branches', 'branches.brands', 'groups');

                    foreach($companies as $company) {
                        foreach ($company->branches as $branch) {
                            $sheet->appendRow(array(
                                $company->name,
                                $company->code,
                                $company->city,
                                $company->street,
                                $branch->short_name,
                                $branch->code,
                                $branch->city,
                                $branch->street,
                                implode(', ', $company->groups->lists('name')),
                                $company->nip,
                                implode(', ', $branch->brands->lists('name')),
                            ));
                        }
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
            'nazwa warsztatu',
            'kod warsztatu',
            'miasto warsztatu',
            'ulica warsztatu',
            'grupa serwisu',
            'NIP serwisu',
            'obsługiwane marki'
        );
    }
}