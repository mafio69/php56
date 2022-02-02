<?php

class InsurancesInfoDialogController extends \BaseController {


    public function __construct()
    {
        $this->beforeFilter('permitted:kartoteka_polisy#zarzadzaj');
    }

    /*
     * Edycja danych przedmiotu ubezpieczenia
     */

    public function getEditObject($object_id)
    {
        $object = LeasingAgreementObject::with('leasing_agreement')->findOrFail($object_id);

        if($object->leasing_agreement->has_yacht == 1)
        {
            $categories = ObjectAssetType::where('if_yacht', 1)->lists('name', 'id');

            return  View::make('insurances.manage.card_file-yacht.dialog.edit_object', compact('object', 'categories'));
        }
        $categories = ObjectAssetType::where('if_yacht', 0)->lists('name', 'id');

        return View::make('insurances.manage.card_file.dialog.edit_object', compact('object', 'categories'));
    }

    public function postUpdateObject($object_id)
    {
        $object = LeasingAgreementObject::find($object_id);
        $previousObject = $object->toArray();;

        $inputs = Input::all();
        $inputs['object_id'] = $object_id;
        $object->update($inputs);

        $agreement = $object->leasing_agreement()->first();
        $previousAgreement = $agreement->toArray();

        $agreement->updateLoans();

        $historyType = 4;
        $history_id = Histories::leasingAgreementHistory($object->leasing_agreement_id, $historyType, Auth::user()->id, $object->name);

        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'object' => [
                    'previous' => $previousObject,
                    'current' => $object->toArray()
                ],
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $agreement->toArray()
                ]
            ], $history_id, $agreement->id);

        Flash::success("Dane przedmiotu umowy: <i>".$object->name."</i> zostały zaktualizowane");

        $result['code'] = 0;
        return json_encode($result);
    }

    /*
     * usuwanie przedmiotu ubezpieczenia
     */
    public function getDeleteObject($object_id)
    {
        $object = LeasingAgreementObject::find($object_id);

        return View::make('insurances.manage.card_file.dialog.delete_object', compact('object'));
    }

    public function postRemoveObject($object_id)
    {
        $object = LeasingAgreementObject::find($object_id);
        $agreement = $object->leasing_agreement()->first();
        $previousAgreement = $agreement->toArray();

        $object->delete();
        $agreement->updateLoans();

        $historyType = 17;
        $history_id = Histories::leasingAgreementHistory($agreement->id, $historyType, Auth::id(), $object->name);

        new \Idea\Logging\LeasingAgreements\Logger($historyType,
            [
                'agreement' => [
                    'previous' => $previousAgreement,
                    'current' => $agreement->toArray()
                ]
            ], $history_id, $agreement->id);

        Flash::success("Przedmiot umowy: <i>".$object->name."</i> został usunięty z umowy");

        $result['code'] = 0;
        return json_encode($result);
    }

    /*
     * Dodawanie przedmiotu umowy
     */

    public function getCreateObject($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);

        if($agreement->has_yacht == 1)
        {
            $categories = ObjectAssetType::where('if_yacht', 1)->lists('name', 'id');

            return  View::make('insurances.manage.card_file-yacht.dialog.create_object', compact('agreement_id', 'categories'));
        }

        $categories = ObjectAssetType::where('if_yacht', 0)->lists('name', 'id');

        return View::make('insurances.manage.card_file.dialog.create_object', compact('agreement_id', 'categories'));
    }

    public function postStoreObject()
    {
        $inputs = Input::all();
        $inputs['user_id'] = Auth::user()->id;
        $object = LeasingAgreementObject::create($inputs);

        $agreement = $object->leasing_agreement()->first();
        $agreement->updateLoans();

        Histories::leasingAgreementHistory(Input::get('leasing_agreement_id'), 3, Auth::user()->id, $object->name);

        Flash::success("Przedmiot umowy: <i>".$object->name."</i> zostały dodany");
        $result['code'] = 0;
        return json_encode($result);
    }

    /*
     * Dodawanie klienta
     */

    public function getCreateClient()
    {
        return View::make('insurances.manage.card_file.dialog.create_client');
    }

    public function postCheckClientNIP(){
        \Debugbar::disable();

        $nip = trim(str_replace('-', '', Input::get('NIP')));
        $client = Clients::where('NIP', '=', $nip)->get();
        if($client->isEmpty())
            return '0';

        return '1';
    }

    public function postStoreClient(){
        $input = Input::all();
        $input['NIP'] = trim(str_replace('-', '', $input['NIP']));

        $matcher = new \Idea\VoivodeshipMatcher\SingleMatching();
        if(Input::has('registry_post') && strlen(Input::has('registry_post')) == 6)
        {
            $registry_post = $input['registry_post'];
            $voivodeship_id = $matcher->match($registry_post);
            $input['registry_voivodeship_id'] = $voivodeship_id;
        }
        if(Input::has('correspond_post') && strlen(Input::has('correspond_post')) == 6)
        {
            $correspond_post = $input['correspond_post'];
            $voivodeship_id = $matcher->match($correspond_post);
            $input['correspond_voivodeship_id'] = $voivodeship_id;
        }

        $client = Clients::create($input);

        if($client){
            $result['status'] = 'success';
            $result['client'] = $client->toArray();
        }else{
            $result['status'] = 'error';
            $result['msg'] = 'Wystąpił błąd w trakcie dodawania klienta. Skontaktuj się z administratorem.';
        }
        return json_encode($result);
    }

    /*
     * Dodawanie notki do historii
     */
    public function getHistory($agreement)
    {
        return View::make('insurances.manage.card_file.dialog.add_history', compact('agreement'));
    }

    public function postStoreHistory()
    {
        Histories::leasingAgreementHistory(Input::get('leasing_agreement_id'), 2, Auth::user()->id, '<b>'.Input::get('content').'</b>');

        Flash::success("Notatka została dodana");
        $result['code'] = 0;
        return json_encode($result);
    }

    /*
     * Edycja danych umowy
     */

    public function getEditAgreement($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);

        $leasingAgreementTypes = LeasingAgreementType::lists('name', 'id');
        $leasingAgreementPaymentWays = LeasingAgreementPaymentWay::lists('name', 'id');

        if($agreement->has_yacht == 1)
        {
            return View::make('insurances.manage.card_file-yacht.dialog.edit_agreement', compact('agreement', 'leasingAgreementTypes')) ;
        }

        if($agreement->insurances->isEmpty()){
            $lastInsurance = $groups = null;
        }else {
            $lastInsurance = $agreement->insurances()->active()->first();
            $groups = $this->matchInsuranceGroup($lastInsurance);
        }

        //$owners = Owners::orderBy('name')->lists('name', 'id');
        $ownersDb = Owners::where('active', 0)->orderBy('name')->get();
        $owners = [];
        foreach($ownersDb as $owner)
        {
            $owners[$owner->id] = ($owner->old_name) ? $owner->name.' ('.$owner->old_name.')' : $owner->name;
        }
        $owners[0] = '---wybierz właściciela---';
        ksort($owners);

        return View::make('insurances.manage.card_file.dialog.edit_agreement', compact('agreement', 'leasingAgreementTypes', 'leasingAgreementPaymentWays', 'groups', 'owners'));
    }

    public function postUpdateAgreement($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);
        $previousAgreement = $agreement->toArray();

        $findSameContract = LeasingAgreement::where('nr_contract', Input::get('nr_contract'))->where('id', '!=', $agreement_id)->get();
        if($findSameContract->isEmpty()) {
            $inputs = Input::all();

            if($agreement->has_yacht == 0) {
                if ($inputs['date_acceptation'] == '')
                    unset($inputs['date_acceptation']);
                if ($inputs['insurance_from'] == '')
                    unset($inputs['insurance_from']);
                if ($inputs['insurance_to'] == '')
                    unset($inputs['insurance_to']);
            }

            $agreement->update($inputs);

            $historyType = 11;
            $history_id = Histories::leasingAgreementHistory($agreement_id, $historyType);

            new \Idea\Logging\LeasingAgreements\Logger($historyType,
                [
                    'agreement' => [
                        'previous' => $previousAgreement,
                        'current' => $agreement->toArray()
                    ]
                ], $history_id, $agreement->id);

            Flash::success("Dane umowy zostały zaktualizowane.");

            $result['code'] = 0;
            return json_encode($result);
        }

        $result['code'] = 2;
        $result['error'] = 'Istnieje już w systemie o podanym numerze.';
        return json_encode($result);
    }

    public function postChangeInsurancesGroup()
    {
        $currentRate = LeasingAgreementInsuranceGroupRow::find(Input::get('current_rate_id'));

        $group = LeasingAgreementInsuranceGroup::find(Input::get('group_id'));

        $rates = $group->rows()->get()->toArray();
        $current_id = $group->rows()->where('leasing_agreement_insurance_group_rate_id', $currentRate->leasing_agreement_insurance_group_rate_id)->first();
        if($current_id)
            $current_id = $current_id->id;
        else
            $current_id = '-1';

        return json_encode(['current_id' => $current_id, 'rates' => $rates]);
    }

    /*
     * Dopasuj grupę stawek
     */
    private function matchInsuranceGroup($lastInsurance)
    {
        $date_from = $lastInsurance->date_from;
        $date_to = $lastInsurance->date_to;
        $insurance_company_id = $lastInsurance->insurance_company_id;

        $matchedGroup = LeasingAgreementInsuranceGroup::where('insurance_company_id',$insurance_company_id)->where('valid_from', '<=', $date_from)->where('valid_to', '>=', $date_to)->get()->last();
        if($matchedGroup)
        {
            $returnGroup['status'] = 'success';
            $returnGroup['group'] = $matchedGroup->id;
            $returnGroup['rates'] = $matchedGroup->rows()->get()->lists('rate_name', 'id');
        }else{
            $matchedGroup = LeasingAgreementInsuranceGroup::where('insurance_company_id',$insurance_company_id)->whereNull('valid_to')->whereNotNull('valid_from')->first();

            $returnGroup['status'] = 'error';
            $returnGroup['msg'] = 'nie zdefiniowano grupy stawek obowiązującej w zakrasie obowiązywania aktualnej polisy';
            if($matchedGroup) {
                $returnGroup['group'] = $matchedGroup->id;
                $returnGroup['rates'] = $matchedGroup->rows()->get()->lists('rate_name', 'id');
            }else {
                $returnGroup['group'] = 0;
                $returnGroup['rates'] = ['---brak zdefiniowanych stawek---'];
            }
        }
        if(!LeasingAgreementInsuranceGroup::where('insurance_company_id',$insurance_company_id)->whereNotNull('valid_from')->orderBy('id', 'desc')->get()->isEmpty())
            $returnGroup['groups'] = LeasingAgreementInsuranceGroup::where('insurance_company_id',$insurance_company_id)->whereNotNull('valid_from')->orderBy('id', 'desc')->get()->lists('group_name', 'id');
        else
            $returnGroup['groups'] = ['---brak zdefiniowanych stawek---'];

        return $returnGroup;
    }

    public function getRollbackCession($agreement_id)
    {
        return View::make('insurances.manage.card_file.dialog.rollback_cession', compact('agreement_id'));
    }

    public function getRollbackInsurance($insurance_id)
    {
        return View::make('insurances.manage.card_file.dialog.rollback_insurance', compact('insurance_id'));
    }

    public function getRollbackInsuranceYacht($insurance_id)
    {
        return View::make('insurances.manage.card_file-yacht.dialog.rollback_insurance-yacht', compact('insurance_id'));
    }



    public function postUploadDocument($agreement_id)
    {
        $randomKey  = sha1( time() . microtime() );

        $extension  = Input::file('file')->getClientOriginalExtension();

        $filename   = $randomKey.'.'.$extension;

        $path       = '/files';

        // Move the file and determine if it was succesful or not
        $upload_success = Input::file('file')->move( Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . $path , $filename );

        if( $upload_success ) {

            $file = LeasingAgreementFile::create(array(
                'leasing_agreement_id' => $agreement_id,
                'type'		=> 2,
                'category'	=> 0,
                'user_id'	=> Auth::user()->id,
                'file'		=> $filename,
            ));

            return Response::json(array('status' => 'success', 'file' => $filename, 'id' => $file->id));
        } else {
            return Response::json(array('status' => 'error'));
        }
    }

    public function postAssignUploadedDocument()
    {
        $input = Input::get('files');
        return View::make('insurances.manage.card_file-yacht.dialog.document', compact('input'));
    }

    public function postSetUploadDocument()
    {
        $input = Input::get('files');
        foreach ($input as $k => $v) {
            $file = LeasingAgreementFile::find($v);
            $file->category = Input::get('fileType');
            $file->name = Input::get('content');
            $file->save();
            Histories::leasingAgreementHistory($file->leasing_agreement_id, 21, Auth::user()->id, 'Kategoria '.Config::get('definition.insurancesFileCategory.'.Input::get('fileType')).' - <a target="_blank" href="'.URL::to(url('insurances/manage-actions/downloadDocument', [$v])).'">pobierz</a>');
        }

        return $file->category;
    }

    public function getDeleteDocument($file_id)
    {
        return View::make('insurances.manage.card_file-yacht.dialog.docDelete', compact('file_id'));
    }

    public function postDeleteDocument($file_id)
    {
        $file = LeasingAgreementFile::find($file_id);
        $file->delete();


        Histories::leasingAgreementHistory($file->leasing_agreement_id, 22, Auth::user()->id, '<a target="_blank" href="'.URL::to(url('insurances/manage-actions/downloadDocument', [$file_id])).'">pobierz</a>');
        Flash::success("Dokument został usunięty.");

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getAttachToResume($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::findOrFail($insurance_id);

        $insurance_to_attach = LeasingAgreementInsurance::where('leasing_agreement_id', $insurance->leasing_agreement_id)
            ->where('created_at', '>', $insurance->created_at)
            ->whereNull('resumed_insurance_id')->whereNull('refunded_insurance_id')
            ->with('coverages', 'coverages.type', 'refundInsurance')
            ->get();

        $coveragesTypes = LeasingAgreementInsuranceCoverageType::lists('name', 'id');

        return View::make('insurances.manage.card_file-yacht.dialog.attach-to-resume', compact('insurance_to_attach', 'insurance', 'coveragesTypes'));
    }

    public function getActivateInsurance($insurance_id)
    {
        $insurance = LeasingAgreementInsurance::findOrFail($insurance_id);
        return View::make('insurances.manage.card_file-yacht.dialog.activate-insurance', compact('insurance'));
    }

    public function getMarkAsYacht($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);
        return View::make('insurances.manage.card_file.dialog.mark-as-yacht', compact('agreement'));
    }

    public function postSetAsYachtAgreement($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);
        $agreement->has_yacht = 1;
        $agreement->save();

        Histories::leasingAgreementHistory($agreement_id, 27);

        $result['code'] = 0;
        return json_encode($result);
    }

    public function getMarkAsForeign($agreement_id)
    {
        $agreement = LeasingAgreement::find($agreement_id);
        return View::make('insurances.manage.card_file.dialog.mark-as-foreign', compact('agreement'));
    }

    public function postMarkAsForeignAgreement($agreement_id)
    {
        $agreement = LeasingAgreement::findOrFail($agreement_id);
        $agreement->if_foreign = !$agreement->if_foreign;
        $agreement->save();

        Histories::leasingAgreementHistory($agreement_id, 27);

        $result['code'] = 0;
        return json_encode($result);
    }
}
