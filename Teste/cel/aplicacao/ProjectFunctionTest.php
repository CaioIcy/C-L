<?php

require_once dirname(__FILE__) . '/../../../cel/aplicacao/ProjectFunction.php';

class test_project_function extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $_SESSION['id_usuario_corrente'] = 3;
        inclui_projeto("NOME1", "DESC1");
    }

    protected function tearDown() {
        $query = "DELETE FROM projeto WHERE nome='NOME1'";
        mysql_query($query);
    }

    function testIncluiProjetoNotNull() {
        $result = inclui_projeto("NOME1", "DESC1");
        $this->assertNotNull($result);
    }

    function testRemoveProject() {
        $query = "SELECT id_projeto FROM projeto WHERE nome = 'NOME1'";
        $idProject = mysql_query($query);
        removeProjeto($idProject);
        $result = mysql_query($query);
        $this->assertNull($result);
    }

}

?>
