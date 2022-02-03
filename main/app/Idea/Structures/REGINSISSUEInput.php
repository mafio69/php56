<?php  namespace Idea\Structures; 

class REGINSISSUEInput implements StructuresInterface {


    /**
     * @var
     */
    private $CONTRACT;
    /**
     * @var
     */
    private $ISSUEDATE;
    /**
     * @var
     */
    private $ISSUENUMBER;
    /**
     * @var
     */
    private $ISSUETYPE;
    /**
     * @var
     */
    private $USERNAME;

    function __construct($CONTRACT, $ISSUEDATE, $ISSUENUMBER, $ISSUETYPE, $USERNAME)
    {
        $this->CONTRACT = $CONTRACT;
        $this->ISSUEDATE = $ISSUEDATE;
        $this->ISSUENUMBER = $ISSUENUMBER;
        $this->ISSUETYPE = $ISSUETYPE;
        $this->USERNAME = $USERNAME;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}
