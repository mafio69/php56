<?php namespace Idea\Structures;


class CHKCONTSTATEInput implements StructuresInterface {


    /**
     * @var
     */
    private $CONTLIST;


    function __construct($contractList)
    {
        $this->CONTLIST = $contractList;
    }

    public function getStructure()
    {
        return get_object_vars($this);
    }
}
