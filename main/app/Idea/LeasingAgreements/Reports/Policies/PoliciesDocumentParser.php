<?php
namespace Idea\LeasingAgreements\Reports\Policies;

use Config;
use Event;
use Excel;
use File;
use Idea\Exceptions\ImportException;
use Idea\LeasingAgreements\AgreementDocumentParserInterface;
use Idea\LeasingAgreements\BaseAgreementDocumentParser;

class PoliciesDocumentParser extends BaseAgreementDocumentParser implements AgreementDocumentParserInterface
{

    private $insurance_company_id;
    private $reader;

    function __construct($filename, $insurance_company_id)
    {
        set_time_limit(1000);

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/policies/';
        $this->file = $path.$filename;
        $this->insurance_company_id = $insurance_company_id;

        if(!File::exists($path)) {
            File::makeDirectory($path,511, true);
        }
    }

    function load()
    {
        if(file_exists($this->file)) {
            if ($this->reader = Excel::load($this->file, 'windows-1250')) {
                return true;
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    function parse_rows()
    {
        $sheet = $this->reader->getActiveSheet();

        $parser = new PoliciesParser($sheet, $this->insurance_company_id, $this->filename);
        try {
            $parser->parse();
        }catch (ImportException $exception){
            return $this->parseFailed($exception->getMessage());
        }

        $parsedResult = array(
            'parsed' => $parser->parsed,
            'existing' => $parser->existing,
            'missing' => $parser->missing
        );

        return $parsedResult;
    }
}
