<?php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DebugController extends BaseController
{

    /**
     * DebugController constructor.
     */
    public function __construct()
    {
        Event::fire('check.permission', array('debug'));
    }

    public function getInjuryDocument()
    {
	    exit();
        $injury = Injury::with('documents')->find(5984);
        return $injury->document(2,6)->get();
    }

    public function getMoveCancel($id)
    {
	    exit();
        $injury = Injury::find($id);
        $injury->prev_step = $injury->step;
        $injury->step = '-10';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->touch();

        Histories::history($id, 29, Auth::user()->id);

        //dodanie wątku rozmowy w kartotece
        $status = bindec('100');

        if (get_chat_group() == 1)
            $dos_read = date('Y-m-d H:i:s');
        else
            $dos_read = null;

        if (get_chat_group() == 3)
            $info_read = date('Y-m-d H:i:s');
        else
            $info_read = null;

        if (get_chat_group() == 2)
            $branch_read = date('Y-m-d H:i:s');
        else
            $branch_read = null;

        if($injury->canceled_chat_id == null) {
            $chat = InjuryChat::create(array(
                    'injury_id' => $id,
                    'user_id' => Auth::user()->id,
                    'topic' => 'Anulowanie szkody',
                    'status' => $status
                )
            );

            $injury->canceled_chat_id = $chat->id;
        }else{
            $chat = InjuryChat::find($injury->canceled_chat_id);
        }

        InjuryChatMessages::create(array(
                'chat_id'	=> $chat->id,
                'user_id'	=> Auth::user()->id,
                'content'	=> 'Przeniesienie szkody do anulowanych na życzenie Marty Grzeszczak',
                'status'	=> $status,
                'dos_read'	=> $dos_read,
                'info_read'	=> $info_read,
                'branch_read' => $branch_read
            )
        );
    }

    public function activateLast()
    {
	    exit();
        set_time_limit(1000);
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        $insurances = LeasingAgreementInsurance::groupBy('leasing_agreement_id')->get(array(DB::raw('max(id) as max_id')))->lists('max_id');
        return $insurances;
        LeasingAgreementInsurance::whereIn('id', $insurances)->chunk(200, function ($rows) {
            foreach ($rows as $row) {
                $row->active = 1;
                $row->save();
            }
        });
    }

    public function getMoveToArchive()
    {
	    exit();
        set_time_limit(1000);
        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        return LeasingAgreementInsurance::where('if_refund_contribution', 1)->whereHas('leasingAgreement', function ($query) {
            $query->whereNull('archive');
        })->chunk(200, function ($rows) {
            foreach ($rows as $row) {
                $agreement = $row->leasingAgreement;
                if (is_null($agreement->archive)) {
                    $agreement->archive = \Carbon\Carbon::now()->toDateTimeString();
                    $agreement->save();

                    Histories::leasingAgreementHistory($agreement->id, 8);
                }
            }
        });
        Session::set('avoid_query_logging', false);
    }

    public function getImportInprogress()
    {

    }

    public function getDocGenView($injury_id, $documentType_id)
    {
        Debugbar::disable();
        //testowanie wygenerowania dokumentu
        $inputs = [
            'description' => '',
            'nr_account' => 1,
            'person' => 'test',
            'nr_id' => 2,
            'car_location' => 'grudziąc'
        ]; // jeśli dokument potrzebuje przekazania danych do widoku
        $injuryTable = 'Injury'; //model z którego ma być brana szkoda, na chwilę obecną Injury

        $branch = 0;
        if(isset($inputs['branch'])) $branch = $inputs['branch'];
        $docGenerator = new Idea\DocGenerator\DocGenerator($injury_id, $injuryTable, $documentType_id, $inputs, $branch);
        return $docGenerator->generateDocView();
    }

    public function getDocGen($injury_id)
    {
        Debugbar::disable();
        //testowanie wygenerowania dokumentu
        $documentType_id = 91; //z tabeli *_document_type
        $inputs = array(); // jeśli dokument potrzebuje przekazania danych do widoku
        $injuryTable = 'Injury'; //model z którego ma być brana szkoda, na chwilę obecną Injury

        $injury = Injury::findOrFail($injury_id);

        $docGenerator = new Idea\DocGenerator\DocGenerator($injury_id, $injuryTable, $documentType_id, $inputs);
        $filename = str_random();
        $fs = new Illuminate\Filesystem\Filesystem();
        $view = $docGenerator->generateDocView();

        if(preg_match('/^.+\.(([pP][dD][fF]))$/', $view)){
            dd($view);
        }

        $fs->put( base_path('converter/'.$filename.'.html'), $view);

        $cmd = 'node convert.js --template='.$injury->vehicle->owner->documentTemplate->slug.' --infilename='.$filename.'.html --outfilename='.$filename.'.pdf --path="'.$docGenerator->savePath().'"';

        $process = new Process($cmd);
        $process->setWorkingDirectory(base_path('converter'));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
       // echo $docGenerator->getPath();
        echo PHP_EOL;
        echo $process->getOutput();
    }

    public function getDosDocGen()
    {
        //testowanie wygenerowania dokumentu
        $injury_id = 508;
        $documentType_id = 4; //z tabeli *_document_type

        $injury = DosOtherInjury::find($injury_id);
        $inputs = Input::all();
        $remarks = Text_contents::find($injury->remarks_damage);
        $idea = Idea_data::whereOwner_id($injury->object()->first()->owner_id)->get();
        if(isset($inputs['idea_office_id']))
            $ideaOffice = IdeaOffices::find($inputs['idea_office_id']);
        else
            $ideaOffice = '';

        $documentType = DosOtherInjuryDocumentType::find($documentType_id);

        $ideaA = array();
        foreach($idea as $setting)
        {
            $ideaA[$setting->parameter_id] = $setting->value;
        }

        return View::make('dos.other_injuries.docs_templates.'.$documentType->short_name,
            compact('injury', 'damage', 'damageSet', 'remarks', 'inputs', 'ideaA', 'ideaOffice')
        );
    }

	public function getInsuranceDocGen($policy_id, $type_id)
	{
		//testowanie wygenerowania dokumentu
		$inputs = array('annex_id' => '1', 'insuranceCompaniesPolicy'=> '2', 'annex_number' => 'xxx-08-30', 'refer' => 1, 'place'=> 'x', 'extra_options' => 'x', 'date_from' => '2018-02-06', 'date_to' => '2018-05-06', 'annex_value' => 0, 'type' => 1, 'return_date' => '2018-02-06', 'annex_content' => ''); // jeśli dokument potrzebuje przekazania danych do widoku
        $policy = LeasingAgreementInsurance::find($policy_id);
		$doc = new \Idea\DocGenerator\DocGeneratorPolicy($policy, $type_id, $inputs);
		return $doc->generateDocView();
	}

    public function getGenReport($id)
    {
        $report = new \Idea\Commissions\SettledReport($id);
        $filename = $report->generate();

        $reports = new \Idea\Commissions\AccountingReport($id);
        $filenameAccounting = $reports->generate();

        $report = CommissionReport::find($id);
        $report->update(['filename_settled' => $filename, 'filename_accounting' => $filenameAccounting,]);
    }

    public function getVbImport()
    {
        exit();
        ini_set('max_execution_time', '500');

        $vbInjuriesImport = new Idea\VB\VbInjuriesImport("RaportSzkod2.xls");
        $vbInjuriesImport->loadXLS();

        $highestRowInWorksheet = $vbInjuriesImport->highestRow;
        echo $highestRowInWorksheet . '<br/>';
        /***
         * $parsingStartingRow = 2;
         * $parsingHighestRow = 1000;
         *
         * $parsingStartingRow = 1001;
         * $parsingHighestRow = 2000;
         *
         * $parsingStartingRow = 2001;
         * $parsingHighestRow = 3000;
         *
         * $parsingStartingRow = 3001;
         * $parsingHighestRow = 4000;
         *
         * $parsingStartingRow = 4001;
         * $parsingHighestRow = 5000;
         *
         * $parsingStartingRow = 5001;
         * $parsingHighestRow = 5758;
         *
         * $parsingStartingRow = 5759;
         * $parsingHighestRow = 5964;
         ***/
        $vbInjuriesImport->parseWorksheet($parsingStartingRow, $parsingHighestRow);

        $vbInjuriesImport->import();

        echo 'done';
    }

    public function getWebservice($connection_id)
    {
	    exit();
        $nr_contract = '32343/14';
        $username = substr( Auth::user()->login, 0, 10);
        $data = new Idea\Structures\GETVEHICLEDTAInput($nr_contract, '', $username);

        $webservice = Webservice::establishSoap($connection_id)->generateParameters($data)->callSoap('getvehicledta_XML');

        $xml = $webservice->getResponseXML();
        //$xml = $xml->ANSWER->getVehicleDataReturn->getVehicle;
        echo '<pre>';
        dd($xml);
    }

    public function getWebserviceInjuryCheck($contract, $regnumber, $owner_id=1)
    {
	    exit();
        //$contract = '12423/13';
        //$regnumber = 'SK481EA';
        $user = 'przem_k';
        $data = new Idea\Structures\GETVEHICLEDTAInput($contract, $regnumber, $user);
        $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('GETVEHICLEDTA');
        $xml = $webservice->getResponseXML();
        return dd($xml);
    }

    public function getWebservice_check()
    {
	    exit();
        $injuries = Injury::with('vehicle')->where(function($query) {
            $query ->vehicleExists('register_as', 1);
        })->get();

        $contractList = array();
        $registrationList = array();
        foreach ($injuries as $injury) {
            if ($injury->vehicle->nr_contract != '')
                $contractList[$injury->vehicle->nr_contract] = $injury->vehicle->nr_contract;
            elseif ($injury->vehicle->registration != '')
                $registrationList[] = $injury->vehicle->registration;
        }

        if (count($contractList) > 0) {
            $xml = new SimpleXMLElement('<contractList/>');
            array_walk_recursive($contractList, array($xml, 'addChild'));
            $contlist = $xml->asXML();
            $data = new Idea\Structures\CHKCONTSTATEInput($contlist);
            $owners = Owners::whereActive('0')->where('wsdl', '!=', '')->get();

            $foundContracts = array();
            echo '<pre>';

            foreach ($owners as $owner) {
                echo $owner->name . '<br/>';
                $owner_id = $owner->id;

                $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('chkcontstate_XML');

                $xml = $webservice->getResponseXML();

                $errorCode = $xml->ANSWER->chkContStateReturn->Error->ErrorCde;
                dd($xml);
                if ($errorCode == 'ERR0000') {
                    $foundContractsXml = $xml->ANSWER->chkContStateReturn->stateList->contractState;

                    foreach ($foundContractsXml as $foundContract) {
                        $foundContracts[$foundContract->number->__toString()] = 1;
                    }
                } else {
                    Log::error('Błąd przy sprawdzaniu umów. ' . $xml->ANSWER->chkContStateReturn->Error->ErrorDes);
                    throw new \Whoops\Exception\ErrorException($xml->asXML());
                }
            }
            /*
            foreach($contractList as $contract => $v)
            {
                if( isset($foundContracts[$contract]) ){
                    echo $contract.' founded<br/>';
                    //$affectedRows = MobileInjury::where('nr_contract', '=', $contract)->update(array('if_on_as_server' => '1'));
                }else{
                    echo $contract.' not founded<br/>';
                    //$affectedRows = MobileInjury::where('nr_contract', '=', $contract)->update(array('if_on_as_server' => '-1'));
                }
            }
            */

            //print_r($foundContracts);
        }
    }

    public function getDosOtherInjuriesGenerateAssetTypes()
    {
        exit();
        $objects = Objects::all();

        foreach ($objects as $object) {
            $assetType = ObjectAssetType::where('name', '=', $object->assetType)->first();
            if (!$assetType) {
                if (trim($object->assetType) != '') {
                    $objectAssetType = ObjectAssetType::create(array(
                        'name' => $object->assetType
                    ));
                    $object->assetType_id = $objectAssetType->id;
                    $object->save();
                }

            } else {
                $object->assetType_id = $assetType->id;
                $object->save();
            }
        }
    }

    public function getMoveTotal($id)
    {
	    exit();
        $injury = Injury::find($id);
        $injury->prev_step = $injury->step;
        $injury->step = '-5';
        $injury->date_end = date("Y-m-d H:i:s");
        $injury->total_status_id = 11;
        $injury->touch();

        InjuryWreck::create(array(
            'injury_id' => $id
        ));

        Histories::history($id, 30, Auth::user()->id);

        if ($injury->save()) {
            $status = "CAŁKOWITA";

            $vehicle = Vehicles::find($injury->vehicle_id);
            $vehicle->contract_status = $status;
            $vehicle->touch();
            $vehicle->save();

            return 'success';
        }
    }

    public function getNonMapBranches()
    {
        exit();
        Excel::create('lista warsztatów bez wskazanego położenia na mapie', function($excel) {
            $excel->sheet('lista warsztatów', function($sheet) {

                $sheet->appendRow([
                    'typ serwisu',
                    'nazwa serwisu',
                    'nazwa warsztatu',
                    'kod',
                    'miasto',
                    'ulica',
                ]);

                Branch::with('company')->where('if_map', 0)->orderBy('company_id')->chunk(100, function($branches) use (&$sheet) {
                    foreach ($branches as $branch) {
                        $sheet->appendRow(array(
                            ($branch->company->type == 1) ? 'serwis idea' : 'serwis inny',
                            $branch->company->name,
                            $branch->short_name,
                            $branch->code,
                            $branch->city,
                            $branch->street
                        ));
                    }
                });

            });

        })->download();
    }

    public function getBranches()
    {
        Excel::create('lista warsztatów', function($excel) {
            $excel->sheet('lista warsztatów', function($sheet) {

                $sheet->appendRow([
                    'nazwa serwisu',
                    'kod serwisu',
                    'miasto serwisu',
                    'ulica serwisu',
                    'nazwa warsztatu',
                    'kod warsztatu',
                    'miasto warsztatu',
                    'ulica warsztatu',
                    'grupa serwisu',
                    'NIP serwisu'
                ]);

                Company::whereHas('groups', function ($query)  {
                    $query->whereIn('company_groups.id', [1, 5]);
                })->chunk(100, function($companies) use(&$sheet){
                    $companies->load('branches', 'groups');

                    foreach($companies as $company) {
                        foreach ($company->branches as $branch) {
                            $sheet->appendRow(array(
                                $company->name,
                                $company->code,
                                $company->city,
                                $company->street,
                                $branch->short_name,
                                $branch->code,
                                $branch->city,
                                $branch->street,
                                implode(', ', $company->groups->lists('name')),
                                $company->nip
                            ));
                        }
                    }
                });

            });

        })->download();
    }

    public function getChangeOwners()
    {
        exit();
        $injuries = Injury::vehicleExists('owner_id', 3, 'where')
                            ->vehicleExists('cfm', 1, 'where')
                            ->whereNotIn('step', array('15', '17', '19', '20', '-7') )
                            ->get();
        foreach($injuries as $injury)
        {
            $injury->vehicle->owner_id = 10;
            $injury->vehicle->save();
        }

        return 'done';
    }

    public function getListId()
    {
        exit();
        Debugbar::disable();
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename=export.csv');
        $injuries = Injury::vehicleExists('owner_id', 3, 'where')
            ->vehicleExists('cfm', 0, 'where')
            ->whereNotIn('step', array('15', '17', '19', '20', '-7') )
            ->lists('id');
        $injuries = implode(',', $injuries);

        $fp = fopen('php://output', 'w');

        fputcsv($fp, [$injuries]);

        fclose($fp);

    }

    public function getImportId()
    {
        exit();
        $file = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/imports/injuries/export_id.csv";
        if (($handle = fopen($file, "r")) !== FALSE) {
            $data = fgetcsv($handle, 1000, ",");
            $data = $data[0];
            fclose($handle);

            $ids = explode(',', $data);
            Injury::with('vehicle')->whereIn('id', $ids)->chunk(200, function($injuries)
            {
                foreach ($injuries as $injury)
                {
                    $injury->vehicle->register_as = 0;
                    $injury->vehicle->save();
                }
            });

            return 'done';

        }

    }

    public function getChangeInsuranceOwners()
    {
        exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);
        LeasingAgreement::where('owner_id', 3)->chunk(200, function($agreements)
        {
            foreach ($agreements as $agreement)
            {
                $agreement->owner_id = 1;
                $agreement->save();
            }
        });


        return 'done';
    }

    public function getZestawienie()
    {
        exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);
        Excel::create('zestawienie spraw po 1 czerwca', function($excel) {
            $excel->sheet('lista warsztatów', function($sheet) {

                $sheet->appendRow([
                    'nr umowy',
                    'nr rejestracyjny',
                    'właściciel',
                    'nr sprawy',
                ]);

                Injury::where('created_at', '>', '2015-06-01 00:00:00')->with('vehicle', 'vehicle.owner')->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->owner->name,
                            $injury->case_nr
                        ));
                    }
                });
            });

        })->download();
    }

    public function getInsurancesList()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        Excel::create('lista zmodyfikowanych umów', function($excel) {
            $excel->sheet('lista umów', function($sheet) {

                $sheet->appendRow([
                    'nr umowy',
                ]);

                $agreements_history = LeasingAgreementHistory::distinct()->select('leasing_agreement_id')->where('notification_number', '03/2015')->whereHas('agreement', function($query){
                    $query->where('owner_id', 3);
                })->getQuery();

                $agreements = LeasingAgreementInsurance::distinct()->select('leasing_agreement_id')->where('notification_number', '03/2015')->whereHas('leasingAgreement', function($query){
                    $query->where('owner_id', 3);
                })->union($agreements_history)->lists('leasing_agreement_id');

                LeasingAgreement::whereIn('id', $agreements)->chunk(100 , function($agreements) use (&$sheet) {
                        foreach ($agreements as $agreement) {
                            $sheet->appendRow(array(
                                $agreement->nr_contract,
                            ));

                            $agreement->owner_id = 1;
                            $agreement->save();
                        }
                    });
            });

        })->download();
    }

    public function getParseInsurance($filename)
    {
	    exit();
        $importFactory = new \Idea\LeasingAgreements\NewAgreement\ImportNewFactory();

        DB::disableQueryLog();
        Session::set('avoid_query_logging', true);

        $import = new \Idea\LeasingAgreements\Import($importFactory);
        $result = $import->parse($filename);

        Session::set('avoid_query_logging', false);
        return json_encode($result);
    }

    public function getDeleteCompanies()
    {
	    exit();
        $companies_to_delete = Company::where('active', '9')->lists('id');

        Company::whereIn('id', $companies_to_delete)->delete();
    }

    public function getSetCompaniesGroups()
    {
	    exit();
        Company::get()->each(function($company){
            $company_group_id = $company->company_group_id;
            $company->groups()->attach($company_group_id);
        });
    }

    public function getImportVmanage()
    {
	    exit();
        Debugbar::disable();
        $importer = new \Idea\Vmanage\Imports\ImportIdeaFleet17112015('export17112015.xlsx');
        $importer->load();
        $importer->parseRows();
    }

    public function getImportYacht()
    {
	    exit();
        $importer = new \Idea\LeasingAgreements\YachtAgreement\YachtLeasingDocumentParser('JACHTY.xls', ['owner_id' => 1]);
        $importer->load();
        $importer->parse_rows();
        dd($importer->unparsedRows);
    }

    public function getRemoveOldYacht()
    {
	    exit();
        $agreements = LeasingAgreement::where('has_yacht', 1)->where('created_at', '<', '2015-07-01 00:00:00')->get();
        foreach($agreements as $agreement)
        {
            $agreement->delete();
        }
    }

    public function getInsurancesYachtList()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        Excel::create('lista polis dla umów jachtów', function($excel) {
            $excel->sheet('lista umów', function($sheet) {

                $sheet->appendRow([
                    'nr umowy',
                    'nr polisy',
                    'data polisy',
                    'polisa od',
                    'polisa do',
                    'liczba miesięcy',
                    'składka leasingobiorcy'
                ]);

                LeasingAgreement::has('insurances', '>', 0)
                    ->where('has_yacht', 1)
                    ->with('insurances')
                    ->chunk(100 , function($agreements) use (&$sheet) {
                        foreach ($agreements as $agreement) {
                            foreach($agreement->insurances as $insurance) {
                                $sheet->appendRow(array(
                                    $agreement->nr_contract,
                                    $insurance->insurance_number,
                                    $insurance->insurance_date,
                                    $insurance->date_from,
                                    $insurance->date_to,
                                    $insurance->months,
                                    $insurance->contribution_lessor
                                ));
                            }
                        }
                    });
            });

        })->download();
    }

    public function getInsurances()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        $agreements = LeasingAgreement::with('insurances')->has('insurances', '>', 2)->get();
        set_time_limit(500);

        $suspects = [];

        foreach($agreements as $agreement)
        {
            $last_insurance_date_from = null;
            foreach($agreement->insurances as $insurance)
            {
                if(!is_null($insurance->date_from) && $insurance->date_from != '0000-00-00' && \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $insurance->created_at) > \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '2015-04-02 23:59:59')) {
                    if (is_null($last_insurance_date_from))
                        $last_insurance_date_from = \Carbon\Carbon::createFromFormat('Y-m-d', $insurance->date_from);
                    else {
                        $current_date_from = \Carbon\Carbon::createFromFormat('Y-m-d', $insurance->date_from);
                        if ($last_insurance_date_from > $current_date_from) {
                            $suspects[] = $agreement;
                            break;
                        } else {
                            $last_insurance_date_from = $current_date_from;
                        }
                    }
                }
            }
        }

        Excel::create('lista podejrzanych umów', function($excel) use($suspects) {
            $excel->sheet('lista umów', function($sheet) use($suspects){

                $sheet->appendRow([
                    'nr umowy',
                    'nr zgłoszenia',
                ]);


                foreach($suspects as $suspect) {
                    $sheet->appendRow(array(
                        $suspect->nr_contract,
                        $suspect->nr_agreement,
                    ));
                }

            });

        })->download();
    }

    public function getExcelArchive()
    {
	    exit();
        $leasingAgreements = LeasingAgreement::
            distinct()
            ->whereNull('withdraw')
            ->whereNotNull('archive')
            ->whereBetween('archive', array(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '2015-09-21 00:00:00'), \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', '2015-09-24 59:59:00')))->get();

        Excel::create('lista umów przeniesionych do archiwum od 21-09 do 24-09', function($excel) use($leasingAgreements) {
            $excel->sheet('lista umów ', function($sheet) use($leasingAgreements){

                $sheet->appendRow([
                    'nr umowy',
                    'nr zgłoszenia',
                    'data przeniesienia do archiwum'
                ]);


                foreach($leasingAgreements as $agreement){
                    $sheet->appendRow(array(
                        $agreement->nr_contract,
                        $agreement->nr_agreement,
                        $agreement->archive
                    ));
                }
            });

        })->download();
    }

    public function getForeign()
    {
	    exit();
        LeasingAgreementInsurance::whereHas('leasingAgreement', function($query){
            $query->where('import_insurance_company', 'like', '%obc%');
        })->where('if_foreign_policy', 0)->chunk(200, function($insurances)
        {
            foreach ($insurances as $insurance)
            {
                $insurance->if_foreign_policy = 1;
                $insurance->save();
            }
        });
    }

    public function getBledne()
    {
	    exit();
        $file = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER').'/insurances/new/szkody_wyc.xlsx';
        $reader = Excel::load($file, 'windows-1250');
        $objWorksheet = $reader->getActiveSheet();

        $maxCell = $objWorksheet->getHighestRowAndColumn();
        $data = $objWorksheet->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'],
            NULL,
            TRUE,
            FALSE,
            TRUE);
        $data = array_map('array_filter', $data);
        $rows = array_filter($data);
        unset($rows[1]);
        $contract_numbers = [];
        foreach($rows as $row)
        {
            if(isset($row['B']))
                $contract_numbers[$row['B']] = 1;
        }

        $contract_numbers_list = [];

        foreach($contract_numbers as $number => $k)
        {
            $contract_numbers_list[] = $number;
        }

        $agreements = LeasingAgreement::whereIn('nr_contract', $contract_numbers_list)->lists('id', 'nr_contract');

        //dd($agreements);

        $agreements = LeasingAgreement::whereIn('nr_contract', $contract_numbers_list)->has('history', 1)->lists('id', 'nr_contract');
        dd($agreements);
    }

    public function getBuyers()
    {
	    exit();
        $wrecks = InjuryWreck::where('buyer', '!=', 0)->get();

        foreach($wrecks as $wreck)
        {
            $buyer = Buyer::where('name', $wreck->buyer_name)
                    ->where('address_street', $wreck->buyer_address_street)
                    ->where('address_code', $wreck->buyer_address_code)
                    ->where('address_city', $wreck->buyer_address_city)
                    ->where('nip', $wreck->buyer_nip)
                    ->where('regon', $wreck->buyer_regon)
                    ->where('account_nr', $wreck->buyer_account_nr)
                    ->where('phone', $wreck->buyer_phone)
                    ->where('email', $wreck->buyer_email)
                    ->where('contact_person', $wreck->buyer_contact_person)
                    ->first();

            if(! $buyer)
            {
                $buyer = Buyer::create([
                    'name' => $wreck->buyer_name,
                    'address_street' => $wreck->buyer_address_street,
                    'address_code' => $wreck->buyer_address_code,
                    'address_city' => $wreck->buyer_address_city,
                    'nip' => $wreck->buyer_nip,
                    'regon' => $wreck->buyer_regon,
                    'account_nr' => $wreck->buyer_account_nr,
                    'phone' => $wreck->buyer_phone,
                    'email' => $wreck->buyer_email,
                    'contact_person' => $wreck->buyer_contact_person,
                ]);
            }

            $wreck->buyer_id = $buyer->id;
            $wreck->save();
        }
    }

    public function getSameNumbers()
    {
	    exit();
        DB::disableQueryLog();
        Excel::create('lista umów z tym samym numerem zgłoszenia', function($excel){
            $excel->sheet('lista umów', function($sheet){

                $sheet->appendRow([
                    'nr umowy',
                    'nr zgłoszenia',
                    'liczba polis z tym samym numerem zgłoszenia',
                    'właściciel'
                ]);

                LeasingAgreementInsurance::
                        select('leasing_agreement_id', 'notification_number',  DB::raw('count(*) as ct'))->
                        whereNotNull('notification_number')->
                        whereNotIn('notification_number', ['', '(auto)'])->
                        groupBy('notification_number')->
                        groupBy('leasing_agreement_id')->
                        having('ct', '>', 1)->
                        latest()->
                        with('leasingAgreement', 'leasingAgreement.owner', 'insuranceCompany')->
                chunk(500, function($insurances) use($sheet){
                    foreach($insurances as $insurance){
                        $sheet->appendRow(array(
                            $insurance->leasingAgreement->nr_contract,
                            $insurance->notification_number,
                            $insurance->ct,
                            $insurance->leasingAgreement->owner->name
                        ));
                    }
                });


            });

        })->download();
    }

    public function getSheetYachts()
    {
	    exit();
        $filename  = 'zestawienie-jachtów';

        $report = new Idea\Reports\InsurancesReports\Sheets\YachtsSimpleReport($filename);
        return $report->generateReport();
    }

    public function getCompletedToMove()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        Excel::create('lista spraw do przemianiowania w zakończonych', function($excel){
            $excel->sheet('lista spraw przed 01-01-2015', function($sheet){
                $sheet->appendRow([
                    'status zmieniony na zakończone',
                ]);
                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'nr szkody'
                ]);

                Injury::where('active', '=', '0')->whereIn('step', array('17',  '19') )
                    ->where('created_at', '<', \Carbon\Carbon::createFromFormat('Y-m-d', '2015-01-01'))
                    ->chunk(1000, function($injuries) use($sheet){
                        foreach($injuries as $injury){
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->injury_nr,
                            ));
                        }
                    });
            });


            $excel->sheet('lista spraw po 01-01-2015', function($sheet) {
                $sheet->appendRow([
                    'status zmieniony na zakończone (dopięta decyzja : "zakończona")',
                ]);
                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'nr szkody'
                ]);

                Injury::where('active', '=', '0')
                    ->whereIn('step', array( '17', '19'))
                    ->where('created_at', '>', \Carbon\Carbon::createFromFormat('Y-m-d', '2014-12-31'))
                    ->whereHas('documents',
                        function ($query) {
                            $query->where('type', 2)->where('category', 6);
                        }, '>', 0
                    )->
                    chunk(1000, function ($injuries) use ($sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->injury_nr
                            ));
                        }
                    });
            });

            $excel->sheet('lista spraw po 01-01-2015', function($sheet) {
                $sheet->appendRow([
                    'status zmieniony na w obsłudze (brak decyzji : "w obsłudze")',
                ]);
                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'nr szkody'
                ]);

                Injury::where('active', '=', '0')
                    ->whereIn('step', array( '17', '19'))
                    ->where('created_at', '>', \Carbon\Carbon::createFromFormat('Y-m-d', '2014-12-31'))
                    ->whereHas('documents',
                        function ($query) {
                            $query->where('type', 2)->where('category', 6);
                        }, '<', 1
                    )
                    ->where(function ($query) {
                        $query->vehicleExists('cfm', 0, 'where')
                            ->orWhere('step', '!=', '17');
                    })
                    ->chunk(1000, function ($injuries) use ($sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->injury_nr
                            ));
                        }
                    });

            });
            $excel->sheet('lista spraw po 01-01-2015', function($sheet){
                $sheet->appendRow([
                    'status zmieniony na rozliczona (szkoda CFM i status "zakończona bez liwkidacji")',
                ]);
                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'nr szkody'
                ]);

                Injury::where('active', '=', '0')
                    ->whereIn('step', array('17') )
                    ->where('created_at', '>', \Carbon\Carbon::createFromFormat('Y-m-d', '2014-12-31'))
                    ->vehicleExists('cfm', 1, 'where')
                    ->chunk(1000, function($injuries) use($sheet){
                        foreach($injuries as $injury){
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->injury_nr
                            ));
                        }
                    });
            });

        })->download();

        return View::make('panel.home');
    }

    public function getChangeCompletedStatuses()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $user_id = Auth::user()->id;
        $data = date('Y-m-d H:i:s');
        $rowsToInsert = [];

        $injury_ids = Injury::where('active', '=', '0')
                    ->whereIn('step', array('17',  '19') )
                    ->where('created_at', '<', \Carbon\Carbon::createFromFormat('Y-m-d', '2015-01-01'))
                    ->lists('id');

        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);
        $rowsToInsert = [];

        Injury::whereIn('id', $injury_ids)->update(['step' => '15']);
        CustomLog::info('changing_statuses', 'lista spraw przed 01-01-2015 - status zmieniony na zakończone', ['ids' => json_encode($injury_ids)]);
        echo 'lista spraw przed 01-01-2015 - status zmieniony na zakończone:'.count($injury_ids);
        echo '</br>';



        $injury_ids = Injury::where('active', '=', '0')
            ->whereIn('step', array( '17', '19'))
            ->where('created_at', '>', \Carbon\Carbon::createFromFormat('Y-m-d', '2014-12-31'))
            ->whereHas('documents',
                function ($query) {
                    $query->where('type', 2)->where('category', 6);
                }, '<', 1
            )
            ->where(function ($query) {
                $query->vehicleExists('cfm', 0, 'where')
                    ->orWhere('step', '!=', '17');
            })
            ->lists('id');

        $value = "Przeniesiono na etap w obsłudze w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);
        $rowsToInsert = [];

        Injury::whereIn('id', $injury_ids)->update(['step' => '10']);
        CustomLog::info('changing_statuses', 'lista spraw po 01-01-2015 - status zmieniony na w obsłudze (brak decyzji : "w obsłudze")', ['ids' => json_encode($injury_ids)]);
        echo 'lista spraw po 01-01-2015 - status zmieniony na w obsłudze (brak decyzji : "w obsłudze"):'.count($injury_ids);
        echo '</br>';

        $injury_ids = Injury::where('active', '=', '0')
            ->whereIn('step', array('17') )
            ->where('created_at', '>', \Carbon\Carbon::createFromFormat('Y-m-d', '2014-12-31'))
            ->vehicleExists('cfm', 1, 'where')
            ->lists('id');

        $value = "Przeniesiono na etap rozliczone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);
        $rowsToInsert = [];

        Injury::whereIn('id', $injury_ids)->update(['step' => '16']);
        CustomLog::info('changing_statuses', 'lista spraw po 01-01-2015 - status zmieniony na rozliczona (szkoda CFM i status "zakończona bez liwkidacji")', ['ids' => json_encode($injury_ids)]);
        echo 'lista spraw po 01-01-2015 - status zmieniony na rozliczona (szkoda CFM i status "zakończona bez liwkidacji"):'.count($injury_ids);
        echo '</br>';

        $injury_ids = Injury::where('active', '=', '0')
            ->whereIn('step', array( '17', '19'))
            ->where('created_at', '>', \Carbon\Carbon::createFromFormat('Y-m-d', '2014-12-31'))
            ->whereHas('documents',
                function ($query) {
                    $query->where('type', 2)->where('category', 6);
                }, '>', 0
            )->lists('id');
        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);

        Injury::whereIn('id', $injury_ids)->update(['step' => '15']);
        CustomLog::info('changing_statuses', 'lista spraw po 01-01-2015 - status zmieniony na zakończone (dopięta decyzja : "zakończona")', ['ids' => json_encode($injury_ids)]);
        echo 'lista spraw po 01-01-2015 - status zmieniony na zakończone (dopięta decyzja : "zakończona"):'.count($injury_ids);
        echo '</br>';

        return 'done';
    }

    public function getRepairChangeCompletedStatuses()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $user_id = Auth::user()->id;
        $data = date('Y-m-d H:i:s');
        $rowsToInsert = [];

        $injury_ids = Injury::where('active', '=', '0')
            ->whereIn('step', array('15') )
            ->vehicleExists('cfm', 1, 'where')
            ->lists('id');


        $value = "Przeniesiono na etap rozliczone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);

        Injury::whereIn('id', $injury_ids)->update(['step' => '16']);
        CustomLog::info('changing_statuses', 'lista spraw - status zmieniony na rozliczona (szkoda CFM i status "zakończona")', ['ids' => json_encode($injury_ids)]);
        echo 'lista spraw - status zmieniony na rozliczona (szkoda CFM i status "zakończona"):'.count($injury_ids);
        echo '</br>';

        return 'done';
    }

    public function getNewToCompleted()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        Excel::create('lista spraw do przemianiowania na zakończone', function($excel){

            $excel->sheet('ze statusu nowe', function($sheet) {
                $sheet->appendRow([
                    'Szkody na statusie „nowa”, do której dopięta jest decyzja wypłaty odszkodowania, a nie są CFMem – przechodzi na status „zakończona”',
                ]);
                $sheet->appendRow([
                    'nr sprawy',
                    'nr szkody'
                ]);

                Injury::where('active', '=', '0')
                    ->where('step', '0')
                    ->vehicleExists('cfm', 0, 'where')
                    ->whereHas('documents',
                        function ($query) {
                            $query->where('type', 2)->where('category', 6);
                        }, '>', 0
                    )->
                    chunk(1000, function ($injuries) use ($sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->injury_nr
                            ));
                        }
                    });
            });

            $excel->sheet('w obsłudze', function($sheet) {
                $sheet->appendRow([
                    'Szkody na statusie „ w obsłudze”, do której dopięta jest decyzja wypłaty odszkodowania, nie jest to CFM, nie ma serwisu lub jest procedowane w nie naszym serwisie, nie ma wygenerowania zlecenia – przechodzi na status „zakończona”',
                ]);
                $sheet->appendRow([
                    'nr sprawy',
                    'nr szkody'
                ]);

                Injury::where('active', '=', '0')
                    ->where('step', '10')
                    ->whereHas('documents',
                        function ($query) {
                            $query->where('type', 2)->where('category', 6);
                        }, '>', 0
                    )
                    ->vehicleExists('cfm', 0, 'where')
                    ->whereDoesntHave('documents',
                        function ($query) {
                            $query->where('type', 3)->where('category', 6);
                        }
                    )->where(function($query){
                        $query->where('branch_id', '<', 1)
                            ->orWhereHas('branch', function($query){
                                $query->whereHas('company', function($query){
                                    $query->whereDoesntHave('groups');
                                });
                            });
                    })
                    ->chunk(1000, function ($injuries) use ($sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->injury_nr
                            ));
                        }
                    });
            });


        })->download();

        return View::make('panel.home');
    }

    public function getChangeNewToCompleted(){
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $user_id = Auth::user()->id;
        $data = date('Y-m-d H:i:s');
        $rowsToInsert = [];

        $injury_ids = Injury::where('active', '=', '0')
            ->where('step', '0')
            ->vehicleExists('cfm', 0, 'where')
            ->whereHas('documents',
                function ($query) {
                    $query->where('type', 2)->where('category', 6);
                }, '>', 0
            )->lists('id');

        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);

        Injury::whereIn('id', $injury_ids)->update(['step' => '15']);
        CustomLog::info('changing_statuses', 'lista spraw na statusie nowa, do której dopięta jest decyzja wypłaty odszkodowania, a nie są CFMem  - status zmieniony na zakończone', ['ids' => json_encode($injury_ids)]);
        echo 'lista spraw na statusie nowa, do której dopięta jest decyzja wypłaty odszkodowania, a nie są CFMem  - status zmieniony na zakończone:'.count($injury_ids);

        return 'done';
    }

    public function getChangeInprogressToCompleted(){
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $user_id = Auth::user()->id;
        $data = date('Y-m-d H:i:s');
        $rowsToInsert = [];

        $injury_ids = Injury::where('active', '=', '0')
            ->where('step', '10')
            ->whereHas('documents',
                function ($query) {
                    $query->where('type', 2)->where('category', 6);
                }, '>', 0
            )
            ->vehicleExists('cfm', 0, 'where')
            ->whereDoesntHave('documents',
                function ($query) {
                    $query->where('type', 3)->where('category', 6);
                }
            )->where(function($query){
                $query->where('branch_id', '<', 1)
                    ->orWhereHas('branch', function($query){
                        $query->whereHas('company', function($query){
                            $query->whereDoesntHave('groups');
                        });
                    });
            })->lists('id');

        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);

        Injury::whereIn('id', $injury_ids)->update(['step' => '15']);
        CustomLog::info('changing_statuses', 'Szkody na statusie w obsłudze, do której dopięta jest decyzja wypłaty odszkodowania, nie jest to CFM, nie ma serwisu lub jest procedowane w nie naszym serwisie, nie ma wygenerowania zlecenia - status zmieniony na zakończone', ['ids' => json_encode($injury_ids)]);
        echo 'lista spraw na statusie w obsłudze, do której dopięta jest decyzja wypłaty odszkodowania, nie jest to CFM, nie ma serwisu lub jest procedowane w nie naszym serwisie, nie ma wygenerowania zlecenia  - status zmieniony na zakończone:'.count($injury_ids);

        return 'done';
    }

    public function getInProgressToCompleted()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        Excel::create('lista spraw do przemianiowania na zakończone', function($excel){
            $excel->sheet('ze statusu w trakcie naprawy', function($sheet) {
                $sheet->appendRow([
                    'Szkody na statusie „w trakcie naprawy”, zarejestrowane przed 2015-01-01',
                ]);
                $sheet->appendRow([
                    'nr sprawy',
                    'nr szkody',
                    'nr umowy'
                ]);

                Injury::where('active', '=', '0')
                    ->where('step', '10')
                    ->where('created_at', '<', \Carbon\Carbon::createFromFormat('Y-m-d', '2015-01-01'))->
                    with('vehicle')->
                    chunk(200, function ($injuries) use ($sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->injury_nr,
                                $injury->vehicle->nr_contract
                            ));
                        }
                    });
            });
        })->download();
    }

    public function getMoveInProgressToCompleted()
    {
        exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $reader = Excel::load(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/injuries/lista_spraw_do_przemianiowania_na_zakonczone.xls', 'windows-1250');
        $objWorksheet = $reader->getActiveSheet();

        $maxCell = $objWorksheet->getHighestRowAndColumn();
        $data = $objWorksheet->rangeToArray('A3:' . $maxCell['column'] . $maxCell['row'],
            NULL,
            TRUE,
            FALSE,
            TRUE);
        $data = array_map('array_filter', $data);
        $rows = array_filter($data);

        $case_nr = [];
        foreach($rows as $row)
        {
            if(!isset($row['D']))
            {
                $case_nr[] = $row['A'];
            }
        }

        $user_id = Auth::user()->id;
        $data = date('Y-m-d H:i:s');
        $injury_ids = Injury::whereIn('case_nr', $case_nr)->lists('id');

        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);

        Injury::whereIn('id', $injury_ids)->update(['step' => '15']);
        CustomLog::info('changing_statuses', 'Szkody na statusie w obsłudze, przed 2015-01-01, bez CFM, bez służbowych - status zmieniony na zakończone', ['ids' => json_encode($injury_ids)]);
        return 'Szkody na statusie w obsłudze, przed 2015-01-01, bez CFM, bez służbowych - status zmieniony na zakończone:'.count($injury_ids);
    }

    public function getInsertDocumentTypes()
    {
        exit();
        $toInsert = [
            '1' ,
            '2',
            '3' ,
            '4' ,
            '5' ,
            '6' ,
            '7' ,
            '8',
            '9' ,
            '10' ,
            '23' ,
            '24',
            '25' ,
            '26' ,
            '27' ,
            '28' ,
            '29' ,
            '30' ,
            '31' ,
            '32' ,
            '33' ,
            '34' ,
            '35' ,
            '36' ,
            '37' ,
            '38' ,

            '45' ,
            '46' ,
            '49' ,
            '50' ,
            '51' ,
            '52' ,
            '53',
            '54' ,

            '57'
        ];

        foreach($toInsert as $item) {
            DB::table('injury_document_type_availability')->insert(
                [
                    'injury_document_type_id'   => $item,
                    'injury_steps_id'           => '13'
                ]
            );
        }
    }

    public function getIdeaExpert()
    {
	    exit();
        $injuries = Injury::vehicleExists('owner_id', 5)->lists('id');

        return $injuries;
    }

    public function getMoveGreco()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $user_id = Auth::user()->id;
        $data = date('Y-m-d H:i:s');
        $rowsToInsert = [];

        $injury_ids = Injury::where('active', '=', '0')
            ->whereIn('step', array('0',  '10', '13') )
            ->vehicleExists('cfm', 0, 'where')
            ->vehicleExistsLikeEnd('nr_contract', '/N', $whereType = 'where', $whereMethod = 'not like')
            ->vehicleExistsLikeEnd('nr_contract', '/T', $whereType = 'where', $whereMethod = 'not like')
            ->vehicleExistsLikeEnd('nr_contract', '/LL', $whereType = 'where', $whereMethod = 'not like')
            ->vehicleExistsLikeEnd('nr_contract', '/LAI', $whereType = 'where', $whereMethod = 'not like')
            ->vehicleExistsLikeEnd('nr_contract', '/NK', $whereType = 'where', $whereMethod = 'not like')
            ->vehicleExistsLikeStart('nr_contract', 'SŁUŻBOWY', $whereType = 'where', $whereMethod = 'not like')
            ->where('created_at', '<', \Carbon\Carbon::createFromFormat('Y-m-d', '2015-01-01'))
            ->where('user_id', 1)->lists('id');


        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        foreach($injury_ids as $injury_id)
        {
            $rowsToInsert[] =
                [
                    'injury_id' => $injury_id,
                    'user_id'	=> $user_id,
                    'history_type_id'	=> 128,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        InjuryHistory::insert($rowsToInsert);

        Injury::whereIn('id', $injury_ids)->update(['step' => '15']);
        CustomLog::info('changing_statuses', ' wszystkie sprawy z przed styczeń 2015 i były zaciągnięte z Greco, znacznikiem będzie w systemie przyjmujący jako „VB xls Administrator DOS” (UWAGA! Proszę o weryfikację w przesłanym pliku są wszystkie szkody również te z roku 2015 i 2016) z pominięciem umów CFM - status zmieniony na zakończone', ['ids' => json_encode($injury_ids)]);
        echo 'wszystkie sprawy z przed styczeń 2015 i były zaciągnięte z Greco, znacznikiem będzie w systemie przyjmujący jako „VB xls Administrator DOS” (UWAGA! Proszę o weryfikację w przesłanym pliku są wszystkie szkody również te z roku 2015 i 2016) z pominięciem umów CFM  - status zmieniony na zakończone:'.count($injury_ids);

        return 'done';
    }

    public function getImportIdeaBank()
    {
	    exit();
        Debugbar::disable();
        echo '<pre>';
        $importer = new \Idea\Vmanage\Imports\ImportIdeaBank('IDEA_BANK.xlsx');
        $importer->load();
        $importer->parseRows();
    }

    public function getCodes($rows = 10)
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        $matcher = new \Idea\VoivodeshipMatcher\GroupMatching();
        return $matcher->matchBranch($rows);
    }

    public function getTest($filename)
    {
        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$filename;

        $parser = new PhpMimeMailParser\Parser();
        $parser->setText(file_get_contents($path));

        $body = $parser->getMessageBody('text');
        $subject = $parser->getHeader('subject');
        echo '<pre>';
        dd( $body , $subject  );
    }


    public function getMoveToFinished()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $reader = Excel::load(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/injuries/lista_spraw_do_przemianiowania_na_zakonczone1.xls');
        $objWorksheet = $reader->getActiveSheet();

        $maxCell = $objWorksheet->getHighestRowAndColumn();
        $data = $objWorksheet->rangeToArray('A2:' . $maxCell['column'] . $maxCell['row'],
            NULL,
            TRUE,
            FALSE,
            TRUE);
        $data = array_map('array_filter', $data);
        $rows = array_filter($data);

        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        $user_id = 10;
        $date = date("Y-m-d H:i:s");
        $ids = [];
        $non = [];

        foreach($rows as $row)
        {
            $injury = Injury::with('vehicle', 'vehicle.owner')->where('injury_nr', 'like', '%'.$row['I'])->vehicleExists('nr_contract', $row['C'], 'where')->where('step', '!=', 15)->where('step', '!=', '-10')->get();

            if($injury->count() > 0) {

                if($injury->count() > 1){
                    dd($row);
                }else{
                    $injury = $injury->first();
                }

                $contract = $injury->vehicle->nr_contract;
                $issuedate = $injury->date_event;
                $issuenumber = $injury->case_nr;
                $issuetype = 'B';
                $username = substr(Auth::user()->login, 0, 10);
                $owner_id = $injury->vehicle->owner_id;

                if ($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1) {
                    $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

                    $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                    $xml = $webservice->getResponseXML();
                }
                if ($injury->vehicle->owner->wsdl == '' || $injury->vehicle->register_as == 0 || $xml->Error->ErrorCde == 'ERR0000' || $xml->Error->ErrorCde == 'ERR0010') {

                    if ($injury->vehicle->owner->wsdl != '' && $injury->vehicle->register_as == 1 && $xml->Error->ErrorCde == 'ERR0010') {
                        $data = new Idea\Structures\REGINSISSUEInput($contract, $issuedate, $issuenumber, $issuetype, $username);

                        $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('reginsissue');

                        $xml = $webservice->getResponseXML();

                        if ($xml->Error->ErrorCde != 'ERR0000') {

                        } else {
                            $data = new Idea\Structures\CLOSEISSUEInput($issuenumber, NULL, $username);

                            $webservice = Webservice::establishSoap($owner_id)->generateParameters($data)->callSoap('closeissue');

                            $xml = $webservice->getResponseXML();
                        }
                    }
                }
                $injury->prev_step = $injury->step;
                $injury->step = '15';
                $injury->date_end = date("Y-m-d H:i:s");
                $injury->touch();
                $injury->save();

                Histories::history($injury->id, 117, $user_id);
                InjuryHistory::create(
                    [
                        'injury_id' => $injury->id,
                        'user_id' => $user_id,
                        'history_type_id' => 128,
                        'created_at' => $date,
                        'value' => $value
                    ]
                );
                $ids[] = $injury->id;
            }else{
                $non[] = $row;
            }
        }

        CustomLog::info('changing_statuses', 'Szkody z pliku lista_spraw_do_przemianiowania_na_zakonczone1.xls status zmieniony na zakończone', ['ids' => json_encode($ids)]);
        dd($non);
        return 'Szkody z pliku lista_spraw_do_przemianiowania_na_zakonczone1.xls status zmieniony na zakończone:'.count($ids);
    }

    public function getFixMoveTotalFinished()
    {
	    exit();
        Debugbar::disable();
        DB::disableQueryLog();
        set_time_limit(500);

        $reader = Excel::load(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/injuries/1-szkody do przeniesienia na status ZAKOŃCZONA.xlsx', 'windows-1250');
        $objWorksheet = $reader->getActiveSheet();

        $maxCell = $objWorksheet->getHighestRowAndColumn();
        $data = $objWorksheet->rangeToArray('A2:' . $maxCell['column'] . $maxCell['row'],
            NULL,
            TRUE,
            FALSE,
            TRUE);
        $data = array_map('array_filter', $data);
        $rows = array_filter($data);
        $injury_nr = [];
        foreach($rows as $row)
        {
            if(isset($row['E']))
                $injury_nr[] = trim($row['E']);
        }

        $injuries = Injury::whereIn('injury_nr', $injury_nr)->get();

        $value = "Przeniesiono na etap zakończone w trakcie grupowej zmiany statusów.";
        $user_id = 10;
        $date = date("Y-m-d H:i:s");

        foreach($injuries as $injury)
        {
            $history = InjuryHistory::where('injury_id', $injury->id)->where('user_id', 10)->where('history_type_id', 117)->find();
            if($history)
            {
                $history->update(['history_type_id' => 114]);
            }else{
                Histories::history($injury->id, 114, $user_id);
                InjuryHistory::create(
                    [
                        'injury_id' => $injury->id,
                        'user_id'	=> $user_id,
                        'history_type_id'	=> 128,
                        'created_at'	=> $date,
                        'value'		=> $value
                    ]
                );
            }
        }

        return 'Done';
    }

    public function getImportGetin($filename)
    {
//        echo '<pre>';
//        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/imports/vmanage/';
//        $file = $path.$filename;
//        if(file_exists($file)) {
//            $rows = array();
//            $header = NULL;
//            if (($handle = fopen($file, 'r')) !== FALSE)
//            {
//                fgetcsv($handle, 0,chr(9));
//                $lp = 0;
//                while(($row = fgetcsv($handle,0,chr(9)))!==FALSE){
//                    $lp ++;
//                    $explodedRow = [
//                        'lp'    =>  $row[0],
//                        'registration'  =>  $row[1],
//                        'vin'   =>  $row[2],
//                        'brand' =>  $row[3],
//                        'model' =>  $row[4],
//                        'pojemnosc_silnika' =>  $row[5],
//                        'moc_silnika'   =>  $row[6],
//                        'jednostka_mocy'    =>  $row[7],
//                        'rok_produkcji' =>  $row[8],
//                        'typ_nadwozia'  =>  $row[9],
//                        'data_konca_polisy' =>  $row[10],
//                        'nazwa_TU'  =>  $row[11],
//                        'wlasciciel_pojazdu'    => $row[12],
//                        'sprzedawca'    =>  $row[13],
//                        'dane_sprzedawcy'   =>  $row[14],
//                        'dealer_forda'  =>  $row[15],
//                        'data_zawarcia_UL'  =>  $row[16],
//                        'NIP_dostawcy'  =>  $row[17]
//                    ];
//
//                    $explodedRow = array_map('trim' , $explodedRow);
//                    foreach ($explodedRow as $k => $item)
//                    {
//                        $explodedRow[$k] = iconv('WINDOWS-1250','utf-8', $item);
//                    }
//
//                    dd($explodedRow);
//                }
//                fclose($handle);
//
//            }
//        }
//
//        exit();

        $import = VmanageImport::create(
            [
                'user_id'   =>  Auth::user()->id,
                'filename'  =>  $filename
            ]);

        Queue::push('Idea\Vmanage\Imports\QueueImportGetin', array('filename' => $filename, 'import_id' => $import->id));
        echo 'working';

	    exit();
        //$filename = 'POJAZDY_UB_MANTIS_8968-'.$part.'.csv';
        $filename = 'POJAZDY_UB_MANTIS_8968_23_11-'.$part.'.tsv';
        $importer = new \Idea\Vmanage\Imports\ImportGetin($filename);
        $importer->loadTsv();
        $importer->parse();

        return 'done';
    }

    public function getChangePolicyOwner()
    {
	    exit();
        $leasingAgreements = LeasingAgreement::where('owner_id', 2)->where('created_at', '>', '2016-01-01 00:00:00')->lists('id');

        $user_id = Auth::user()->id;
        $data = date('Y-m-d H:i:s');
        $value = 'Zmiana finansującego na IL1 Leasing Spółka z ograniczoną odpowiedzialnością na wniosek Wojciecha Kamińskiego.';
        foreach($leasingAgreements as $leasingAgreement_id)
        {
            $rowsToInsert[] =
                [
                    'leasing_agreement_id' => $leasingAgreement_id,
                    'user_id'	=> $user_id,
                    'leasing_agreement_history_type_id'	=> 2,
                    'created_at'	=> $data,
                    'value'		=> $value
                ];
        }
        LeasingAgreementHistory::insert($rowsToInsert);

        LeasingAgreement::whereIn('id', $leasingAgreements)->update(['owner_id' => '4']);
        CustomLog::info('changing_owners_policies', 'we wszystkich umowach wprowadzonych do systemu do początku bieżącego roku z IL2 Leasing Spółka z ograniczoną odpowiedzialnością na IL1 Leasing Spółka z ograniczoną odpowiedzialnością', ['ids' => json_encode($leasingAgreements)]);

        return 'done';
    }

    public function getExportArchive()
    {
	    exit();
        set_time_limit(500);
        DB::disableQueryLog();
        Debugbar::disable();

        $fileName = 'archive.csv';

        $count = LeasingAgreement::
            distinct()
            ->whereNull('withdraw')
            ->whereNotNull('archive')
            ->count();

        $processAtOnce = 100;
        $rounds = round($count / $processAtOnce);

        header("Content-disposition: attachment; filename={$fileName}");
        header("Content-Type: text/csv");

        $headerSet = false;
        for ($i = 0; $i < $rounds; ++$i) {

            $limit = $processAtOnce;
            $offset = $i * $processAtOnce;
            $rows = LeasingAgreement::
                distinct()
                ->whereNull('withdraw')
                ->whereNotNull('archive')
                ->with(array('client', 'user', 'insurances' => function($query)
                {
                    $query->active();
                }), 'insurances.insuranceCompany')
                ->orderBy('id', 'asc')->skip($offset)->take($limit)->get();

            if (empty($rows)) {
                continue;
            }
            $outStream = fopen('php://output', 'w');

            if (!$headerSet) {
                fputcsv($outStream, [
                    'nr umowy',
                    'nr zgłoszenia',
                    'leasingobiorca',
                    'Ubezpieczyciel',
                    'wprowadzający',
                    'data zgłoszenia'
                ], ',', '"');
                $headerSet = true;
            }

            foreach ($rows as $row) {
                fputcsv($outStream, [
                    $row->nr_contract,
                    $row->nr_agreement,
                    $row->client->name,
                    ($row->insurances->last() && $row->insurances->last()->insuranceCompany) ? $row->insurances->last()->insuranceCompany->name:'---',
                    $row->user->name,
                    substr($row->created_at, 0, -3)
                ], ',', '"');
            }

            echo fgets($outStream);

            fclose($outStream);
        }
    }

    public function getIlVehicles()
    {
	    exit();
        Excel::create('lista spraw', function($excel) {
            $excel->sheet('lista spraw', function($sheet) {

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::whereBetween('created_at', ['2016-10-03 06:00:00', '2016-10-04 15:00:00'])
                    ->whereHas('vehicleFromVehicle', function($query) {
                        $query->where('owner_id', 1)->where('register_as', 0);
                    })
                    ->with('vehicle')
                    ->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->case_nr,
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->VIN
                        ));
                    }
                });

            });

        })->download();
    }

    public function getMobileInjuries($id)
    {
        $injury = MobileInjury::find($id);

        $group_name = '';
        if( ($injury->source == 0 || $injury->source == 3)  && $injury->injuries_type()->first()) {
            $group_name = $injury->injuries_type()->first()->name;
        }else {
            if ($injury->injuries_type == 2)
                $group_name = 'komunikacyjna OC';
            elseif($injury->injuries_type == 1)
                $group_name = 'komunikacyjna AC';
            elseif($injury->injuries_type == 3)
                $group_name = 'komunikacyjna kradzież';
            elseif($injury->injuries_type == 4)
                $group_name = 'majątkowa';
            elseif($injury->injuries_type == 5)
                $group_name = 'majątkowa kradzież';
            elseif($injury->injuries_type == 6)
                $group_name = 'komunikacyjna AC - Regres';
        }

        if (strpos($group_name, 'kradzież') !== false) {
            $task_group_id = 3;
        }else{
            $task_group_id = 1;
        }

        $task = Task::create([
            'task_source_id' => 2, //druk online
            'from_email' => $injury->notifier_email,
            'from_name' => $injury->notifier_name.' '.$injury->notifier_surname,
            'subject' => $injury->nr_contract.' # '.$injury->registration,
            'content' => $injury->description(),
            'task_group_id' => $task_group_id,
            'task_date' => $injury->created_at
        ]);

        $injury->tasks()->save($task);

        if($injury->source == 1)
            $template = 'mobile.info_template_web';
        else
            $template = 'mobile.info_template_phone';
        $html = View::make($template, compact('injury'));
        $name= str_random(32).'.pdf';

        PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false)->save(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER')."/emails/".$name);

        $task->files()->create([
            'filename' => $name,
            'original_filename' => 'zgłoszenie.pdf',
            'mime' => 'application/pdf'
        ]);


        \Idea\Tasker\Tasker::assign($task);
	    exit();
        Excel::create('z aplikacji mobilnej', function($excel) {
            $excel->sheet('z aplikacji mobilnej', function($sheet) {

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::whereBetween('created_at', ['2016-01-01 06:00:00', '2016-11-24 15:00:00'])
                    ->whereHas('mobileInjury', function($query){
                        $query->where('source', 0);
                    })
                    ->with('vehicle')
                    ->chunk(100, function($injuries) use (&$sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->registration,
                                $injury->vehicle->VIN
                            ));
                        }
                    });

            });

        })->download();
    }

    public function getQueue($s)
    {
        Queue::push('Idea\Debug\Queue', $s);
    }

    public function getMail()
    {
        $context = 'test';
        $body = View::make('emails.errors.error_simple', compact('context'))->render();
        $mailer = new \Idea\Mail\Mailer();
        $mailer->addAddress('przemek@webwizards.pl');
        $mailer->setSubject('[IdeaLeasing] Error notification.');
        $mailer->setBody($body);
        $mailer->setTimeout(15);
        $mailer->debug(3);
        dd($mailer->send());

        dd('done');
    }

    public function getChangeAuthorizeCfm(){
        exit();
      $injuries= Injury::where(function($query){
          $query->vehicleExists('cfm', 1, 'where');
      })->whereHas('documents',function($query){
        $query->where('category',53);
      },0)->update(['task_authorization'=>0]);

      $injuries= Injury::where(function($query){
          $query->vehicleExists('cfm', 1, 'where');
      })->whereHas('documents',function($query){
        $query->where('category',53);
      })->update(['task_authorization'=>1]);
    }

    public function getUpdateInjuryFiles()
    {
	    exit();
        $document_type_id = 241;
        $stage = InjuryStepStage::where('injury_step_id', 0)->whereHas( 'documentTypes', function($query) use($document_type_id){
            $query->where( 'injury_document_type_id', $document_type_id);
        })->first();
        dd($stage);
    }

    public function getSetDocDate()
    {
	    exit();
        $injuries = InjuryFiles::where('category', 69)->where('active', 0)->where('created_at', '>', '2017-02-27 00:00:00')->lists('injury_id', 'id');
        dd($injuries);
    }

    public function getNewStatuses()
    {
	    exit();
        /*
        Excel::create('aktualizacje etapow', function($excel) {
            $excel->sheet('na zakonczona - wyst up', function($sheet) {
                //w obsłudze z tego statusu przenieść na ZAKONCZONA - WYSTAWIONO UPOWAŻNIENIE jeśli wygenerowano załączniiki (zal nr 2, zał nr 8) a nie podpięto DECYZJI TU lub ODMOWY TU
                $sheet->appendRow(['w obsłudze na ZAKONCZONA - WYSTAWIONO UPOWAŻNIENIE']);

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::where('created_at', '>', '2015-01-01 00:00:00')
                    ->where('step', 10)
                    ->whereHas('documents', function($query){
                        $query->where('type', 3)->whereIn('category', [2,3,7,26,32,68,72] )->where('active', 0);
                    })->whereDoesntHave('documents', function($query){
                        $query->where('type', 2)->whereIn('category', [6,7] )->where('active', 0);
                    })
                    ->with('vehicle')
                    ->chunk(100, function($injuries) use (&$sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->registration,
                                $injury->vehicle->VIN
                            ));
                        }
                    });
            });

            $excel->sheet('na w obsludze edb', function($sheet) {
                $sheet->appendRow(['w obsłudze na w obsłudze EDB']);

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 10)->whereHas( 'branch', function($query){
                    $query->whereHas('company', function ($query){
                        $query->whereHas('groups', function($query){
                            $query->where('company_group_id', 5);
                        });
                    });
                })->where(function($query){
                    $query->vehicleExists('cfm', 1);
                })
                ->with('vehicle')
                ->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->case_nr,
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->VIN
                        ));
                    }
                });

                Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 10)->whereHas( 'branch', function($query){
                    $query->whereHas('company', function ($query){
                        $query->whereHas('groups', function($query){
                            $query->where('company_group_id', 1);
                        });
                    });
                })
                ->with('vehicle')
                ->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->case_nr,
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->VIN
                        ));
                    }
                });
            });

            $excel->sheet('na do rozliczenia EDB', function($sheet) {
                $sheet->appendRow(['do rozliczenia na do rozliczenia EDB']);

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 13)->whereHas( 'branch', function($query){
                    $query->whereHas('company', function ($query){
                        $query->whereHas('groups', function($query){
                            $query->where('company_group_id', 5);
                        });
                    });
                })->where(function($query){
                    $query->vehicleExists('cfm', 1);
                })
                ->with('vehicle')
                ->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->case_nr,
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->VIN
                        ));
                    }
                });

                Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 13)->whereHas( 'branch', function($query){
                    $query->whereHas('company', function ($query){
                        $query->whereHas('groups', function($query){
                            $query->where('company_group_id', 1);
                        });
                    });
                })
                ->with('vehicle')
                ->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->case_nr,
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->VIN
                        ));
                    }
                });
            });

            $excel->sheet('na rozliczona EDB', function($sheet) {
                $sheet->appendRow(['rozliczona na rozliczona EDB']);

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 16)->whereHas( 'branch', function($query){
                    $query->whereHas('company', function ($query){
                        $query->whereHas('groups', function($query){
                            $query->where('company_group_id', 5);
                        });
                    });
                })->where(function($query){
                    $query->vehicleExists('cfm', 1);
                })->with('vehicle')
                ->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->case_nr,
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->VIN
                        ));
                    }
                });
                Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 16)->whereHas( 'branch', function($query){
                    $query->whereHas('company', function ($query){
                        $query->whereHas('groups', function($query){
                            $query->where('company_group_id', 1);
                        });
                    });
                })->with('vehicle')
                ->chunk(100, function($injuries) use (&$sheet) {
                    foreach ($injuries as $injury) {
                        $sheet->appendRow(array(
                            $injury->case_nr,
                            $injury->vehicle->nr_contract,
                            $injury->vehicle->registration,
                            $injury->vehicle->VIN
                        ));
                    }
                });
            });

            $excel->sheet('na zakończona - wyst upow', function($sheet) {
                $sheet->appendRow(['z zakończone bez likwidacji na zakończona - wystawiono upoważnienie']);

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::where('created_at', '>', '2015-01-01 00:00:00')
                    ->where('step', 17)
                    ->where('task_authorization', 1)
                    ->whereDoesntHave('documents', function($query){
                        $query->where('type', 2)->whereIn('category', [6,7] )->where('active', 0);
                    })
                    ->chunk(100, function($injuries) use (&$sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->registration,
                                $injury->vehicle->VIN
                            ));
                        }
                    });
            });

            $excel->sheet('na zakończona odmową tu', function($sheet) {
                $sheet->appendRow(['z odmowa ZU na zakończona odmową tu']);

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin'
                ]);

                Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('created_at', '<', '2016-01-01 00:00:00')->where('step', 20 )
                    ->chunk(100, function($injuries) use (&$sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->registration,
                                $injury->vehicle->VIN
                            ));
                        }
                    });
            });

        })->download();

        exit();
        */
        exit();
        //w obsłudze z tego statusu przenieść na ZAKONCZONA - WYSTAWIONO UPOWAŻNIENIE jeśli wygenerowano załączniiki (zal nr 2, zał nr 8) a nie podpięto DECYZJI TU lub ODMOWY TU
        Injury::where('created_at', '>', '2015-01-01 00:00:00')
                    ->where('step', 10)
                    ->whereHas('documents', function($query){
                        $query->where('type', 3)->whereIn('category', [2,3,7,26,32,68,72] )->where('active', 0);
                    })->whereDoesntHave('documents', function($query){
                        $query->where('type', 2)->whereIn('category', [6,7] )->where('active', 0);
                    })->update(['step' => '23']);


        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 10)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 5);
                });
            });
        })->where(function($query){
            $query->vehicleExists('cfm', 1);
        })->update(['step' => 11]);

        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 10)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 1);
                });
            });
        })->update(['step' => 11]);



        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 13)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 5);
                });
            });
        })->where(function($query){
            $query->vehicleExists('cfm', 1);
        })->update(['step' => 14]);

        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 13)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 1);
                });
            });
        })->update(['step' => 14]);


        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 16)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 5);
                });
            });
        })->where(function($query){
            $query->vehicleExists('cfm', 1);
        })->update(['step' => 21]);
        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 16)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 1);
                });
            });
        })->update(['step' => 21]);


        Injury::where('created_at', '>', '2015-01-01 00:00:00')
            ->where('step', 17)
            ->where('task_authorization', 1)
            ->whereDoesntHave('documents', function($query){
                $query->where('type', 2)->whereIn('category', [6,7] )->where('active', 0);
            })->update(['step' => '23']);


        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 20)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 5);
                });
            });
        })->where(function($query){
            $query->vehicleExists('cfm', 1);
        })->update(['step' => 22]);

        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('step', 20)->whereHas( 'branch', function($query){
            $query->whereHas('company', function ($query){
                $query->whereHas('groups', function($query){
                    $query->where('company_group_id', 1);
                });
            });
        })->update(['step' => 22]);

        //odmowa zakładu ubezpieczeń	z tego statusu przenieść na ststus ZAKOŃCZONA ODMOWĄ TU
        Injury::where('created_at', '>', '2015-01-01 00:00:00')->where('created_at', '<', '2016-01-01 00:00:00')->where('step', 20 )->update(['step' => '24']);
    }

    public function getChangeTotalStatus(){
	    exit();
        $injuries = Injury::where('step', '-5')->update(['step' => 31]);
        $injuries = Injury::where('step', '-3')->update(['step' => 41]);
    }

    public function getFixEdb()
    {
	    exit();
        $injuries = Injury::where('step', '11')->whereHas('documents', function($query){
            $query->whereType(3)->whereIn('category', [
                6, 49, 52, 60
            ]);
        }, '<', 1)->lists('id');

        Log::info('fix edb ids', $injuries);
        Injury::whereIn('id', $injuries)->update(['step' => 10]);
        dd($injuries);
    }

    public function getFixOld()
    {
	    exit();
        $injuries = Injury::where('created_at', '<', '2015-01-01 00:00:00')->where('updated_at', '>', '2017-03-20 00:00:00')->where('step', 15)->lists('id');
        Log::info('before 2015 ids to check', $injuries);

        $injuries = Injury::where('created_at', '<', '2015-01-01 00:00:00')->where('updated_at', '<=', '2017-03-20 00:00:00')->where('step', 15)->lists('id');
        Log::info('before 2015 ids fixed', $injuries);
        Injury::whereIn('id', $injuries)->update(['step' => 25]);
    }

    public function getReportOdmowa()
    {
	    exit();
        Excel::create('zestawienie', function($excel) {
            $excel->sheet('zestawienie', function($sheet) {

                $sheet->appendRow([
                    'nr sprawy',
                    'nr umowy',
                    'rejestracja',
                    'vin',
                    'status',
                    'etap'
                ]);

                Injury::whereHas('documents', function($query){
                    $query->where('document_type', 'InjuryUploadedDocumentType')->whereIn('document_id', [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36]);
                })->with('stepStage', 'status', 'vehicle')
                    ->chunk(100, function($injuries) use (&$sheet) {
                        foreach ($injuries as $injury) {
                            $sheet->appendRow(array(
                                $injury->case_nr,
                                $injury->vehicle->nr_contract,
                                $injury->vehicle->registration,
                                $injury->vehicle->VIN,
                                $injury->status->name,
                                ($injury->stepStage) ? $injury->stepStage->name : ''
                            ));
                        }
                    });

            });

        })->download();
    }

    public function getUpdateOwners()
    {
//    	exit();
//    	Owners::whereIn('id', [1,2,4,13])->update([
//    		'name' => 'Idea Getin Leasing S.A.',
//		    'short_name' => 'IGL S.A.'
//	    ]);
	    Settings::set('idea_getin_activated','enabled');

	    header('Content-Type: text/html; charset=utf-8');
    	echo 'Dane spółek i papier firmowy zostały zaktualizowane';
    }

    public function getUpdateInjuryDocs()
    {
	    $documentsTypes = InjuryDocumentType::whereActive(0)->with('ownersGroups')->get();
	    foreach($documentsTypes as $documentsType)
	    {
		    if($documentsType->ownersGroups->contains(1)){
			    if(! $documentsType->ownersGroups->contains(3)){
				    $documentsType->ownersGroups()->attach(3);
			    }
			    if(! $documentsType->ownersGroups->contains(6)){
				    $documentsType->ownersGroups()->attach(6);
			    }
		    }elseif($documentsType->ownersGroups->contains(3)){
			    if(! $documentsType->ownersGroups->contains(1)){
				    $documentsType->ownersGroups()->attach(1);
			    }
			    if(! $documentsType->ownersGroups->contains(6)){
				    $documentsType->ownersGroups()->attach(6);
			    }
		    }elseif($documentsType->ownersGroups->contains(6)){
			    if(! $documentsType->ownersGroups->contains(3)){
				    $documentsType->ownersGroups()->attach(3);
			    }
			    if(! $documentsType->ownersGroups->contains(1)){
				    $documentsType->ownersGroups()->attach(1);
			    }
		    }
	    }
    }

    public function getReport()
    {
    	echo 'start';
    	$nr_szkody = [];
	    \Injury::where('active', 0)->where(function($query){
			    $query->where('created_at', '>', "2017-01-01 00:00:00");
			    $query->where('created_at', '<', "2018-02-01 00:00:00");
	    })->with('client','injuries_type', 'getRemarks', 'getInfo', 'damages', 'damages.damage', 'branch', 'branch.company', 'branch.company.groups', 'branch.voivodeship', 'invoices', 'status', 'totalStatus', 'theftStatus', 'chat', 'chat.messages', 'user', 'leader', 'theft', 'theft.acceptations', 'totalRepair', 'wreck', 'totalStatusesHistory', 'theftStatusesHistory', 'documents', 'compensations', 'receive', 'driver')
		    ->chunk(1000, function($injuries) use(&$nr_szkody)  {
		    	foreach($injuries as $injury)
			    {
			    	if(in_array($injury->injury_nr, ['PL2017051003345', '1570691/1', 'PL2018010901948']))
					    $nr_szkody [] = $injury->injury_nr;
			    }
		    });
	    echo 'koniec';
	    dd($nr_szkody);
	    return Response::json($nr_szkody);
    }

    public function getOwner()
    {
    	exit();
    	$ownerGroup = OwnersGroup::find(7);
    	$owner = OwnersGroup::find(8);
    	foreach($ownerGroup->injuryDocumentTypes as $documentType)
	    {
		    $owner->injuryDocumentTypes()->attach($documentType->id);
	    }
    }

    public function getVmanageInjuries()
    {
    	$injuries  = Injury::where('step', '!=', '-10')->where('vehicle_type', 'VmanageVehicle')->whereHas('vehicleFromVmanageVehicle', function($query){
    		$query->where('owner_id', 16);
	    })->where('active', 0)->take(20)->lists('id');
    	dd($injuries);
    }

    public function getImportCommission()
    {
	    InjuryInvoices::where('commission', 1)->chunk(100, function($invoices){
		    foreach ($invoices as $invoice)
		    {
			    $commission = new Commission;
			    $commission->injury_invoice_id = $invoice->id;
			    $commission->commission_step_id = 1;
			    $commission->invoice_date = $invoice->invoice_date;
			    $commission->created_at = $invoice->created_at;
			    $commission->save();
		    }
	    });
    }

	public function getCas()
	{
	    exit();
		$settings = json_decode(file_get_contents(base_path('app/config/constants.json')), true);
		$settings['cas'] =  '2018-05-01 01:01:01';
		file_put_contents(base_path('app/config/constants.json'), json_encode($settings));

		$step = InjurySteps::find(11);
		$step->update(['name' => 'w obsłudze asysta']);

		$step = InjurySteps::find(14);
		$step->update(['name' => 'do rozliczenia asysta']);

		$step = InjurySteps::find(21);
		$step->update(['name' => 'rozliczona asysta']);

		$step = InjurySteps::find(22);
		$step->update(['name' => 'odmowa ZU asysta']);

		header('Content-Type: text/html; charset=utf-8');
		echo 'CAS aktywowane';
	}

	public function getSetTotalStage()
    {
        Injury::whereIn('step', [30,31,32,33,34,35,36,37,40,41,42,43,44,45,46,'-7'])->whereHas('documents', function($query){
            $query->where('document_type', 'InjuryUploadedDocumentType')
                ->whereIn('category', [28,29,30,31,32,33,34,35,36]);
        })->with(['documents' => function($query){
            $query->where('document_type', 'InjuryUploadedDocumentType')
                ->whereIn('category', [28,29,30,31,32,33,34,35,36]);
        }])->get()->each(function($injury){
            $document_type_id = $injury->documents->last()->category;

            $stage = InjuryStepStage::whereHas('uploadedDocumentTypes', function ($query) use ($document_type_id) {
                $query->where('injury_uploaded_document_type_id', $document_type_id);
            })->orderBy('id')->first();

            if($stage)
            {
                $injury->update([
                    'injury_total_step_stage_id' => $stage->id
                ]);
            }
        });

        header('Content-Type: text/html; charset=utf-8');
        echo 'done';
    }

    public function getMoveForecasts()
    {
        $injuries = Injury::where('gap_forecast', '>', 0)->with('injuryGap')->get();

        foreach($injuries as $injury){
            if(! $injury->injuryGap){
                InjuryGap::create([
                    'injury_id' => $injury->id,
                    'forecast' => $injury->gap_forecast
                ]);
            }
        }

        echo $injuries->count();
    }

    public function getPagination()
    {
        $users = User::paginate();

        echo '<pre>';
        dd($users->toArray());
    }
    
    public function getError()
    {
        dd('test');
    }
}
