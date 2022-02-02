<?php

class InsurancesUploadController extends \BaseController {


    /**
     * InsurancesUploadController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:wykaz_polis#wprowadzenie_umowy');
    }

    public function postUploadFile($step)
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
            $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/'.$step;

            $randomKey  = sha1( time() . microtime() );
            $filename = $randomKey.'.'.$file->getClientOriginalExtension();

            if(!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath,511, true);
            }

            $upload_success = Input::file('file')->move($destinationPath, $filename);

            if ($upload_success) {
                $result['redirect'] = URL::to('insurances/upload/processing', [$step,$filename]);
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

    public function getUploadDialog()
    {
        return View::make('insurances.manage.dialog.insuranceFileUpload');
    }

    public function getProcessing( $step,$filename = null)
    {
        $ownersDb = Owners::where('active', 0)->get();
        $owners = [];
        foreach($ownersDb as $owner)
        {
            if($owner->old_name && $owner->old_name != '') {
                $owners[$owner->id] = $owner->name . '(' . $owner->old_name . ')';
            }else{
                $owners[$owner->id] = $owner->name;
            }
        }
        $agreementTypes = LeasingAgreementType::lists('name', 'id');

        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');
        $leasingAgreementPaymentWays = LeasingAgreementPaymentWay::lists('name', 'id');
        $leasingAgreementPaymentWays[null] = 'nie zdefiniowano';

        return View::make('insurances.manage.processing/'.$step, compact('filename', 'owners', 'agreementTypes', 'insuranceCompanies', 'leasingAgreementPaymentWays'));
    }

    public function postParse($filename, $step)
    {
        switch($step) {
            case 'new':
                $importFactory = new \Idea\LeasingAgreements\NewAgreement\ImportNewFactory();
                break;
            case 'resume':
                $importFactory = new \Idea\LeasingAgreements\ResumeAgreement\ImportResumeFactory();
                break;
            case 'inprogress':
                break;
            default:
                $result['status'] = 'error';
                $result['msg'] = 'Wystąpił błąd w trakcie przetwarzania pliku. Skontaktuj się z administratorem.';
                return json_encode($result);
                break;
        }
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        $import = new \Idea\LeasingAgreements\Import($importFactory);
        $result = $import->parse($filename);

        Session::set('avoid_query_logging', false);
        return json_encode($result);
    }

    public function postParseCollection($filename, $limit = 200)
    {
        $importFactory = new \Idea\LeasingAgreements\CollectionAgreement\ImportCollection();
        $importFactory->import($filename);
        $result = $importFactory->parse($limit);
        return json_encode($result);
    }

    public function postParseReport($filename, $step)
    {
        $owner_id = Input::get('owner_id');
        $insurance_company_id = Input::get('insurance_company_id');
        $notification_number = Input::get('notification_number');
        $leasing_agreement_payment_way_id = Input::get('leasing_agreement_payment_way_id');
        $parser = new \Idea\LeasingAgreements\Reports\Complex\ReportComplexDocumentParser($filename, $owner_id, $insurance_company_id, $notification_number, $leasing_agreement_payment_way_id);

        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        $parser->load();
        $result = $parser->$step();

        Session::set('avoid_query_logging', false);
        if(isset($result['error']))
            return Response::json($result['error'], 406);

        return Response::json($result);
    }

    public function postImportPoliciesNumber()
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
            $destinationPath = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/policies';

            $randomKey  = sha1( time() . microtime() );
            $filename = $randomKey.'.'.$file->getClientOriginalExtension();

            if(!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath,511, true);
            }

            $upload_success = Input::file('file')->move($destinationPath, $filename);

            if ($upload_success) {
                $result['redirect'] = URL::to('insurances/upload/processing-policies-number', [$filename]);
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

    public function getProcessingPoliciesNumber( $filename = null)
    {
        $existInsuranceCompaniesInInsurancesGroups = LeasingAgreementInsuranceGroup::whereNotNull('insurance_company_id')->groupBy('insurance_company_id')->lists('insurance_company_id');
        $insuranceCompanies = Insurance_companies::whereIn('id', $existInsuranceCompaniesInInsurancesGroups)->orderBy('name')->lists('name', 'id');

        return View::make('insurances.manage.processing.policies-number', compact('filename', 'insuranceCompanies'));
    }

    public function postParsePolicies($filename)
    {
        $insurance_company_id = Input::get('insurance_company_id');

        $parser = new \Idea\LeasingAgreements\Reports\Policies\PoliciesDocumentParser($filename, $insurance_company_id);

        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        $parser->load();
        $result = $parser->parse_rows();

        Session::set('avoid_query_logging', false);
        if(!$result )
            return Response::json($parser->getMsg(), 406);

        return Response::json($result);
    }
}
