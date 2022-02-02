<?php
/**
 * Created by PhpStorm.
 * User: przemek
 * Date: 16.02.15
 * Time: 14:41
 */

namespace Idea\Gap;


interface AgreementDocumentParserInterface {
    function load();
    function parse_rows();
    function getMsg();
}
