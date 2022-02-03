<?php

namespace Idea\Logging\LeasingAgreements\Translations;


class BaseTranslator {

    protected $translations = array();

    public function translate($key, $values)
    {
        return $this->$key($key, $values);
    }

    public function __call($key, $values)
    {
        if(isset($this->translations[$key]))
            return [$this->translations[$key] => $values[1]] ;

        return null;
    }
}