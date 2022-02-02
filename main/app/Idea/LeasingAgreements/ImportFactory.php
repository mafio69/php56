<?php
namespace Idea\LeasingAgreements;


interface ImportFactory {
    function import($filename);
    function parse();
}