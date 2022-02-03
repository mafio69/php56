<?php

namespace Idea\LeasingAgreements\YachtAgreement;


use Idea\LeasingAgreements\BaseImportFactory;
use Idea\LeasingAgreements\ImportFactory;

class ImportYachtLeasingFactory extends BaseImportFactory implements ImportFactory {

    function import($filename)
    {
        $agreementDocumentParser = new YachtLeasingDocumentParser($filename);
        $this->agreementDocumentParser = $agreementDocumentParser;
    }
}