<?php

require_once dirname(__FILE__) . '/../../../cel/aplicacao/ProjectFunction.php';

class test_project_function extends PHPUnit_Framework_TestCase {

    protected function setUpProjectInDatabase() {
        includeProject("NOME1", "DESC2");
    }

    protected function tearDownProjectInDatabase() {
        removeProjeto("NOME1");
    }

    function testIncluiProjeto() {
        $this->setUpProjectInDatabase();
        $result = inclui_projeto("NOME2", "DESC2");
        $this->assertFalse($result);
        $this->tearDownProjectInDatabase();
    }

    function testRemoveProject() {
        $result = removeProject("2");
        $this->assertTrue($result);
    }

}

?>
