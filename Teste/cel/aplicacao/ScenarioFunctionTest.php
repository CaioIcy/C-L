<?php
require_once dirname(__FILE__) . '/../../../cel/aplicacao/ScenarioFunctions.php';
require_once dirname(__FILE__) . '/../../../cel/aplicacao/ProjectFunction.php';

class test_scenario_function extends PHPUnit_Framework_TestCase{
    
      private $id_project=NULL;
    
    protected function setUp() {
        $_SESSION['id_current_user'] = "nivi";
        $this->id_project = includeProject("NOME2", "DESC2");
    }
    
    public function testIncluiCenario() {
        $result = inclui_cenario($this->id_project);
        $this->assertEmpty($result);
    }
    public function testAdicionarCenario() {
        $result = adicionar_cenario($this->id_project);
        $this->assertEmpty($result);
    }
        
    public function testRemoveCenario() {
        $result = removeProject($this->id_project);
        $this->assertTrue($result);
        
    }
}
?>
