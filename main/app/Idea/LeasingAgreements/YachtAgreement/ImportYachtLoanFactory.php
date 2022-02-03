<?php 
namespace Idea\LeasingAgreements\YachtAgreement;


use Idea\LeasingAgreements\BaseImportFactory;
use Idea\LeasingAgreements\ImportFactory;

class ImportYachtLoanFactory extends BaseImportFactory implements ImportFactory{

    function import($filename)
    {
        $agreementDocumentParser = new YachtLoanDocumentParser($filename);
        $this->agreementDocumentParser = $agreementDocumentParser;
    }

}