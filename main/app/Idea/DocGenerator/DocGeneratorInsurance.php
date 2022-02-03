<?php
namespace Idea\DocGenerator;

use Config;
use Damage_type;
use File;
use Idea_data;
use IdeaOffices;
use LeasingAgreement;
use LeasingAgreementDocumentType;
use LeasingAgreementAnnexRefer;
use PDF;
use Text_contents;
use View;

class DocGeneratorInsurance {


    private $agreement;
    private $documentType;

    /**
     * @return mixed
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    private $inputs;
    private $filename;

    function __construct($agreement, $documentType, $inputs = array())
    {
        $this->agreement = LeasingAgreement::find($agreement);

        $this->documentType = LeasingAgreementDocumentType::find($documentType);

        $this->inputs = $inputs;

    }

    public function generateDoc()
    {
        $templatePath = $this->templatePath();

        if(!View::exists($templatePath))
            throw new FileNotFoundException("Nie znaleziono pliku: ".$this->templatePath());

        $agreement = $this->agreement;
        $inputs = $this->inputs;

        $annex_refers = LeasingAgreementAnnexRefer::lists('name','id');
	    $ideaA = $this->generateOwnerInfo();

        $html = View::make($templatePath, compact('agreement', 'inputs', 'annex_refers', 'ideaA'))->render();
        $html = preg_replace('/>\s+</', '><', $html);
        PDF::loadHTML($html)->setPaper('a4')->setOrientation('portrait')->setWarnings(false)->save($this->savePath());

        return $this->filename;
    }

    public function generateDocView()
    {
        $templatePath = $this->templatePath();

        if(!View::exists($templatePath))
            throw new FileNotFoundException("Nie znaleziono pliku: ".$this->templatePath());

        $agreement = $this->agreement;
        $inputs = $this->inputs;
	    $annex_refers = LeasingAgreementAnnexRefer::lists('name','id');
	    $ideaA = $this->generateOwnerInfo();

        return View::make($templatePath, compact('agreement', 'inputs', 'annex_refers', 'ideaA'));
    }


    /**
     * @return string
     */
    public function templatePath()
    {
        return 'insurances.docs_templates.' . $this->documentType->template_name;
    }


    /**
     * @return string
     */
    private function savePath()
    {
        $randomKey  = sha1( time() . microtime() );
        $this->filename = $this->agreement->id . '_' . $randomKey . '.pdf';

        $directory = Config::get('webconfig.WEBCONFIG_DOWNLOADS_FOLDER') . '/' . $this->documentType->short_name;

        if(! File::exists($directory))
            File::makeDirectory($directory);

        return $directory . '/' . $this->filename;
    }

	private function generateOwnerInfo()
	{
		$idea = Idea_data::whereOwner_id($this->agreement->owner_id)->get();

		$ideaA = array();
		foreach ($idea as $setting) {
			$ideaA[$setting->parameter_id] = $setting->value;
		}
		return $ideaA;
	}
}
