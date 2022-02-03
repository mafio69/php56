<?php
namespace Idea\DocGenerator;

use Config;
use Damage_type;
use Exception;
use File;
use Idea_data;
use IdeaOffices;
use Injury;
use InjuryInvoices;
use Branch;
use PDF;
use Text_contents;
use Log;
use CompanyAccountNumbers;
use View;

class DocGenerator {


    private $injury;
    private $branch;
    private $documentType;
    private $path;
    private $injuryTable;
    private $vehicle;
    private $owner;
    private $inputs;
    private $filename;


    public function getFilename()
    {
        return $this->filename;
    }

    public function getDocumentType()
    {
        return $this->documentType;
    }

    public function getPath()
    {
        return $this->path;
    }

    function __construct($injury, $injuryTable, $documentType, $inputs = array(), $branch)
    {
        $this->injury = Injury::with('injuryPolicy')->find($injury);

        $this->injuryTable = $injuryTable;

        $this->documentType = $this->generateClassInstance('DocumentType')->findOrFail($documentType);

        $this->inputs = $inputs;
        $this->branch = Branch::find($branch);

        $this->vehicle = $this->injury->vehicle;
        $this->owner = $this->injury->vehicle->owner()->first();
    }

    public function generateDocView()
    {
        if($this->documentType->pdf == 1)
        {
            File::copy(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/' . $this->documentType->template_name.'.pdf', $this->savePath());
            return $this->filename;
        }

        $templatePath = $this->templatePath();

        if(!View::exists($templatePath))
            throw new FileNotFoundException("Nie znaleziono pliku: ".$this->templatePath());

        $injury = $this->injury;
        $inputs = $this->inputs;
        $branch = $this->branch;
        $damageSet = $this->generateClassInstance('Damage')->where('injury_id', '=', $injury->id)->get();
        $damage = Damage_type::all();
        $remarks = Text_contents::find($injury->remarks_damage);

        if(isset($inputs['idea_office_id'])) $ideaOffice = IdeaOffices::find($inputs['idea_office_id']);
        else $ideaOffice = '';

        if(isset($inputs['invoices'])) $invoices = InjuryInvoices::with('injury_files', 'serviceType')->find($inputs['invoices']);
        else $invoices = null;

        if(isset($inputs['account_id'])) $choosed_account = CompanyAccountNumbers::find($inputs['account_id']);
        else $choosed_account = null;

        $ideaA = $this->generateOwnerInfo();
        $owner_group = $this->owner->group;
        $owner = $this->owner;
        $vehicle = $injury->vehicle;

        if($owner->conditionalDocumentTemplate){
            $contract_number = $vehicle->nr_contract;

            if(! preg_match('/.*\/\d{4}$/', $contract_number)){
                $documentTemplate = $owner->conditionalDocumentTemplate;
            }else{
                $documentTemplate = $owner->documentTemplate;
            }
        }elseif($owner->documentTemplate){
            $documentTemplate = $owner->documentTemplate;
        }else{
            $documentTemplate = \DocumentTemplate::where('slug', 'default')->first();
        }

        return View::make($templatePath, compact('injury', 'damage', 'damageSet', 'remarks', 'inputs', 'ideaA', 'ideaOffice', 'owner_group', 'owner', 'vehicle', 'documentTemplate', 'branch', 'invoices', 'choosed_account'));
    }

    public function generatePreview($filename)
    {
        if($this->documentType->pdf == 1)
        {
            File::copy(Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/templates/' . $this->documentType->template_name.'.pdf', $this->savePath());
            return $this->filename;
        }
        $templatePath = $this->templatePath();

        if(!View::exists($templatePath))
            throw new FileNotFoundException("Nie znaleziono pliku: ".$this->templatePath());

        $injury = $this->injury;
        $inputs = $this->inputs;
        //deb
        $inputs['description']=str_random();
        $inputs['nr_account']=str_random();
        $inputs['date_submit']=date('Y-m-d');
        $inputs['person']=str_random();;
        $inputs['nr_id']=str_random();
        $inputs['car_location']=str_random();
        $inputs['address']=str_random();
        $inputs['name']=str_random();
        $inputs['id_series']=str_random();
        $inputs['id_number']=str_random();
        $inputs['pesel']=str_random();
        $inputs['vehicle_card_number']=str_random();
        $inputs['invoice_number']=str_random();
        $inputs['registration_document_series']=str_random();
        $inputs['registration_document_number']=str_random();
        $inputs['vehicle_card_series']=str_random();
        $inputs['vehicle_card_number']=str_random();
        $inputs['nr_invoice']=str_random();
        $inputs['company']=str_random();
        $inputs['address_pl']=str_random();
        $inputs['date']=date('Y-m-d');
        $inputs['address_en']=str_random();
        $inputs['address_pl']=str_random();
        $inputs['document_date']=date('Y-m-d');
        $inputs['expire_date']=date('Y-m-d');
        $inputs['broker_date']=date('Y-m-d');
        $inputs['value_before']=str_random();
        $inputs['value_after']=str_random();
        $inputs['value_compensation']=str_random();
        $inputs['price']=str_random();
        $inputs['info_date']=date('Y-m-d');
        $inputs['vehicle_location']=str_random();
        $inputs['extra_costs']=str_random();
        $inputs['receive_date']=date('Y-m-d');
        $inputs['documents_location']=str_random();
        $inputs['transport_costs']=str_random();
        $inputs['sending_date']=str_random();
        $inputs['value_undamaged']=str_random();
        $inputs['value_undamaged_net_gross']=str_random();
        $inputs['value_repurchase']=str_random();
        $inputs['value_repurchase_net_gross']=str_random();
        $inputs['value_compensation']=str_random();
        $inputs['value_compensation_net_gross']=str_random();
        $inputs['expire_tenderer']=date('Y-m-d');
        $inputs['net_value']=str_random();
        $inputs['currency']=str_random();
        $inputs['for_whom']=str_random();
        $inputs['for_whom_info']=str_random();
        $inputs['remarks']=str_random();
        $inputs['email_comment']=str_random();

        $damageSet = $this->generateClassInstance('Damage')->where('injury_id', '=', $injury->id)->get();
        $damage = Damage_type::all();
        $remarks = Text_contents::find($injury->remarks_damage);

        if(isset($inputs['idea_office_id']))
            $ideaOffice = IdeaOffices::find($inputs['idea_office_id']);
        else
            $ideaOffice = '';

        $ideaA = $this->generateOwnerInfo();
        $owner_group = $this->owner->group;
        $owner = $this->owner;
        $vehicle = $injury->vehicle;

        if($owner->conditionalDocumentTemplate){
            $contract_number = $vehicle->nr_contract;

            if(! preg_match('/.*\/\d{4}$/', $contract_number)){
                $documentTemplate = $owner->conditionalDocumentTemplate;
            }else{
                $documentTemplate = $owner->documentTemplate;
            }
        }elseif($owner->documentTemplate){
            $documentTemplate = $owner->documentTemplate;
        }else{
            $documentTemplate = \DocumentTemplate::where('slug', 'default')->first();
        }

        $html = View::make($templatePath, compact('injury', 'damage', 'damageSet', 'remarks', 'inputs', 'ideaA', 'ideaOffice', 'owner_group', 'owner', 'vehicle', 'documentTemplate'));

        $pdf = PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false);

        $pdf->save(storage_path('previews/'.$owner->group->name . '_' . $this->documentType->short_name . '.pdf'));

        return $pdf->download($filename.'.pdf');
    }

    public function debug()
    {
        $templatePath = $this->templatePath();

        if(!View::exists($templatePath))
            throw new FileNotFoundException("Nie znaleziono pliku: ".$this->templatePath());

        $injury = $this->injury;
        $inputs = $this->inputs;
        $damageSet = $this->generateClassInstance('Damage')->where('injury_id', '=', $injury->id)->get();
        $damage = Damage_type::all();
        $remarks = Text_contents::find($injury->remarks_damage);

        if(isset($inputs['idea_office_id']))
            $ideaOffice = IdeaOffices::find($inputs['idea_office_id']);
        else
            $ideaOffice = '';

        $ideaA = $this->generateOwnerInfo();
        $owner_group = $this->owner->group;
        $owner = $this->owner;
        $vehicle = $injury->vehicle;

        /*
         * aby zdebugować html dokumentu odkomentować linijkę poniżej
         *
         */
        return View::make($templatePath, compact('injury', 'damage', 'damageSet', 'remarks', 'inputs', 'ideaA', 'ideaOffice', 'owner_group', 'owner', 'vehicle'));

        $html = View::make($templatePath, compact('injury', 'damage', 'damageSet', 'remarks', 'inputs', 'ideaA', 'ideaOffice', 'owner_group', 'owner', 'vehicle'));
        PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false)->save($this->savePath());

        return $this->filename;
    }

    /**
     * @param $className
     * @return string
     */
    private function generateClassInstance($className)
    {
        $documentTypeClass = $this->injuryTable . $className;
        return new $documentTypeClass();
    }

    /**
     * @return string
     */
    public function templatePath()
    {
        if(file_exists(app_path('views/injuries/docs_templates/'.$this->injury->vehicle->owner->group->name.'/'.$this->documentType->template_name.'.blade.php')))
            return 'injuries.docs_templates.' . $this->injury->vehicle->owner->group->name. '.'. $this->documentType->template_name;

        return 'injuries.docs_templates.' . $this->documentType->template_name;
    }


    /**
     * @return string
     */
    public function savePath()
    {
        $randomKey  = sha1( time() . microtime() );
        $this->filename = $this->injury->id . '_' . $randomKey . '.pdf';

        $directory = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/' . $this->documentType->short_name;

        if(! File::exists($directory))
            File::makeDirectory($directory);

        $this->path = $directory . '/' . $this->filename;

        return $directory . '/' . $this->filename;
    }

    /**
     * @return mixed
     */
    private function generateOwnerInfo()
    {
        $idea = Idea_data::whereOwner_id($this->owner->id)->get();

        $ideaA = array();
        foreach ($idea as $setting) {
            $ideaA[$setting->parameter_id] = $setting->value;
        }
        return $ideaA;
    }

}
