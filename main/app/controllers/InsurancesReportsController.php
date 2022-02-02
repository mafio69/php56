<?php

class InsurancesReportsController extends \BaseController
{


    function __construct()
    {
        $this->beforeFilter('permitted:raporty#wejscie');
    }

    public  function getIndex()
    {
        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $ownersDb = Owners::get();
        $owners = [];
        foreach($ownersDb as $owner)
        {
            $owners[$owner->id] = $owner->name.($owner->old_name ? ' ('.$owner->old_name.')' : '');
        }
        $general_contracts = LeasingAgreementInsuranceGroupRow::whereHas('insurance_group', function($query){
                                $query->whereHas('insuranceCompany', function($query){
                                    $query->where('name', 'like', '%hestia%');
                                });
                            })->lists('general_contract', 'general_contract');

        return View::make('insurances.reports.index', compact('insuranceCompanies', 'owners', 'general_contracts'));
    }

    public function getArchive()
    {
        $reports = LeasingAgreementReport::with('user', 'insurance_company', 'owner')->orderBy('id', 'desc')->paginate(Session::get('search.pagin', '10'));

        return View::make('insurances.reports.archive', compact('reports'));
    }

    public function getSheets()
    {
        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        return View::make('insurances.reports.sheets', compact('insuranceCompanies'));
    }

    public function getDownload($report_id)
    {
        $report = LeasingAgreementReport::findOrFail($report_id);

        $pathToFile = \Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/reports/generated/'.$report->filename.'.xls';

        if(file_exists($pathToFile))
        {
            ob_start();

            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $pathParts = pathinfo($pathToFile);

            $name = $report->filename;
            // Prepare the headers
            $headers = array(
                'Content-Description' => 'File Transfer',
                'Content-Type' => finfo_file($finfo, $pathToFile),
                'Content-Transfer-Encoding' => 'binary',
                'Expires' => 0,
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
                'Content-Length' => File::size($pathToFile),
                'Content-Disposition' => 'inline; filename="' . $name . '.' . $pathParts['extension'] . '"'
            );
            finfo_close($finfo);

            $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

            // If there's a session we should save it now
            if (Config::get('session.driver') !== '') {
                Session::save();
            }

            // Below is from http://uk1.php.net/manual/en/function.fpassthru.php comments
            session_write_close();
            if (ob_get_contents()) ob_end_clean();
            $response->sendHeaders();
            if ($file = fopen($pathToFile, 'rb')) {
                while (!feof($file) and (connection_status() == 0)) {
                    print(fread($file, 1024 * 8));
                    flush();
                }
                fclose($file);
            }

            // Finish off, like Laravel would
            Event::fire('laravel.done', array($response));
            //$response->foundation->finish();

            exit;
        }

        return Redirect::back();
    }

    public function postGenerate()
    {
        $passingParams = Input::all();
        $insuranceCompany = Insurance_companies::find($passingParams['insurance_company_id']);
        $report_type = $passingParams['report_type'];

	    try {
            if ($report_type == 'complex') {
                if (stripos(strtolower($insuranceCompany->name), "compensa") !== false)
                    $report = new \Idea\Reports\InsurancesReports\Complex\CompensaReport($passingParams);
                elseif (stripos(strtolower($insuranceCompany->name), "europa") !== false)
                    $report = new \Idea\Reports\InsurancesReports\Complex\EuropaReport($passingParams);
                elseif (stripos(strtolower($insuranceCompany->name), "hestia") !== false)
                    $report = new \Idea\Reports\InsurancesReports\Complex\HestiaReport($passingParams);
                else
                    throw new \Idea\Reports\ReportNotFoundException('Brak zdefiniowanego raportu dla wybranego ubezpieczyciela.');
            } else if ($report_type == 're-invoices') {
                if (stripos(strtolower($insuranceCompany->name), "compensa") !== false )
                    $report = new \Idea\Reports\InsurancesReports\ReInvoices\CompensaReport($passingParams);
                elseif (stripos(strtolower($insuranceCompany->name), "europa") !== false)
                    $report = new \Idea\Reports\InsurancesReports\ReInvoices\EuropaReport($passingParams);
                elseif (stripos(strtolower($insuranceCompany->name), "hestia") !== false)
                    $report = new \Idea\Reports\InsurancesReports\ReInvoices\HestiaReport($passingParams);
                else
                    throw new \Idea\Reports\ReportNotFoundException('Brak zdefiniowanego raportu dla wybranego ubezpieczyciela.');
            }elseif($report_type == "complex-refund"){
                if (stripos(strtolower($insuranceCompany->name), "europa") !== false)
                    $report = new \Idea\Reports\InsurancesReports\Refunds\EuropaReport($passingParams);
                else
                    throw new \Idea\Reports\ReportNotFoundException('Brak zdefiniowanego raportu dla wybranego ubezpieczyciela.');
            } else
                throw new \Idea\Reports\ReportNotFoundException('Brak zdefiniowanego raportu dla podanych parametrów.');
        }catch (\Idea\Reports\ReportNotFoundException $e) {
            Flash::error('Wystąpił błąd w trakcie generowanie raportu. '.$e->getMessage().' Skontaktuj się z administratorem.');
            Log::error('Wystąpił błąd w trakcie generowanie raportu', [$report_type, $passingParams, $e->getMessage()]);
            return Redirect::back()->withInput($passingParams);
        }

        return $report->generateReport();
    }

    public function postSheetOther()
    {
        $passingParams = Input::all();

        $filename  = 'zestawienie-umów-obcych-'.date('Y-m-d-H-i');

        $report = new Idea\Reports\InsurancesReport('Sheets\OtherReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function postSheetYachts()
    {
        $passingParams = Input::all();

        $filename  = 'zestawienie-jachtów-'.date('Y-m-d-H-i');

        $report = new Idea\Reports\InsurancesReport('Sheets\YachtsReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function postSheetObjects()
    {
        $passingParams = Input::all();

        $filename  = 'zestawienie-po-przedmiotach-'.date('Y-m-d-H-i');

        $report = new Idea\Reports\InsurancesReport('Sheets\ObjectsReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function postSheetDifferences()
    {
        $passingParams = Input::all();

        $filename = 'zestawienie-roznic-'.date('Y-m-d-H-i');

        $report = new Idea\Reports\InsurancesReport('Sheets\DifferencesReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function postSheetPropertyInsurances()
    {
        $passingParams = Input::all();

        $filename = 'zestawienie-ubezpieczen-majatkowych'.date('Y-m-d-H-i');

        $report = new Idea\Reports\InsurancesReport('Sheets\PropertyInsurancesReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function postSheetRefunds()
    {
        $passingParams = Input::all();

        $filename = 'rejestr-zwrotow-skladek'.date('Y-m-d-H-i');

        $report = new Idea\Reports\InsurancesReport('Sheets\RefundsReport', $filename, $passingParams);
        return $report->generateReport();
    }
}
