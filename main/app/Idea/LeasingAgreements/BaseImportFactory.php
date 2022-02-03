<?php

namespace Idea\LeasingAgreements;


class BaseImportFactory {

    protected $agreementDocumentParser;

    function parse()
    {
        if($this->agreementDocumentParser->load())
        {
            $parsedRows = $this->agreementDocumentParser->parse_rows();

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