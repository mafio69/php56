<?php

namespace Idea\LeasingAgreements\Reports\Complex;


use Idea\LeasingAgreements\BaseImportFactory;
use Idea\LeasingAgreements\ImportFactory;

class ImportReportsComplexFactory extends BaseImportFactory implements ImportFactory {

    function import($filename)
    {
        $agreementDocumentParser = new ReportComplexDocumentParser();
        $this->agreementDocumentParser = $agreementDocumentParser;
    }
}