<?php
namespace Idea\Reports;

use Config;
use Custom_report_type;
use File;

class CustomReport extends BaseReport implements ReportsInterface{

    private $reportType;
    private $params;
    private $filename;

    function __construct($reportId, $filename, $params = array())
    {
        $this->reportType = Custom_report_type::find($reportId);
        $this->params = $params;
        $this->filename = $filename;
    }

    /**
     * @return mixed
     * @throws ReportNotFoundException
     */
    public function generateReport()
    {
        $path =  Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/'.$this->reportType->name;
        if(!File::exists($path)) {
            File::makeDirectory($path,511, true);
        }

        $reportClass = 'Idea\Reports\CustomReports\\'.ucfirst($this->reportType->class_name);
        if (class_exists($reportClass)) {
            $reportObject = new $reportClass($this->reportType, $this->filename, $this->params);
            return $reportObject->generateReport();
        }else{
            throw new ReportNotFoundException;
        }
    }
}