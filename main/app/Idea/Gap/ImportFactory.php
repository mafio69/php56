<?php
namespace Idea\Gap;


interface ImportFactory {
    function import($filename);
    function parse($patern);
    function parseTest();

}
