<?php  namespace Idea\Structures;

class ADDISSUEFEEInput implements StructuresInterface {

    private $ISSUENUMBER;
    private $FEEAMOUNT;
    private $USERNAME;

    function __construct($ISSUENUMBER, $FEEAMOUNT, $USERNAME)
    {
        $this->ISSUENUMBER = $ISSUENUMBER;
        $this->FEEAMOUNT = $FEEAMOUNT;
        $this->USERNAME = $USERNAME;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}