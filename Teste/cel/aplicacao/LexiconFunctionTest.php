<?php

require_once dirname(__FILE__) . '/../../../cel/aplicacao/ScenarioFunctions.php';
require_once dirname(__FILE__) . '/../../../cel/aplicacao/ProjectFunction.php';
require_once dirname(__FILE__) . '/../../../cel/aplicacao/LexiconFunctions.php';

class test_lexicon_function extends PHPUnit_Framework_TestCase{
 
     protected function setUp() {
        includeProject("NOME2", "DESC2");
    }

    function testIncluiLexico() {
        $result = inclui_lexico("NOME2", "DESC2");
        $this->assertFalse($result);
    }

    function testRemoveLexico() {
        $result = removeLexico("2");
        $this->assertTrue($result);
    }
    
}
?>
