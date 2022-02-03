<?php  namespace Idea\Structures; 

class CLOSEISSUEInput implements StructuresInterface {

    private $ISSUENUMBER;
    private $CLOSECODE;
    private $USERNAME;

    function __construct($ISSUENUMBER, $CLOSECODE, $USERNAME)
    {
        $this->ISSUENUMBER = $ISSUENUMBER;
        $this->CLOSECODE = $CLOSECODE;
        $this->USERNAME = $USERNAME;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}