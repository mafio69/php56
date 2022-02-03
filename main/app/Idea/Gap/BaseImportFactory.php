<?php

namespace Idea\Gap;


class BaseImportFactory {

    protected $agreementDocumentParser;

    function parse($patern)
    {
        if($this->agreementDocumentParser->load())
        {
            $this->agreementDocumentParser->setPatern($patern);

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

    function parseTest()
    {
        if($this->agreementDocumentParser->load())
        {
            $this->agreementDocumentParser->setPatern();

            $parsedRows = $this->agreementDocumentParser->parse_rows(true);

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

    function getDefaultParsePatern(){
      return $this->agreementDocumentParser->getDefaultParsePatern();
    }

    function setPatern($patern){
      return $this->agreementDocumentParser->setPatern($patern);
    }
}
