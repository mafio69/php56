<?php
namespace Idea\Gap;

class Import {

    /**
     * @var
     */
    private $importFactory;

    function __construct(ImportFactory $importFactory)
    {
        $this->importFactory = $importFactory;
    }

    public function parse($filename,$patern)
    {
        $this->importFactory->import($filename);
        return $this->importFactory->parse($patern);
    }

    public function parseTest($filename)
    {
        $this->importFactory->import($filename);
        return $this->importFactory->parseTest();
    }

    public function getDefaultParsePatern(){
        $this->importFactory->import($filename);
        return $this->importFactory->getDefaultParsePatern();
    }

}
