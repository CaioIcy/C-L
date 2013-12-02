<?php

require_once '/../../../../cel/aplicacao/DAO/ProjectDAO.php';
require_once '/../../../../cel/aplicacao/bd.inc';

class ProjectDAOTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        database_connect();
        $queryProject = "INSERT INTO  projeto (id_projeto,  nome, 
            data_criacao,  descricao,  id_status) VALUES ( 9999,
            'ProjectName', 2013 -11 -25,  'DescProject', 9999 )";
        mysql_query($queryProject);
    }

    protected function tearDown() {
        $queryDelProject = "DELETE FROM projeto WHERE id_projeto='9999'";
        mysql_query($queryDelProject);
    }
    
    public function testGetProjectNameDatabase(){
        $resultProject = getProjectNameDatabase("ProjectName");
        $this->assertEquals("ProjectName",$resultProject[1]);
    }

}

?>
