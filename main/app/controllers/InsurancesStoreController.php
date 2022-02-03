<?php

class InsurancesStoreController extends \BaseController {

    /**
     * InsurancesStoreController constructor.
     */
    public function __construct()
    {
        $this->beforeFilter('permitted:kartoteka_polisy#zarzadzaj');
    }

    public function postAddNew()
    {
        if(Input::has('agreements')) {
            $agreements = Input::get('agreements');
            $storekeeper = new Idea\LeasingAgreements\NewAgreement\StoreNewAgreement();
            DB::disableQueryLog();
            Session::set('avoid_query_logging', true);

            foreach ($agreements as $agreement) {
                $agreement['agreement_data']['owner_id'] = Input::get('owner_id');
//                $agreement['agreement_data']['leasing_agreement_type_id'] = Input::get('leasing_agreement_type_id');
                $agreement['agreement_data']['creating_way'] = 2;
                $storekeeper->store($agreement);
            }
            Session::set('avoid_query_logging', false);

            Flash::success('Nowe umowy zostały wgrane do systemu.');
        }else
            Flash::message('Nie wykryto nowych umów do wgrania.');

        return Redirect::to('insurances/manage/index');

    }

    public function postAddResume()
    {
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);
        if(Input::has('toProceed'))
        {
            foreach(Input::get('toProceed') as $to_proceed => $on)
            {
                $agreement_id = Input::get('with_existing_insurance')[$to_proceed]['agreement_id'];
                $agreement = LeasingAgreement::find($agreement_id);

                $agreement->reported_to_resume = date('Y-m-d');
                $agreement->save();
            }
            Flash::message('Wybrane umowy zostały oznaczone do wznowienia.');
        }else{
            Flash::warning('Nie wybrano umów do wznowienia.');
        }
        Session::set('avoid_query_logging', false);

        return Redirect::to('insurances/manage/resume');
    }

    public function postAddYachts()
    {
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        switch(Input::get('leasing_agreement_type_id'))
        {
            case '1':
                $importer = new \Idea\LeasingAgreements\YachtAgreement\YachtLoanDocumentParser(Input::get('filename'), Input::all());
                break;
            case '2':
                $importer = new \Idea\LeasingAgreements\YachtAgreement\YachtLeasingDocumentParser(Input::get('filename'), Input::all());
                break;
        }

        if(!$importer->load())
        {
            $error = $importer->getMsg();
            Session::flash('error', $error);
            return Redirect::back()->withInput();
        }
        $import_result = $importer->parse_rows();

        Session::set('avoid_query_logging', false);

        if($import_result != 'success')
        {
            $error = $importer->getMsg();
            Session::flash('error', $error);
            return Redirect::back()->withInput();
        }
        if(count($importer->unparsedRows) > 0)
        {
            $unparsedRows = $importer->unparsedRows;
            $error = 'Istnieją wiersze dla których nie przeprowadzono importu ze względu na brak numeru umowy.';
            Session::flash('unparsedRows', $unparsedRows);
            return Redirect::back()->withInput();
        }
        Flash::success('Import umów jachtów zakończył się sukcesem.');
        return Redirect::to('insurances/manage/index');
    }

}
