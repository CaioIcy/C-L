<?php

require_once dirname(__FILE__) . '/../../../cel/aplicacao/ProjectFunction.php';

class test_project_function extends PHPUnit_Framework_TestCase{
    
    function testIncluiProjeto(){
        $result = inclui_projeto("NOME", "DESC");
        $this->assertEquals(1,$result);
    }
    
}
?>
