<?php
namespace Idea\LeasingAgreements;

class Import {

    /**
     * @var
     */
    private $importFactory;

    function __construct(ImportFactory $importFactory)
    {
        $this->importFactory = $importFactory;
    }

    public function parse($filename)
    {
        $this->importFactory->import($filename);
        return $this->importFactory->parse();
    }

}