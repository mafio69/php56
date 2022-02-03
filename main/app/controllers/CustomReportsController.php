<?php

class CustomReportsController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:pojazdy#raporty#wejscie');
    }

    public function index()
    {
        $reports = Auth::user()->custom_reports()->get();
        return View::make('reports.custom.index', compact('reports'));
    }

    public function generate($report_id)
    {
        $report = Custom_report_type::find($report_id);
        return View::make('reports.custom.generate', compact('report'));
    }

    public function byContractESA(){
        set_time_limit(0);
        $passingParams = Input::all();
        $reportType = Custom_report_type::find(1);
        $filename  = 'raport-wg-'.$reportType->default_term.'-'.date('Y-m-d');

        $report = new Idea\Reports\CustomReport(1, $filename, $passingParams);

        return $report->generateReport();

    }

    public function authorizations()
    {
        set_time_limit(0);
        $passingParams = Input::all();
        $filename  = 'raport-wystawionych-upoważnień-'.date('Y-m-d');

        $report = new Idea\Reports\CustomReport(2, $filename, $passingParams);

        return $report->generateReport();
    }

    public function reportOC()
    {
        set_time_limit(0);
        $passingParams = Input::all();
        $filename  = 'raport-szkód-OC-2014';

        $report = new Idea\Reports\CustomReport(3, $filename, $passingParams);

        return $report->generateReport();
    }

    public function reportTheftTotal()
    {
        set_time_limit(0);
        $passingParams = Input::all();
        $filename  = 'raport-szkód-całkowitych-kradzież';

        $report = new Idea\Reports\CustomReport(4, $filename, $passingParams);

        return $report->generateReport();
    }

    public static function download($id, $filename)
    {
        ob_start();

        $report = Custom_report_type::find($id);

        $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER').'/reports/'.$report->name.'/'.$filename;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pathParts = pathinfo($path);

        // Prepare the headers
        if(Input::has('download')){
            $headers = array(
                'Content-Type' => finfo_file($finfo, $path),
                'Pragma' => 'no-cache',
                'Content-Length' => File::size($path),
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            );
        }else{
            $headers = array(
                'Content-Description' => 'File Transfer',
                'Content-Type' => finfo_file($finfo, $path),
                'Content-Transfer-Encoding' => 'binary',
                'Expires' => 0,
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
                'Content-Length' => File::size($path),
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            );
        }
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
        if ($file = fopen($path, 'rb')) {
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


}
