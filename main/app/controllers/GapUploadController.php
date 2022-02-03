<?php

class GapUploadController extends \BaseController {


    public function uploadFile($step)
    {
        \Debugbar::disable();

        $result = array();
        $file = Input::file('file');

        $mimes = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        if($mimes != 'application/vnd.ms-excel' && $extension != 'xls' && $extension != 'xlsx' && $extension != 'XLS' && $extension != 'XLSX'){
            $result['status'] = 'error';
            $result['msg'] = 'Niepoprawny format pliku. Obsługiwany format to .xls i .xlsx';
            return json_encode($result);
        }

        if($file) {
            $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/gap/'.$step;

            $randomKey  = sha1( time() . microtime() );
            $filename = $randomKey.'.'.$file->getClientOriginalExtension();

            if(!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath,511, true);
            }

            $upload_success = Input::file('file')->move($destinationPath, $filename);

            if ($upload_success) {
                $result['redirect'] = url('gap/upload/processing/'.$step.'/'.$filename);
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

    public function uploadDialog()
    {
        return View::make('gap.manage.dialog.fileupload');
    }

    public function processing( $step,$filename = null)
    {
        switch($step) {
            case 'new':
                $importFactory = new \Idea\Gap\NewAgreement\ImportNewFactory();
                break;
            default:
                $result['status'] = 'error';
                $result['msg'] = 'Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.';
                return json_encode($result);
                break;
        }

        $import = new \Idea\Gap\Import($importFactory);

        $parse_patern = $import->getDefaultParsePatern();

        $parse_patern[] = array('name'=>'Nie importowane','code'=>0);

        $paterns = array_pluck($parse_patern,'name','code');

        $results = $import->parseTest($filename);

        $owners = Owners::where('active', 0)->lists('name', 'id');
        $agreementTypes = LeasingAgreementType::lists('name', 'id');

        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $leasingAgreementPaymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
        $leasingAgreementPaymentWays[null] = 'nie zdefiniowano';

        return View::make('gap.manage.processing.'.$step, compact('filename', 'owners', 'agreementTypes', 'insuranceCompanies', 'leasingAgreementPaymentWays', 'results','paterns'));
    }

    public function parse($step, $filename)
    {
        switch($step) {
            case 'new':
                $importFactory = new \Idea\Gap\NewAgreement\ImportNewFactory();
                break;
            default:
                $result['status'] = 'error';
                $result['msg'] = 'Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.';
                return json_encode($result);
                break;
        }
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        $import = new \Idea\Gap\Import($importFactory);

        $paterns_init = Input::get('paterns');
        $patern_default = $import->getDefaultParsePatern();
        $duplicate = array();
        $patern = array();

        foreach($paterns_init as $key => $patern_init){
          if($patern_init!='0'){
            if(!in_array($patern_init,$patern)&&isset($patern_default[$key]))
              $patern[$key] = array('code'=>$patern_init,'name'=>$patern_default[$key]['name'],'type'=>$patern_default[$key]['type']);
            else
              $duplicate[] = $key;
          }
        }
      //  dd($paterns_init);
        if(count($duplicate)>0){
          $result['status'] = 'error_patern';
          $result['msg'] = 'Kilka kolumn wskazuje na tą samą wartość';
          $result['data']  = $duplicate;
          return json_encode($result);
        }
        if(count($patern_default)>count($patern)){
          $result['status'] = 'error_patern';
          $result['msg'] = 'Nie ustalono kolumn dla wszystkich wartości';
          $result['data']  = array();
          return json_encode($result);
        }

        $result = $import->parse($filename,$patern);

        Session::set('avoid_query_logging', false);

        return json_encode($result);
    }
}
