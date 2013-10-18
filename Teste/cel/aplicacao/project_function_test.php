<?php

require_once dirname(__FILE__) . '/../../../cel/aplicacao/ProjectFunction.php';

class test_project_function extends PHPUnit_Framework_TestCase{
    
    function test_inclui_projeto(){
        $result = inclui_projeto("NOME1", "DESC");
        $this->assertEquals(1,$result);
    }
    
}
?>
