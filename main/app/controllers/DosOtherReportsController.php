<?php

use Idea\SpreadsheetParser\SpreadsheetParser;

class DosOtherReportsController extends BaseController {


    /**
     * DosInjuriesReportsController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:majatek#raporty#wejscie');
    }

    public function getIndex()
    {
        $skipped = DosOtherInjury::where('skip_in_ending_report', 1)->where('active', 0)->count();

        return View::make('reports.dos-other.index', compact('skipped'));
    }

    public function postOrders()
    {
        $passingParams = Input::all();

        $filename  = 'raport-zleceń-'.date('Y-m-d');

        $report = new Idea\Reports\DosOtherReport('OrdersReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function postCompletedOrders()
    {
        $passingParams = Input::all();

        $filename  = 'raport-zlecen-zakonczonych-'.date('Y-m-d');

        $report = new Idea\Reports\DosOtherReport('CompletedOrdersReport', $filename, $passingParams);
        return $report->generateReport();
    }

    public function postUploadList()
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

                $ids = [];
                foreach ($workbook->createRowIterator($sheetIndex) as $rowIndex => $row) {
                    if(isset($row['A'])){
                        $injury = DosOtherInjury::where('case_nr', $row['A'])->first();
                        if($injury && $injury->skip_in_ending_report == 0)
                        {
                            $ids[] = $injury->id;
                        }
                    }
                }
                if(count($ids)){
                    DosOtherInjury::whereIn('id', $ids)->update(['skip_in_ending_report' => 1]);
                }
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

    public function getExcluded()
    {
        $injuries = DosOtherInjury::where('active', 0)->where('skip_in_ending_report', 1)->orderBy('created_at','desc')->paginate(Session::get('search.pagin', '10'));

        return View::make('reports.dos-other.excluded', compact('injuries'));
    }

    public function postRevert($injury_id)
    {
        $injury = DosOtherInjury::find($injury_id);
        $injury->update(['skip_in_ending_report' => 0]);

        return Redirect::back();
    }
}
