<?php

namespace Idea\Gap;


use Log;

class BaseAgreementDocumentParser {

    protected $file;
    protected $filename;
    protected $parameters;
    protected $msg;
    protected $rows;


    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }
    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param string $desc
     * @param string $msg
     * @return bool
     */
    protected function parseFailed($desc = '', $msg = 'Błędna struktura pliku, skontaktuj się z administratorem.')
    {
        $this->msg = $msg;
        Log::alert($msg, array('file' => $this->file, 'desc' => $desc));
        return false;
    }

}
