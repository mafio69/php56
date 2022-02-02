<?php

use Idea\SpreadsheetParser\SpreadsheetParser;
use Idea\Reports\InjuriesReport;

class InjuriesReportsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:pojazdy#raporty#wejscie');
    }

    public function index(){
        $skipped = Injury::where('skip_in_ending_report', 1)->where('active', 0)->count();
        return View::make('reports.injuries.index', compact('skipped'));
    }

    public function orders()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-zleceń-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('OrderReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function invoices()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-faktur-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('InvoiceReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function cfm()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-cfm-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('CFMReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function ordersGarages()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-zleceń-serwisy-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('OrderGaragesReport', $filename, $passingParams);
        return $report->generateReport();

    }

    public function ideaBank()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-idea-bank-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('IdeaBankReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function ideaBankLoan()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-idea-bank-pozyczka-konsumencka-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('IdeaBankLoanReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function redirections()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-przekierowań-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('RedirectionReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function attachment_12()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'Zestawienie-wystawionych-upoważnień-do-naliczenia-opłaty-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('Attachment12Report', $filename, $passingParams);
        return $report->generateReport();
    }

    public function attachment_12c()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'zał-12C-raport-szkód-całkowitych-i-kradzieżowych-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('Attachment12cReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function attachment_13()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'zał-nr-13-raport-szkód-częściowych'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('Attachment13Report', $filename, $passingParams);
        return $report->generateReport();
    }

    public function docs()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'zestawienie-dokumentow-przy-szkodach '.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('DocsReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function outdated()
    {
        set_time_limit(0);
        $passingParams = Input::all();
        $filename  = 'raport-zaległości'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('OutdatedReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function original_parameters()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-parametrow-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('OriginalParametersReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function completed_orders()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename  = 'raport-zlecen-zakonczonych-'.date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('CompletedOrdersReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function custom()
    {
        return View::make('reports.injuries.custom');
    }

    public function generateCustom()
    {
        set_time_limit(0);
        $passingParams = Input::all();
        $filename  = 'zestawienie-konfigurowalne';

        $report = new Idea\Reports\InjuriesReport('CustomReport', $filename, $passingParams);
        return $report->generateReport();
    }


    public function invoices_settled()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename = 'raport-faktur-rozliczonych-' . date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('InvoiceSettledReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function uploadList()
    {
        $result = array();
        $file = Input::file('file');

        if($file) {
            $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/files';

            $randomKey  = sha1( time() . microtime() );
            $filename = $randomKey.'.'.$file->getClientOriginalExtension();

            if(!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath,511, true);
            }

            $upload_success = Input::file('file')->move($destinationPath, $filename);

            $workbook = SpreadsheetParser::open($destinationPath.'/'.$filename);

            foreach($workbook->getWorksheets() as $sheet) {
                $sheetIndex = $workbook->getWorksheetIndex($sheet);

                $case_numbers = [];
                foreach ($workbook->createRowIterator($sheetIndex) as $rowIndex => $row) {
                    if(isset($row['A'])){
                        $case_numbers[] = $row['A'];
                    }
                }

                Injury::whereIn('case_nr', $case_numbers)->where('skip_in_ending_report', 0)->update(['skip_in_ending_report' => 1]);
            }

            if ($upload_success) {
                $result['status'] = 'success';
                return json_encode($result);
            } else {
                $result['status'] = 'error';
                $result['msg'] = 'Wystąpił błąd w trakcie wgrywania pliku. Skontaktuj się z administratorem.';
                return json_encode($result);
            }
        }
        return Response::json('error', 400);
    }

    public function excluded()
    {
        $injuries = Injury::where('active', 0)->where('skip_in_ending_report', 1)->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        return View::make('reports.injuries.excluded', compact('injuries'));
    }

    public function revert($injury_id)
    {
        $injury = Injury::find($injury_id);
        $injury->update(['skip_in_ending_report' => 0]);

        return Redirect::back();
    }

    public function branches()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename = 'raport-warsztatow-' . date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('BranchesReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function companiesWithGuardians()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename = 'raport-serwisów-z-opiekunami' . date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('CompaniesWithGuardiansReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function injuries_buyer()
    {
        set_time_limit(0);
        $passingParams = Input::all();

        $filename = 'raport-nabywcow-' . date('Y-m-d');

        $report = new Idea\Reports\InjuriesReport('InjuriesBuyerReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function sap()
    {
        $reports = InjurySapReport::orderBy('report_date', 'desc')->paginate(100);

        return View::make('reports.injuries.sap', compact('reports'));
    }

    public function downloadSap()
    {
        $report = InjurySapReport::find(Input::get('report_id'));

        $filepath = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/sap/'.$report->filename;
        return Response::download($filepath);
    }
}
