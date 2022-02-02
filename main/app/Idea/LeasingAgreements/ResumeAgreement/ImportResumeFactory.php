<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 18.02.15
 * Time: 12:07
 */

namespace Idea\LeasingAgreements\ResumeAgreement;


use Idea\LeasingAgreements\BaseImportFactory;
use Idea\LeasingAgreements\ImportFactory;

class ImportResumeFactory extends BaseImportFactory implements ImportFactory {

    function import($filename)
    {
        $this->agreementDocumentParser = new ResumeAgreementDocumentParser($filename);
    }

}