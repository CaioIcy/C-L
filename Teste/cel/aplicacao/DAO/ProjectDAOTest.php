<?php

require_once '/../../../../cel/aplicacao/DAO/ProjectDAO.php';
require_once '/../../../../cel/aplicacao/bd.inc';

class ProjectDAOTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        database_connect();
        $this->setUpProject();
        $this->setUpProjectParticipant();
    }

    protected function setUpProject() {
        $queryProject = "INSERT INTO  projeto (id_projeto,  nome, 
            data_criacao,  descricao,  id_status) VALUES ( 9999,
            'ProjectName', 2013 -11 -25,  'DescProject', 9999 )";
        mysql_query($queryProject);
    }
    
    protected function setUpProjectParticipant(){
        $queryPP = "INSERT INTO participa(id_usuario,
            id_projeto, gerente) VALUES (9999,9999,1)";
        mysql_query($queryPP);
    }

    protected function tearDown() {
        $this->tearDownProject();
        $this->tearDownProjectParticipant();
    }

    protected function tearDownProject() {
        $queryDelProject = "DELETE FROM projeto WHERE id_projeto='9999'";
        mysql_query($queryDelProject);
    }
    
    protected function tearDownProjectParticipant(){
        $queryDelPP = "DELETE FROM participa WHERE id_usuario='9999'";
        mysql_query($queryDelPP);
    }

    public function testGetProjectNameDatabase() {
        $resultProject = getProjectNameDatabase("ProjectName");
        $this->assertEquals("ProjectName", $resultProject[1]);
    }

    public function testGetProjectIdDatabase() {
        $resultId = getProjectIdDatabase(9999, 9999);
        $this->assertEquals(9999, $resultId[0]);
    }
}

?>
