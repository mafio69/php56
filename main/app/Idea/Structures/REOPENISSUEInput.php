<?php  namespace Idea\Structures; 

class REOPENISSUEInput implements StructuresInterface {

    private $ISSUENUMBER;
    private $COMMENT;
    private $USERNAME;

    function __construct($ISSUENUMBER, $COMMENT, $USERNAME)
    {
        $this->COMMENT = $COMMENT;
        $this->ISSUENUMBER = $ISSUENUMBER;
        $this->USERNAME = $USERNAME;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}