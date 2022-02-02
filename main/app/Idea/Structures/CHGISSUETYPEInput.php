<?php  namespace Idea\Structures; 

class CHGISSUETYPEInput implements StructuresInterface{

    private $ISSUENUMBER;
    private $ISSUETYPE;
    private $USERNAME;

    function __construct($ISSUENUMBER, $ISSUETYPE, $USERNAME)
    {
        $this->ISSUENUMBER = $ISSUENUMBER;
        $this->ISSUETYPE = $ISSUETYPE;
        $this->USERNAME = $USERNAME;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}