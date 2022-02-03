<?php

class InsurancesInprogressController extends BaseController{

    public function processing()
    {
        set_time_limit(500);
        //$import = LeasingAgreementImport::groupBy('nr_contract')->having('cnt', '>', 1)->paginate(100,array('nr_contract', DB::raw('count(*) as cnt')));
        //$import = LeasingAgreementImport::groupBy('nr_contract')->having('cnt', '>', 1)->get(array('nr_contract', DB::raw('count(*) as cnt')))->count();
        //$import = LeasingAgreementImport::groupBy('nr_contract')->having('if_cession', '=', 1)->having('cnt', '>', 1)->get(array('nr_contract', 'if_cession', DB::raw('count(*) as cnt')));
        //$import = LeasingAgreementImport::groupBy('nr_contract')->having('cnt', '=', 1)->get(array('nr_contract', DB::raw('count(*) as cnt')))->count();
        $importSingleIds = LeasingAgreementImport::where('parsed', '=', 0)->where('if_cession', '=', 0)->groupBy('nr_contract')->having('cnt', '=', 1)->get(array('id', 'nr_contract', DB::raw('count(*) as cnt')))->lists('id');
        $import = LeasingAgreementImport::where('parsed', '=', 0)->where('if_cession', '=', 0)->whereIn('id', $importSingleIds)->get();
        unset($importSingleIds);
        return round(memory_get_usage(true)/1024,2)." kilobytes";

        return $import->count();
    }

    public function processingSingle()
    {
        set_time_limit(1000);
        DB::disableQueryLog();

        $start = microtime(true);
        $inprogressParser = new \Idea\LeasingAgreements\InprogressAgreement\InprogressParser();
        $importSingleIds = LeasingAgreementImport::where('parsed', '=', 0)->where('if_exist_cession', '=', 0)->groupBy('nr_contract')->having('cnt', '=', 1)
                            ->get(array('id', 'nr_contract', DB::raw('count(*) as cnt')))->take(3000)->lists('id');

        if(count($importSingleIds) > 0) {
            Session::set('avoid_query_logging', true);
            Log::info('start processing single');
            LeasingAgreementImport::whereIn('id', $importSingleIds)->chunk(250, function ($agreements) use ($inprogressParser) {
                Log::info('processing single');
                foreach ($agreements as $agreement) {
                    $inprogressParser->parseSingle($agreement->toArray());
                    $agreement->parsed = 1;
                    $agreement->save();
                }

            });
            Log::info('finished processing single');
            Session::set('avoid_query_logging', false);
        }else{
            return 'nothing left to parse';
        }

        $time_elapsed_us = microtime(true) - $start;

        return $time_elapsed_us.'s '.round(memory_get_usage(true)/1024,2)." kilobytes";
    }

    public function processingMultipleWithoutCession()
    {
        set_time_limit(1000);
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);
        $start = microtime(true);

        $multipleContracts = LeasingAgreementImport::distinct()->where('parsed', '=', 0)->where('if_exist_cession', '=', 0)->groupBy('nr_contract_pure')->having('cnt', '>', 1)
                            ->get(array('nr_contract_pure', DB::raw('count(*) as cnt')))->take(1500)->lists('nr_contract_pure');

        if(count($multipleContracts) > 0) {
            Session::set('avoid_query_logging', true);
            Log::info('start processing multiple without cession');

            foreach($multipleContracts as $contract)
            {
                $imports = LeasingAgreementImport::where('nr_contract_pure', '=', $contract)->get();

                $inprogressParser = new \Idea\LeasingAgreements\InprogressAgreement\InprogressParser();
                $inprogressParser->parseMultiple($imports->toArray());
                unset($inprogressParser);

                foreach($imports as $import)
                {
                    $import->parsed = 1;
                    $import->save();
                }
            }

            Log::info('finished processing single');
            Session::set('avoid_query_logging', false);
        }else{
            return 'nothing left to parse';
        }
        $time_elapsed_us = microtime(true) - $start;

        return $time_elapsed_us.'s '.round(memory_get_usage(true)/1024,2)." kilobytes";
    }

    public function processingSingleWithCession()
    {
        set_time_limit(1000);
        DB::disableQueryLog();
        $start = microtime(true);

        $importSingleCession = LeasingAgreementImport::where('parsed', '=', 0)->where('if_exist_cession', '=', 1)->groupBy('nr_contract_pure')->having('cnt', '=', 1)
            ->get(array('id', 'nr_contract_pure', DB::raw('count(*) as cnt')))->take(3000)->lists('nr_contract_pure');

        if(count($importSingleCession) > 0) {
            Session::set('avoid_query_logging', true);
            Log::info('start processing single');

            LeasingAgreementImport::whereIn('nr_contract_pure', $importSingleCession)->chunk(250, function ($agreements) {
                Log::info('processing single');
                foreach ($agreements as $agreement) {
                    $inprogressParser = new \Idea\LeasingAgreements\InprogressAgreement\InprogressParser();
                    $inprogressParser->parseSingleWithCession($agreement->toArray());
                    unset($inprogressParser);

                    $agreement->parsed = 1;
                    $agreement->save();
                }
            });

            Log::info('finished processing single');
            Session::set('avoid_query_logging', false);
        }else{
            return 'nothing left to parse';
        }

        $time_elapsed_us = microtime(true) - $start;

        return $time_elapsed_us.'s '.round(memory_get_usage(true)/1024,2)." kilobytes";
    }

    public function processingMultipleWithCession()
    {
        set_time_limit(1000);
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);
        $start = microtime(true);

        $multipleContracts = LeasingAgreementImport::distinct()->where('parsed', '=', 0)->where('if_exist_cession', '=', '1')->groupBy('nr_contract_pure')->having('cnt', '>', 1)
                            ->get(array('id', 'nr_contract_pure', DB::raw('count(*) as cnt')))->take(2000)->lists('nr_contract_pure');

        if(count($multipleContracts) > 0) {
            Session::set('avoid_query_logging', true);
            Log::info('start processing multiple with cession');

            foreach($multipleContracts as $contract)
            {
                $imports = LeasingAgreementImport::where('nr_contract_pure', '=', $contract)->get();

                $inprogressParser = new \Idea\LeasingAgreements\InprogressAgreement\InprogressParser();
                $inprogressParser->parseMultipleWithCession($imports->toArray());
                unset($inprogressParser);

                foreach($imports as $import)
                {
                    $import->parsed = 1;
                    $import->save();
                }
            }

            Log::info('finished processing with cession');
            Session::set('avoid_query_logging', false);
        }else{
            return 'nothing left to parse';
        }

        $time_elapsed_us = microtime(true) - $start;

        return $time_elapsed_us.'s '.round(memory_get_usage(true)/1024,2)." kilobytes";
    }

    public function activateLast()
    {
        set_time_limit(1000);
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        $insurances = LeasingAgreementInsurance::groupBy('leasing_agreement_id')->get(array(DB::raw('max(id) as max_id')))->lists('max_id');
        return $insurances;
        LeasingAgreementInsurance::whereIn('id', $insurances)->chunk(200, function($rows){
            foreach($rows as $row){
                $row->active = 1;
                $row->save();
            }
        });
    }

    public function moveToArchive()
    {
        set_time_limit(1000);
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        return LeasingAgreementInsurance::where('if_refund_contribution', 1)->whereHas('leasingAgreement', function($query){
            $query->whereNull('archive');
        })->chunk(200, function($rows){
            foreach($rows as $row){
                $agreement = $row->leasingAgreement;
                if(is_null($agreement->archive)) {
                    $agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
                    $agreement->save();

                    Histories::leasingAgreementHistory($agreement->id, 8);
                }
            }
        });
        Session::set('avoid_query_logging', false);
    }

}