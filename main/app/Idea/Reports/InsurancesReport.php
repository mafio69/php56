<?php
namespace Idea\Reports;

class InsurancesReport implements ReportsInterface {

    private $reportName;
    private $params;
    private $filename;

    function __construct($reportName, $filename, $params = array())
    {
        $this->reportName = 'Idea\Reports\InsurancesReports\\'.ucfirst($reportName);
        $this->params = $params;
        $this->filename = $filename;
    }


    /**
     * @return mixed
     * @throws ReportNotFoundException
     */
    public function generateReport()
    {
        if (class_exists($this->reportName)) {
            $reportObject = new $this->reportName($this->filename, $this->params);

            \CustomLog::info('reports', $this->reportName, $this->params);

            return $reportObject->generateReport();
        }else{
            throw new ReportNotFoundException($this->reportName);
        }
    }




}