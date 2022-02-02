<?php

namespace Idea\LeasingAgreements\Reports\Complex;


use Config;
use Event;
use Excel;
use File;
use Idea\LeasingAgreements\AgreementDocumentParserInterface;
use Idea\LeasingAgreements\BaseAgreementDocumentParser;
use Response;

class ReportComplexDocumentParser extends BaseAgreementDocumentParser implements AgreementDocumentParserInterface {

    private $reader;

    private $baseSheetsList =
        [
            0 => 'NOWE',
            1 => 'NOWE POZYCZKA',
            2 => 'WZNOW',
            3 => 'WZNO POZYCZKA',
            4 => 'WZNOW NA 2 MCE',
            5 => 'ZWROT SKŁADKI'
        ];
    private $owner_id;
    private $insurance_company_id;

    /**
     * @var
     */
    private $notification_number;
    /**
     * @var
     */
    private $leasing_agreement_payment_way_id;

    function __construct($filename, $owner_id, $insurance_company_id, $notification_number, $leasing_agreement_payment_way_id)
    {
        set_time_limit(500);

        $path = Config::get('webconfig.WEBCONFIG_UPLOADS_FOLDER') . '/insurances/report/';
        $this->file = $path.$filename;
        $this->owner_id = $owner_id;
        $this->insurance_company_id = $insurance_company_id;

        if(!File::exists($path)) {
            File::makeDirectory($path,511, true);
        }
        $this->notification_number = $notification_number;
        $this->leasing_agreement_payment_way_id = $leasing_agreement_payment_way_id;
    }

    function load()
    {
        if(file_exists($this->file)) {
            if ($this->reader = Excel::load($this->file, 'windows-1250')) {
                if( ! $this->checkFileStructure() )
                    return false;

                return true;
            }
        }

        return $this->parseFailed("Błąd odczytu pliku, skontaktuj się z administratorem.");
    }

    function parse_rows()
    {
        $this->parseNewLeasingAgreements();
        $this->parseNewLoanAgreements();
        $this->parseResumeLeasingAgreements();
        $this->parseResumeLoanAgreements();
        $this->parseResume2MonthsAgreements();
        $this->parseRefundAgreements();
    }

    private function checkFileStructure()
    {
        $sheetNames = $this->reader->getSheetNames();
        if( count(array_diff($sheetNames, $this->baseSheetsList)) === 0)
        {
            return true;
        }

        return $this->parseFailed("Błędna struktura pliku.", "Błędna nazwa arkuszy.");
    }

    public function parseNewLeasingAgreements()
    {
        $newLeasingSheet = $this->reader->getSheetByName($this->baseSheetsList[0]);
        if(! $newLeasingSheet ) {
            return ['error' => 'brak arkusza NOWE w importowanym pliku'];
        }

        $parser = new Parsers\NewAgreementParser($newLeasingSheet, $this->owner_id, $this->insurance_company_id, 2, $this->notification_number, $this->leasing_agreement_payment_way_id, $this->filename);
        $parser->parse();
        $parsedResult = array(
            'parsedAgreements' => $parser->parsedAgreementsList,
            'existingAgreements' => $parser->existingAgreementsList
        );

        return $parsedResult;
    }

    public function parseNewLoanAgreements()
    {
        $newLoanSheet = $this->reader->getSheetByName($this->baseSheetsList[1]);
        if(! $newLoanSheet ) {
            return ['error' => 'brak arkusza NOWE POZYCZKA w importowanym pliku'];
        }

        $parser = new Parsers\NewAgreementParser($newLoanSheet, $this->owner_id, $this->insurance_company_id, 1, $this->notification_number, $this->leasing_agreement_payment_way_id, $this->filename);
        $parser->parse();
        $parsedResult = array(
            'parsedAgreements' => $parser->parsedAgreementsList,
            'existingAgreements' => $parser->existingAgreementsList
        );

        return $parsedResult;
    }

    public function parseResumeLeasingAgreements()
    {
        $resumeLeasingSheet = $this->reader->getSheetByName($this->baseSheetsList[2]);
        if(! $resumeLeasingSheet ) {
            return ['error' => 'brak arkusza WZNOW w importowanym pliku'];
        }
        $parser = new Parsers\ResumeAgreementParser($resumeLeasingSheet, $this->owner_id, $this->insurance_company_id, 2, $this->notification_number, 2, $this->filename);
        $parser->parse();
        $parsedResult = array(
            'parsedAgreements' => $parser->parsedAgreementsList,
            'existingAgreements' => $parser->existingAgreementsList,
            'alreadyResumedAgreements' => $parser->alreadyResumedAgreementsList
        );

        return $parsedResult;
    }

    public function parseResumeLoanAgreements()
    {
        $resumeLoanSheet = $this->reader->getSheetByName($this->baseSheetsList[3]);
        if(! $resumeLoanSheet ) {
            return ['error' => 'brak arkusza WZNO POZYCZKA w importowanym pliku'];
        }

        $parser = new Parsers\ResumeAgreementParser($resumeLoanSheet, $this->owner_id, $this->insurance_company_id, 1, $this->notification_number, 2, $this->filename);
        $parser->parse();
        $parsedResult = array(
            'parsedAgreements' => $parser->parsedAgreementsList,
            'existingAgreements' => $parser->existingAgreementsList,
            'alreadyResumedAgreements' => $parser->alreadyResumedAgreementsList
        );

        return $parsedResult;
    }

    public function parseResume2MonthsAgreements()
    {
        $resume2MonthsSheet = $this->reader->getSheetByName($this->baseSheetsList[4]);
        if(! $resume2MonthsSheet ) {
            return ['error' => 'brak arkusza WZNOW NA 2 MCE w importowanym pliku'];
        }

        $parser = new Parsers\ResumeAgreementParser($resume2MonthsSheet, $this->owner_id, $this->insurance_company_id, 1, $this->notification_number, 2, $this->filename);
        $parser->parse();
        $parsedResult = array(
            'parsedAgreements' => $parser->parsedAgreementsList,
            'existingAgreements' => $parser->existingAgreementsList,
            'alreadyResumedAgreements' => $parser->alreadyResumedAgreementsList
        );

        return $parsedResult;
    }

    public function parseRefundAgreements()
    {
        $resumeRefundSheet = $this->reader->getSheetByName($this->baseSheetsList[5]);
        if(! $resumeRefundSheet ) {
            return ['error' => 'brak arkusza ZWROT SKŁADKI w importowanym pliku'];
        }

        $parser = new Parsers\RefundAgreementParser($resumeRefundSheet, $this->owner_id, $this->insurance_company_id, $this->notification_number);
        $parser->parse();
        $parsedResult = array(
            'existingAgreementsList' => $parser->existingAgreementsList,
            'unparsedAgreementsList' => $parser->unparsedAgreementsList,
            'alreadyArchivedAgreementsList' => $parser->alreadyArchivedAgreementsList
        );

        return $parsedResult;
    }
}
