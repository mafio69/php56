<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 16.02.15
 * Time: 14:42
 */

namespace Idea\Gap\NewAgreement;


use Idea\Gap\BaseImportFactory;
use Idea\Gap\ImportFactory;

class ImportNewFactory extends BaseImportFactory implements ImportFactory {

    function import($filename)
    {
        $agreementDocumentParser = new NewAgreementDocumentParser($filename);
        $this->agreementDocumentParser = $agreementDocumentParser;
    }


}
