<?php namespace Idea\Structures;


class GETASSETDTAInput implements StructuresInterface {


    private $CONTRACT;
    private $USERNAME;

    function __construct($CONTRACT, $USERNAME)
    {
        $this->CONTRACT = $CONTRACT;
        $this->USERNAME = $USERNAME;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}
