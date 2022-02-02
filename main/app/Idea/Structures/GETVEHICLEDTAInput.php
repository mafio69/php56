<?php namespace Idea\Structures;


class GETVEHICLEDTAInput implements StructuresInterface {


    /**
     * @var
     */
    private $CONTRACT;
    /**
     * @var
     */
    private $REGNUMBER;
    /**
     * @var
     */
    private $USERNAME;

    function __construct($CONTRACT, $REGNUMBER, $USERNAME)
    {
        $this->CONTRACT = $CONTRACT;
        $this->REGNUMBER = $REGNUMBER;
        $this->USERNAME = $USERNAME;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}
