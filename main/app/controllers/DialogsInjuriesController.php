<?php

class DialogsInjuriesController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => array('post', 'delete', 'put')));
        $this->beforeFilter('permitted:zlecenia_(szkody)#zarzadzaj', ['only' => ['getRestoreCompleted', 'getCancel', 'getRestoreDeleted', 'getCompleteRefused', 'getComplete', 'getRefusal', 'getContractSettled', 'getAgreementSettled', 'getTotal', 'getTotalInjuries', 'getTheft', 'getUnlock', 'getLock', 'getTotalFinished', 'getDiscontinuationInvestigation', 'getDeregistrationVehicle', 'getTransferredDok', 'getNoSignsPunishment', 'getUsurpation', 'getRestoreTotal', 'getResignationClaims', 'getDelete']]);
        $this->beforeFilter('permitted:zlecenia_(szkody)#zarzadzaj#przepnij_szkode', ['only' => ['getChangeInjuryStatus']]);
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator', ['only' => ['getChangeInjuryStep']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_klienta', ['only' => ['getEditInjuryClient']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_szkody', ['only' => ['getEditInjury']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_polisy_ac', ['only' => ['getEditInjuryInsurance']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_informacje_wewnetrzna', ['only' => ['getEditInjuryInfo']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_pojazdu', ['only' => ['getEditVehicle']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_zglaszajacego', ['only' => ['getEditInjuryNotifier']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_kierowcy', ['only' => ['getEditInjuryDriver']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#zmien_osobe_kontaktowa', ['only' => ['getChangeContact']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_wlasciciela', ['only' => ['getEditVehicleOwner']]);
        $this->beforeFilter('permitted:kartoteka_szkody#uszkodzenia#edytuj_uwagi_do_uszkodzen', ['only' => ['getEditInjuryRemarks_damage']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dokumentacja#dodaj_dokument', ['only' => ['getDocumentSet']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dokumentacja#usun_dokument', ['only' => ['getDelDoc', 'getDelDocConf', 'setDelDoc', 'setDelDocConf']]);
        $this->beforeFilter('permitted:kartoteka_szkody#rozliczenia_szkody#zarzadzaj', ['only' => ['getEditInvoice', 'getDeleteInvoice', 'getEditCompensation', 'getEditCompensation', 'getDeleteCompensation']]);
        $this->beforeFilter('permitted:kartoteka_szkody#zdjecia#usun_zdjecie', ['only' => ['getDelImage']]);
        $this->beforeFilter('permitted:kartoteka_szkody#komunikator#zarzadzanie_etapem', ['only' => ['getChangeInjuryStep']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#edytuj_dane_sprawcy', ['only' => ['getEditInjuryOffender']]);
        $this->beforeFilter('permitted:kartoteka_szkody#dane_szkody_i_pojazdu#zmien_etap_procesowania', ['only' => ['getChangeStatus']]);
    }

    public function showOwner($id)
    {
        $owner = Owners::find($id);

        return View::make('injuries.dialog.showOwner', compact('owner'));
    }

    public function showClient($id)
    {
        $client = Clients::find($id);

        return View::make('injuries.dialog.showClient', compact('client'));
    }

    public function editClient($id)
    {

        $clients = Clients::where('active', '=', '0')->get();

        return View::make('injuries.dialog.editClient', compact('clients', 'id'));
    }

    public function editInsuranceCompany($id)
    {
		$insurance_companies = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();

        return View::make('injuries.dialog.editInsuranceCompany', compact('insurance_companies', 'id'));
    }

    public function showInsuranceCompany($id)
    {
        $insurance_company = Insurance_companies::find($id);

        return View::make('injuries.dialog.showInsuranceCompany', compact('insurance_company'));
    }

    public function getAssign($id = null, $owner_id = null)
    {
        $injury = Injury::find($id);

        if ($id == null || $id == "all") {
            return View::make('injuries.dialog.assign-create', compact('owner_id'));
        } else
            return View::make('injuries.dialog.assign', compact('injury'));
    }

    /*
    * Pokazuje dialog potwierdzenia przeniesiania szkody do etapu 'w osbłudze' bez przypisania warsztatu
    */
    public function getWithoutCompany($id)
    {

        $injury = Injury::find($id);

        return View::make('injuries.dialog.without_company', compact('injury'));
    }

    public function getEditInjuryBranch($id)
    {
        $injury = Injury::find($id);

        return View::make('injuries.dialog.edit-branch', compact('injury'));
    }

    public function getEditInjuryBranchOriginal($id)
    {
        $injury = Injury::find($id);

        return View::make('injuries.dialog.edit-branch-original', compact('injury'));
    }

    public function getDeleteInjuryBranch($id)
    {
        $injury = Injury::find($id);

        return View::make('injuries.dialog.delete-branch', compact('injury'));
    }

    public function getReturnInjuryBranch($id)
    {
        $injury = Injury::find($id);

        return View::make('injuries.dialog.return-branch', compact('injury'));
    }

    public function getCancel($id)
    {
        return View::make('injuries.dialog.cancel', compact('id'));
    }

    public function getChangeInjuryStatus($injury_id)
    {
        $injury = Injury::with('branch.company.groups')->find($injury_id);

        if (
            $injury->branch_id != '-1' && $injury->branch_id != 0 &&

            (
                ($injury->branch->company->groups->contains(5) && $injury->vehicle->cfm == 1)
                || $injury->branch->company->groups->contains(1)
            )
        ) {
            $if_edb = 1;
        } else {
            $if_edb = 0;
        }

        $statuses = InjurySteps::where('id', '!=', $injury->step)->whereNotIn('id', ['-3', '-5'])->where(function ($query) use ($if_edb) {
            //    $query->whereNull('edb')->orWhere('edb', $if_edb);
        })->lists('name', 'id');

        return View::make('injuries.dialog.change-status', compact('injury', 'statuses'));
    }

    public function getChangeInjuryStep($injury_id)
    {
        $injury = Injury::find($injury_id);
        $steps = [];
        if (in_array($injury->step, [30, 31, 32, 33, 34, 35, 36, 37])) {
            $steps = InjuryStepTotal::has('stage')->get();
        } elseif (in_array($injury->step, [40, 41, 42, 43, 44, 45, 46])) {
            $steps = InjuryStepTheft::has('stage')->get();
        }

        return View::make('injuries.dialog.change-step', compact('injury', 'steps'));
    }

    public function getRestore($id)
    {
        $injury = Injury::find($id);
        return View::make('injuries.dialog.restore', compact('id', 'injury'));
    }

    public function getRestoreCanceled($id)
    {
        return View::make('injuries.dialog.restoreCanceled', compact('id'));
    }

    public function getRestoreDeleted($id)
    {
        return View::make('injuries.dialog.restoreDeleted', compact('id'));
    }

    public function getRestoreTotal($id)
    {
        return View::make('injuries.dialog.restoreTotal', compact('id'));
    }

    public function getResignationClaims($id)
    {
        return View::make('injuries.dialog.resignationClaims', compact('id'));
    }

    public function getContractSettled($id)
    {
        return View::make('injuries.dialog.contractSettled', compact('id'));
    }

    public function getDiscontinuationInvestigation($id)
    {
        return View::make('injuries.dialog.discontinuation-investigation', compact('id'));
    }

    public function getAgreementSettled($id)
    {
        return View::make('injuries.dialog.agreement-settled', compact('id'));
    }

    public function getDeregistrationVehicle($id)
    {
        return View::make('injuries.dialog.deregistration-vehicle', compact('id'));
    }

    public function getTransferredDok($id)
    {
        return View::make('injuries.dialog.transferred-dok', compact('id'));
    }

    public function getNoSignsPunishment($id)
    {
        return View::make('injuries.dialog.no-signs-punishment', compact('id'));
    }

    public function getUsurpation($id)
    {
        return View::make('injuries.dialog.usurpation', compact('id'));
    }

    public function getRestoreCompleted($id)
    {
        $injury = Injury::find($id);
        $steps = InjurySteps::all();
        return View::make('injuries.dialog.restoreCompleted', compact('id', 'injury', 'steps'));
    }

    public function getTotal($id)
    {
        return View::make('injuries.dialog.total', compact('id'));
    }

    public function getTotalFinished($id)
    {
        return View::make('injuries.dialog.totalFinished', compact('id'));
    }

    public function getTheft($id)
    {
        return View::make('injuries.dialog.theft', compact('id'));
    }

    public function getInprogress($id)
    {
        return View::make('injuries.dialog.inprogress', compact('id'));
    }

    public function getComplete($id)
    {
        return View::make('injuries.dialog.complete', compact('id'));
    }

    public function getCompleteRefused($id)
    {
        return View::make('injuries.dialog.complete-refused', compact('id'));
    }

    public function getTotalInjuries($id)
    {
        return View::make('injuries.dialog.total-injuries', compact('id'));
    }

    public function getCompleteL($id)
    {
        return View::make('injuries.dialog.complete-l', compact('id'));
    }

    public function getCompleteN($id)
    {
        return View::make('injuries.dialog.complete-n', compact('id'));
    }

    public function getRefusal($id)
    {
        return View::make('injuries.dialog.refusal', compact('id'));
    }

    public function getUnlock($id)
    {
        return View::make('injuries.dialog.unlock', compact('id'));
    }

    public function getLock($id)
    {
        return View::make('injuries.dialog.lock', compact('id'));
    }

    public function getDocumentSet()
    {
        $input = Input::get('files');
        $documentTypes = InjuryUploadedDocumentType::whereNull('parent_id')->orderBy('ordering')->whereNull('hidden')->get();
        return View::make('injuries.dialog.document', compact('input', 'documentTypes'));
    }

    public function getDelDoc($id)
    {
        $file = InjuryFiles::find($id);

        return View::make('injuries.dialog.docDelete', compact('id', 'file'));
    }

    public function getDelDocConf($id)
    {
        return View::make('injuries.dialog.docDeleteConf', compact('id'));
    }

    public function getDelImage($id)
    {
        return View::make('injuries.dialog.imageDelete', compact('id'));
    }

    public function getChangeContact($id)
    {
        return View::make('injuries.dialog.changeContact', compact('id'));
    }

    public function getEditInvoice($id)
    {
        $invoice = InjuryInvoices::where('id', '=', $id)->with('injury_files', 'serviceType')->first();
        $serviceTypes = InjuryInvoiceServiceType::lists('name', 'id');
        $serviceTypes[0] = '--- wybierz rodzaj usługi ---';
        asort($serviceTypes);

        $base_invoice = $invoice->parent;
        if (!$base_invoice) {
            $base_invoice = new stdClass();
            $base_invoice->netto = 0;
            $base_invoice->vat = 0;
        }

        $invoices = [];
        if ($invoice->injury_files && $invoice->injury_files->category == 4) {
            foreach (InjuryInvoices::with('injury_files')->where('injury_id', '=', $invoice->injury_id)->where('active', '=', '0')->where('id', '!=', $id)->get() as $k => $v) {
                $invoices[] = [
                    'id' => $v->id,
                    'netto' => $v->netto,
                    'vat' => $v->vat,
                    'category' => $v->injury_files->category,
                    'invoice_nr' => $v->invoice_nr,
                    'parent' => ($v->parent) ? $v->parent->invoice_nr : '---'
                ];
            }
        }

        $vat_rates = VatRate::lists('name', 'id');

        $commissionable = 0;

        if ($invoice->injury->branch && $invoice->injury->branch->company) {
            if ($invoice->injury->branch->company->groups->contains(1) || $invoice->injury->branch->company->groups->contains(5)) {
                $commissionable = 1;
            }
        }

        return View::make('injuries.dialog.edit-invoice', compact('id', 'invoice', 'base_invoice', 'invoices', 'serviceTypes', 'vat_rates', 'commissionable'));
    }

    public function getDeleteInvoice($id)
    {
        $invoice = InjuryInvoices::find($id);

        return View::make('injuries.dialog.delete-invoice', compact('invoice'));
    }

    public function getDeleteCompensation($id)
    {
        $compensation = InjuryCompensation::find($id);

        return View::make('injuries.dialog.delete-compensation', compact('compensation'));
    }

    public function getDeleteEstimate($id)
    {
        $estimate = InjuryEstimate::find($id);

        return View::make('injuries.dialog.delete-estimate', compact('estimate'));
    }

    public function getEditInjuryMap($id)
    {
        $injury = Injury::find($id);

        return View::make('injuries.dialog.edit-map', compact('id', 'injury'));
    }

    public function getDateAdmission($id)
    {
        $injury = Injury::find($id);

        return View::make('injuries.dialog.date-admission', compact('id', 'injury'));
    }

    public function getAddHistory($id)
    {

        return View::make('injuries.dialog.add-history', compact('id'));
    }

    public function getEditInjuryInfo($id)
    {
        $injury = Injury::find($id);
        $info = Text_contents::find($injury->info);


        return View::make('injuries.dialog.edit-info', compact('id', 'info', 'injury'));
    }

    public function getEditInjuryRemarks_damage($id)
    {
        $injury = Injury::find($id);
        $info = Text_contents::find($injury->remarks_damage);


        return View::make('injuries.dialog.edit-remarks-damage', compact('id', 'info', 'injury'));
	}

	public function getEditInjury($id)
	{
		$injury = Injury::find($id);
		$injuries_type = Injuries_type::all();
		$receives = Receives::all();
		$invoicereceives = Invoicereceives::all();
		$type_incident = Type_incident::orderBy('order', 'asc')->get();
		$insurance_company = Insurance_companies::where('active','=','0')->orderBy('name')->whereNull('parent_id')->get();

        if($injury->injuries_type_id == '2' || $injury->injuries_type_id == '4' || $injury->injuries_type_id == '5'){
            if(is_null($injury->offender()->first())) {
                $offender = Offenders::create(array());
                $id_offender = $offender->id;
                $injury->offender_id = $id_offender;
                $injury->save();
            }
        }

        return View::make('injuries.dialog.edit', compact('id', 'injuries_type', 'receives', 'type_incident', 'injury', 'invoicereceives', 'insurance_company'));
    }

    public function getEditInjuryInsurance($id)
    {
        $injury = Injury::find($id);
        $policy = $injury->injuryPolicy;

        $insuranceCompanies = Insurance_companies::lists('name', 'id');

        $gapInsuranceCompanies = Insurance_companies::where(function ($query) {
            $query->where('name', 'like', '%compensa%')
                ->orWhere('name', 'like', '%europa%');
        })->lists('name', 'id');
        $gapInsuranceCompanies = ['' => '--- wybierz ---'] + $gapInsuranceCompanies;

        return View::make('injuries.dialog.edit-insurance', compact('id', 'injury', 'policy', 'insuranceCompanies', 'gapInsuranceCompanies'));
    }

    public function getEditInjuryGap($id)
    {
        $injury = Injury::with('injuryGap')->find($id);

        $insuranceCompanies = ['' => '--- wybierz ---'] + Insurance_companies::where(function ($query) {
            $query->where('name', 'like', '%compensa%')
                ->orWhere('name', 'like', '%europa%');
        })->lists('name', 'id');
        $gapTypes = GapType::lists('name', 'id');

        return View::make('injuries.dialog.edit-gap', compact('id', 'injury', 'vehicle', 'insuranceCompanies', 'gapTypes'));
    }

    public function getEditInjuryDriver($id)
    {
        $injury = Injury::find($id);
        $driver = Drivers::find($injury->driver_id);

        return View::make('injuries.dialog.edit-driver', compact('id', 'injury', 'driver'));
    }

    public function getEditInjuryOffender($id)
    {
        $injury = Injury::find($id);

        if (is_null($injury->offender()->first())) {
            $offender = Offenders::create(array());
            $id_offender = $offender->id;
            $injury->offender_id = $id_offender;
            $injury->save();
        }

        $offender = Offenders::find($injury->offender_id);

        return View::make('injuries.dialog.edit-offender', compact('id', 'injury', 'offender'));
    }

    public function getEditInjuryNotifier($id)
    {
        $injury = Injury::find($id);

        return View::make('injuries.dialog.edit-notifier', compact('id', 'injury'));
    }

    public function getEditInjuryClientContact($id)
    {
        $injury = Injury::with('client')->find($id);

        return View::make('injuries.dialog.edit-clientContact', compact('id', 'injury'));
    }

    public function getEditInjuryClient($id)
    {
        $injury = Injury::with('client')->find($id);

        return View::make('injuries.dialog.edit-client', compact('id', 'injury'));
    }


    public function getDamages($id)
    {
        $damages = MobileInjuryDamage::whereMobile_injury_id($id)->get();

        $damagesA = array();

        foreach ($damages as $damage) {
            $damagesA[$damage->mobile_damage_type_id] = 1;
        }

        $damages_type = MobileDamageType::all();

        $damages_typeA = array();

        foreach ($damages_type as $damage_type) {
            $damages_typeA[$damage->id] = $damage_type;
        }

        return View::make('injuries.dialog.damages', compact('damagesA', 'damages_typeA'));
    }

    public function getUploadesPictures($id)
    {
        $pictures = MobileInjuryFile::whereMobile_injury_id($id)->get();

        return View::make('injuries.dialog.pictures', compact('pictures'));
    }

    public function getDelete($id)
    {
        return View::make('injuries.dialog.delete', compact('id'));
    }

    public function getDeleteEa($id)
    {
        return View::make('injuries.dialog.delete-ea', compact('id'));
    }

    public function getChangeStatus($id)
    {
        $injury = Injury::find($id);
        $status = InjuryTotalStatuses::find($injury->totalStatus->manual_changeable);
        $url = URL::route('injuries.total.setStatus', array($id, $status->id));

        return View::make('injuries.dialog.changeStatus', compact('injury', 'status', 'url'));
    }

    public function getEditVehicle($id)
    {
        $vehicle = Injury::find($id)->vehicle()->first();
        return View::make('injuries.dialog.edit-vehicle', compact('vehicle', 'id'));
    }

    public function getEditVehicleOwner($id)
    {
        $vehicle = Injury::find($id)->vehicle()->first();
        $owners_db = Owners::whereActive(0)->get();
        $owners = [];
        foreach ($owners_db as $owner) {
            $owners[$owner->id] = ($owner->old_name) ? $owner->name . ' (' . $owner->old_name . ')' : $owner->name;
        }
        return View::make('injuries.dialog.edit-vehicle-owner', compact('vehicle', 'id', 'owners'));
    }

    public function getEditCompensation($id)
    {
        $compensation = InjuryCompensation::with('injury_file')->find($id);
        $decisionTypes = InjuryCompensationDecisionType::lists('name', 'id');
        $receives = Receives::lists('name', 'id');

        $injury = $compensation->injury;
        if($injury->sap && ((!$compensation->premium && $compensation->mode == 2) || ($compensation->mode == 1 && $compensation->compensation == 0))){
            $sap = new \Idea\SapService\Sap();
            $sap_remote_data = $sap->szkodaPobierz($injury->sap->szkodaId);
            $injury->sap->update($sap_remote_data['fsSzkodaOut']);

            $ftDoplaty = $sap->getExistingSapDoplaty($injury->sap);

            if ($ftDoplaty && count($ftDoplaty) > 0) {
                $premiums = [];
                foreach ($ftDoplaty as $ftDoplata) {
                    $premium = $injury->sapPremiums()->where('nrRaty', 'like', $ftDoplata['nrRaty'])->first();
                    if($premium){
                        if(! $premium->injuryCompensation) $premium->update($ftDoplata);
                    }else{
                        $premium = $injury->sapPremiums()->create($ftDoplata);
                    }
                    $premiums[] = $premium->id;
                }
                $injury->sapPremiums()->whereNotIn('id', $premiums)->delete();
            }

            $premiums = $compensation->injury->sapPremiums()->has('injuryCompensation', '<', 1)->get();

            if(count($premiums) > 0 || $injury->sap->kwotaOdsz > 0) {
                return View::make('injuries.dialog.document-match-sap', compact('compensation', 'premiums'));
            }
        }

        return View::make('injuries.dialog.edit-compensation', compact('compensation', 'decisionTypes', 'receives'));
    }

    public function appendLetter($letter_id, $injury_id)
    {
        return View::make('injuries.dialog.appendLetter', compact('letter_id', 'injury_id'));
    }

    public function getEditEstimate($id)
    {
        $estimate = InjuryEstimate::with('injury_file')->find($id);

        return View::make('injuries.dialog.edit-estimate', compact('estimate'));
    }

    public function previewDoc($id, $type = null)
    {
        if ($type == 'letter') {
            $file = InjuryLetter::find($id);
        } else {
            $file = InjuryFiles::find($id);
        }
        if ($type && $type != 'letter') {
            $documentType = InjuryDocumentType::find($type);
            $path = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . "/" . $documentType->short_name . "/" . $file->file;
        } else {
            $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . "/files/" . $file->file;
        }

        $ext = File::extension($path);

        return View::make('injuries.dialog.preview-doc', compact('id', 'type', 'ext'));
    }


    public function getInvoiceForward($invoice_id)
    {
        $invoice = InjuryInvoices::with('injury_files')->find($invoice_id);
        $compensations = $invoice->injury->compensations;

        $injuryInvoiceForwardDocumentTypes = InjuryInvoiceForwardDocumentType::orderBy('position')->get();

        return View::make('injuries.dialog.invoice-forward', compact('invoice', 'injuryInvoiceForwardDocumentTypes', 'compensations'));
    }

    public function getInvoiceReturn($invoice_id)
    {
        $invoice = InjuryInvoices::with('injury_files')->find($invoice_id);

        $injuryInvoiceForwardDocumentTypes = InjuryInvoiceForwardDocumentType::orderBy('position')->get();
        $injuryInvoiceForwardDocuments = $invoice->injuryInvoiceForwardDocuments->lists('injury_invoice_forward_document_type_id');

        return View::make('injuries.dialog.invoice-return', compact('invoice', 'injuryInvoiceForwardDocumentTypes', 'injuryInvoiceForwardDocuments'));
    }

    public function getInvoiceForwardAgain($invoice_id)
    {
        $invoice = InjuryInvoices::with('injury_files')->find($invoice_id);

        $injuryInvoiceForwardDocumentTypes = InjuryInvoiceForwardDocumentType::orderBy('position')->get();
        $injuryInvoiceForwardDocuments = $invoice->injuryInvoiceForwardDocuments->lists('injury_invoice_forward_document_type_id');
        
        $compensations = $invoice->injury->compensations;
        $invoiceCompensations = $invoice->compensations->lists('id');
        return View::make('injuries.dialog.invoice-forward-again', compact('invoice', 'injuryInvoiceForwardDocumentTypes', 'injuryInvoiceForwardDocuments', 'compensations', 'invoiceCompensations'));
    }


    public function createCessionAmounts($id)
    {
        $injury_id = $id;

        return View::make('injuries.dialog.create-cession', compact('injury_id'));
    }

    public function editCessionAmounts($id)
    {
        $cessionAmount = InjuryCessionAmount::find($id);

        return View::make('injuries.dialog.edit-cession', compact('cessionAmount'));
    }

    public function getAddNote($injury_id)
    {
        return View::make('injuries.dialog.add-note', compact('injury_id'));
    }

    public function getDocumentMatchSap($compensation_id)
    {
        $compensation = InjuryCompensation::findOrFail($compensation_id);
        $injury = $compensation->injury;

        $sap = new \Idea\SapService\Sap();
        $sap_remote_data = $sap->szkodaPobierz($injury->sap->szkodaId);
        $injury->sap->update($sap_remote_data['fsSzkodaOut']);

        $ftDoplaty = $sap->getExistingSapDoplaty($injury->sap);

        if($ftDoplaty && count($ftDoplaty) > 0){
            $premiums = [];
            foreach ($ftDoplaty as $ftDoplata)
            {
                $premium = $injury->sapPremiums()->where('nrRaty', 'like', $ftDoplata['nrRaty'])->first();
                if($premium){
                    if(! $premium->injuryCompensation) $premium->update($ftDoplata);
                }else{
                    $premium = $injury->sapPremiums()->create($ftDoplata);
                }
                $premiums[] = $premium->id;
            }
            $injury->sapPremiums()->whereNotIn('id', $premiums)->delete();
        }

        $premiums = $compensation->injury->sapPremiums()->has('injuryCompensation', '<', 1)->get();

        return View::make('injuries.dialog.document-match-sap', compact('compensation', 'premiums'));
    }


    public function getDeletePremium($id)
    {
        $premium = InjurySapPremium::find($id);

        return View::make('injuries.dialog.delete-premium', compact('premium'));
    }

    public function getInjuryBranchesHistory($injury_id)
    {
        $injury = Injury::with('branches.branch')->find($injury_id);

        return View::make('injuries.dialog.branches-history', compact('injury'));
    }

    public function getAddInjuryBranchesHistory($injury_id){
	    $injury = Injury::find($injury_id);

        return View::make('injuries.dialog.add-branches-history', compact('injury'));
    }
}
