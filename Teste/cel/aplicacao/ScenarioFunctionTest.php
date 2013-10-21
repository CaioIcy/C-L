<?php
require_once dirname(__FILE__) . '/../../../cel/aplicacao/ScenarioFunctions.php';

class test_scenario_function extends PHPUnit_Framework_TestCase{
    
    private $id_project=0;
 
    
    public function test_inclui_cenario() {
        $result = inclui_cenario($this->id_project);
        $this->assertNotNull($result);
    }
}
?>
