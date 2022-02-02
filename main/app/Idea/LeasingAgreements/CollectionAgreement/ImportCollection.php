<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 16.02.15
 * Time: 15:51
 */

namespace Idea\LeasingAgreements\CollectionAgreement;


use Idea\LeasingAgreements\BaseImportFactory;
use Idea\LeasingAgreements\ImportFactory;

class ImportCollection{

    private $agreementDocumentParser;

    function import($filename)
    {
        $agreementDocumentParser = new CollectionAgreementDocumentParser($filename);
        $this->agreementDocumentParser = $agreementDocumentParser;
    }

    function parse($limit)
    {
        if($this->agreementDocumentParser->load())
        {
            $parsedRows = $this->agreementDocumentParser->parse_rows($limit);

            if(!$parsedRows) {
                $result['status'] = 'error';
                $result['msg'] = $this->agreementDocumentParser->getMsg();
                return $result;
            }

            $result['status'] = 'success';
            $result['msg'] = 'Przetwarzanie pliku zakończyło się powodzeniem.';
            $result['parsedData'] = $parsedRows;
            return $result;
        }else{
            $result['status'] = 'error';
            $result['msg'] = $this->agreementDocumentParser->getMsg();
            return $result;
        }
    }

}